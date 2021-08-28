<?php

class ChangeMPIN{
    
    //declarations
    private $_data;
    private $_fields = array();
    private $_requiredFields = ['mobile', "mpin", 'token'];
    private $_mobile;
    const key = 'MySecretKeyForEncryptionAndDecry'; // 32 chars
    const iv = 'helloworldhellow'; // 16 chars
    const method = 'aes-256-cbc';
    
	public function __construct($data){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->_data = $data;
            $this->_dataChecker();
        }else{
            $retCode = "01";
            $message = "method not allowed";
            $this->_returnResponse($retCode, $message, null);
        }
    }

    //handles returning the response
    private function _returnResponse($retCode, $message, $data){
        header("HTTP/1.1 200 Success server connection ");
        header("Content-Type: application/json");
        $response = array();
        $response['retCode'] = $retCode;
        $response['message'] = $message;
        if(!is_null($data)) $response['data'] = $data;
        echo json_encode($response);
    }
    
    private function decrypt($text){
      return openssl_decrypt($text, self::method, self::key, 0, self::iv);
    }
    
    private function encrypt($text){
      return openssl_encrypt($text, self::method, self::key, 0, self::iv);
    }
    
    private function _dataChecker(){
        if($this->_data == 'data'){
            $retCode = "01";
            $message = "Required fields are missing or no value";
            $data = $this->_requiredFields;
            $this->_returnResponse($retCode, $message, $data);
        }else{
            foreach($this->_data as $key => $val) {
                array_push($this->_fields, $key);
            }
            sort($this->_fields);
            sort($this->_requiredFields);
            if (array_diff($this->_requiredFields, $this->_fields)){
                $retCode = "01";
                $message = "Required fields are missing or no value";
                $data = $this->_requiredFields;
                $this->_returnResponse($retCode, $message, $data);
            }else{
                $completeValues = 1;
                $missingFields = array();
                for($i = 0; $i < count($this->_requiredFields); $i++){
                    if(empty($this->_data[$this->_requiredFields[$i]])){
                        $completeValues = 0;
                        array_push($missingFields, $this->_requiredFields[$i]);
                    }else{
                        // do nothing;
                    }
                }
                if ($completeValues == 1){
                    $phoneAccountTable = new PhoneAccountTable();
                    $result = $phoneAccountTable->search($this->encrypt($this->_data['mobile']));
                    $this->_mobile = $this->_data['mobile'];
                    if ($result === false){
                        $retCode = "01";
                        $message = "Failed to connect to database.";
                        $this->_returnResponse($retCode, $message, null);
                    }else if (count($result) > 0){
                        
                        $token = new MPINToken();
                        $access = $token->search($this->encrypt($this->_data['mobile']), $this->_data['token']);
                        if (count($access) > 0){
                            $date1 = new DateTime($access['dateCreated']);
                            $date2 = new DateTime(date("Y-m-d H:i:s"));
                            $diff_mins = abs($date1->getTimestamp() - $date2->getTimestamp()) / 60;
                            if ($diff_mins < 15){
                                $mpin = new MobileMPIN();
                                $response = $mpin->update($result['accountID'],
                                password_hash($this->_data['mpin'], PASSWORD_DEFAULT));
                                if ($response == 1){
                                    $retCode = "00";
                                    $message = "Success changing MPIN.";
                                    $this->_returnResponse($retCode, $message, null);
                                }else{
                                    $retCode = "01";
                                    $message = "Failed changing MPIN.";
                                    $this->_returnResponse($retCode, $message, null);
                                }
                            }else{
                                $retCode = "01";
                                $message = "Failed changing MPIN. Expired access.";
                                $this->_returnResponse($retCode, $message, null);
                            }
                        }else{
                            $retCode = "01";
                            $message = "Failed to change MPIN. Incorrect access.";
                            $this->_returnResponse($retCode, $message, null);
                        }
                    }else{
                        $retCode = "01";
                        $message = "Mobile not found.";
                        $this->_returnResponse($retCode, $message, null);
                    }
                }else{
                    $retCode = "01";
                    $message = "Required fields are missing or no value";
                    $data = $missingFields;
                    $this->_returnResponse($retCode, $message, $data);
                }
            }
        }
    }
}

?>
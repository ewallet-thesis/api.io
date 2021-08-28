<?php

class ValidateAnswers{
    
    //declarations
    private $_data;
    private $_fields = array();
    private $_requiredFields = ['mobile', 'questionID1', 'answer1', 'questionID2', 'answer2', 'questionID3', 'answer3'];
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
                    $rel = new RelationalQueries();
                    $response = $rel->getAnswers($this->encrypt($this->_data['mobile']));
                    $hasIncorrect = 0;
                    $cnt = 1;
                    if ($response === false){
                        $retCode = "01";
                        $message = "Failed requesting data to server. Please try again later.";
                        $this->_returnResponse($retCode, $message, null);
                    }else{
                        if ($response !== null){
                            while($row = mysqli_fetch_assoc($response)){
                                $questionID = 'questionID'.$cnt;
                                $answerID = 'answer'.$cnt;
                                if ($row['questionID'] == $this->_data[$questionID]){
                                    if ($row['answer'] != $this->_data[$answerID]){
                                        $hasIncorrect = 1;
                                    }
                                }
                                $cnt ++;
                            }
                            if ($hasIncorrect == 0){
                                $token = $this->encrypt($this->_data['mobile']) . $this->encrypt(date("Y-m-d H:i:s"));
                                $mpinToken = new MPINToken();
                                $response = $mpinToken->insert(base64_encode($token), $this->encrypt($this->_data['mobile']));
                                    $data['token'] = base64_encode($token);
                                if ($response == 1){
                                    $retCode = "00";
                                    $message = "All answers are correct.";
                                    $this->_returnResponse($retCode, $message, $data);
                                }else{
                                    $retCode = "01";
                                    $message = "Failed requesting access for change MPIN at the moment. Please contact our CSR.";
                                    $this->_returnResponse($retCode, $message);
                                }
                            }else{
                                $retCode = "01";
                                $message = "You have an incorrect answer. Please try again.";
                                $this->_returnResponse($retCode, $message, null);
                            }
                        }else{
                            $retCode = "01";
                            $message = "Mobile number is not valid.";
                            $this->_returnResponse($retCode, $message, null);
                        }
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
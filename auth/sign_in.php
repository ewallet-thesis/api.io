<?php

    class SignIn
    {
        private $_requiredFields = ['mobile','mpin'];
        private $_fields = array();
        private $_data;
        private $_username;
        private $_passwrod;
        
        const key = 'MySecretKeyForEncryptionAndDecry'; // 32 chars
        const iv = 'helloworldhellow'; // 16 chars
        const method = 'aes-256-cbc';
        
        public function __construct($data){
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                $this->_data = $data;
                $this->_dataChecker($data);
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
            if($data != null)$response['data'] = $data;
            echo json_encode($response);
        }
        
        private function decrypt($text){
          return openssl_decrypt($text, self::method, self::key, 0, self::iv);
        }
        
        private function encrypt($text){
          return openssl_encrypt($text, self::method, self::key, 0, self::iv);
        }
        
        //check all required fields
        private function _dataChecker(){
            if($this->_data == 'data'){
                $retCode = "01";
                $message = "Required fields are missing or no value.";
                $data = $this->_requiredFields;
                $this->_returnResponse($retCode, $message, $data);
            }else{
                foreach($this->_data as $key => $val) {
                    array_push($this->_fields, $key);
                }
                sort($this->_fields);
                sort($this->_requiredFields);
                if (array_diff($this->_fields, $this->_requiredFields)){
                    $diff = array_diff($this->_requiredFields, $this->_fields);
                    $retCode = "01";
                    $message = "Required fields are missing or no value.";
                    $data = $diff;
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
                        $this->_signIn();
                    }else{
                        $retCode = "01";
                        $message = "Required fields are missing or no value.";
                        $data = $missingFields;
                        $this->_returnResponse($retCode, $message, $data);
                    }
                }
            }
        }

        private function _signIn(){
            $rel = new RelationalQueries();
            $response = $rel->phoneAndMPin($this->encrypt($this->_data['mobile']));
            if ($response === false){
                $retCode = "01";
                $message = "Failed requesting data to server. Please try again later.";
                $this->_returnResponse($retCode, $message, null);
            }else if ($response != null){
                while($row = mysqli_fetch_assoc($response)){
                    if(password_verify($this->_data['mpin'], $row['mpin'])){
                        //get data
                        $getUserInfo = $rel->getUserInfo($row['accountID']);
                        if ($getUserInfo != null){
                            $data;
                            while($row = mysqli_fetch_assoc($getUserInfo)){
                                $data['userID'] = $row['userID'];
                                $data['mobile'] = $this->decrypt($row['mobile']);
                                $data['firstname'] = $row['firstname'];
                                $data['middleInitial'] = $row['middleInitial'];
                                $data['lastname'] = $row['lastname'];
                                $data['gender'] = $row['gender'];
                                $data['dateOfBirth'] = $row['dateOfBirth'];
                                $data['maritalStatus'] = $row['maritalStatus'];
                                $data['createdDateTime'] = $row['createdDateTime'];
                            }
                            $answers = $rel->getAnswers($this->encrypt($this->_data['mobile']));
                            if($answers != null){
                                $data['hasSecurityQuestion'] = true;
                            }else{
                                $data['hasSecurityQuestion'] = false;
                            }
                            $getEmails = $rel->getEmails($data['userID']);
                            if ($getEmails != null){
                                $email = array();
                                while($emailRow = mysqli_fetch_assoc($getEmails)){
                                     array_push($email, $emailRow);
                                }
                                $data['email'] = $email;
                                $getAddress = $rel->getAddress($data['userID']);
                                if ($getAddress != null){
                                    $address = array();
                                    while($addressRow = mysqli_fetch_assoc($getAddress)){
                                         array_push($address, $addressRow);
                                    }
                                    $data['address'] = $address;
                                    $getDevices = $rel->getDevices($data['userID']);
                                    if ($getDevices != null){
                                        $devices = array();
                                        while($devicesRow = mysqli_fetch_assoc($getDevices)){
                                             array_push($devices, $devicesRow);
                                        }
                                        $data['devices'] = $devices;
                                        $retCode = "00";
                                        $message = "successfully logged in." ;
                                        $this->_returnResponse($retCode, $message, $data);
                                    }else{
                                    }
                                }else{
                                    $retCode = "015";
                                    $message = "Mobile number or MPIN is incorrect.";
                                    $this->_returnResponse($retCode, $message, null);
                                }
                            }else{
                                $retCode = "014";
                                $message = "Mobile number or MPIN is incorrect.";
                                $this->_returnResponse($retCode, $message, null);
                            }
                        }else{
                            $retCode = "013";
                            $message = "Mobile number or MPIN is incorrect.";
                            $this->_returnResponse($retCode, $message, null);
                        }
                    }else{
                        $retCode = "012";
                        $message = "Mobile number or MPIN is incorrect.";
                        $this->_returnResponse($retCode, $message, null);
                    }
                }
            }else{
                $retCode = "01";
                $message = "Mobile number or MPIN is incorrect.";
                $this->_returnResponse($retCode, $message, null);
            }
        }
    }

?>
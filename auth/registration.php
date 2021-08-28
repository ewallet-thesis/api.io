<?php

class Registration{
    
    //declarations
    private $_data;
    private $_accountID;
    private $_userID;
    private $_fields = array();
    private $_requiredFields = [
        "mpin",
        "mobile",
        "firstname",
        "lastname",
        "gender",
        "dateOfBirth",
        "maritalStatus",
        "model",
        "imei",
        "fullAddress",
        "emailAdd"
    ];
    const key = 'MySecretKeyForEncryptionAndDecry'; // 32 chars
    const iv = 'helloworldhellow'; // 16 chars
    const method = 'aes-256-cbc';
    
    //inital call
    public function __construct($data){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->_data = $data;
            $this->_dataChecker();
        }else{
            $retCode = "01";
            $message = "method not allowed";
            $this->_returnResponse($retCode, $message);
        }
    }
        
    private function decrypt($text){
      return openssl_decrypt($text, self::method, self::key, 0, self::iv);
    }
    
    private function encrypt($text){
      return openssl_encrypt($text, self::method, self::key, 0, self::iv);
    }

    //handles returning the response
    private function _returnResponse($retCode, $message, $data){
        header("HTTP/1.1 200 Success server connection ");
        header("Content-Type: application/json");
        $response = array();
        $response['retCode'] = $retCode;
        $response['message'] = $message;
        $response['data'] = $data;
        echo json_encode($response);
    }
    
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
            if (array_diff($this->_fields, $this->_requiredFields) && count($this->_fields) < count($this->_requiredFields)){
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
                    $phoneAccountTable = new PhoneAccountTable();
                    $result = $phoneAccountTable->search($this->encrypt($this->_data['mobile']));
                    $this->_mobile = $this->_data['mobile'];
                    if ($result === false){
                        $retCode = "01";
                        $message = "Failed requesting data to server. Please try again later.";
                        $this->_returnResponse($retCode, $message, null);
                    }else if (count($result) > 0){
                        $retCode = "01";
                        $message = "Mobile already registered.";
                        $this->_returnResponse($retCode, $message, null);
                    }else{
                        $this->_insertMobile();
                    }
                }else{
                    $retCode = "01";
                    $message = "Required fields are missing or no value.";
                    $data = $missingFields;
                    $this->_returnResponse($retCode, $message, $data);
                }
            }
        }
    }
    
    private function _insertMobile(){
        $phoneAccount = new PhoneAccountTable();
        $result = $phoneAccount->insert($this->encrypt($this->_data['mobile']));
        if ($result == 0){
            $retCode = "01";
            $message = "Failed to register account. Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }else{
            $search = $phoneAccount->search($this->encrypt($this->_data['mobile']));
            // print_r($search);
            $this->_accountID = $search['accountID'];
            $this->_insertMPIN();
        }
    }
    
    private function _insertMPIN(){
        $mpin = new MobileMPIN();
        $response = $mpin->insert($this->_accountID, password_hash($this->_data['mpin'], PASSWORD_DEFAULT));
        if ($response == 1){
            $this->_createSavings();
        }else{
            $retCode = "01";
            $message = "Failed to register account. Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }
    }
    
    private function _createSavings(){
        $savings = new MobileSavings();
        $response = $savings->insert(0.0, 0.0, $this->_accountID);
        if ($response == 1){
            $this->_saveDevice();
        }else{
            $retCode = "01";
            $message = "Account successfully created but failed to register E-Money. Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }
    }
    
    private function _saveDevice(){
        $device = new UserDevices();
        $response = $device->insert($this->_data['model'], $this->_data['imei'], 1, $this->_accountID);
        if($response == 1){
            $this->_saveUserInfo();
        }else{
            $retCode = "01";
            $message = "Account successfully created but failed to remember device. Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }
    }
    
    private function _saveUserInfo(){
        $mInitial = 'N/A';
        if (@$this->_data['middleInitial'] != null) {
            $mInitial = $this->_data['middleInitial'];
        }
        $user = new UserInformation();
        $this->_userID =  $user->insert(
            $this->_data['firstname'],
            $mInitial,
            $this->_data['lastname'],
            $this->_data['gender'],
            $this->_data['dateOfBirth'],
            $this->_data['maritalStatus'],
            $this->_accountID
        );
        if ($this->_userID != 0){
            $this->_saveAddress();
        }else{
            $retCode = "01";
            $message = "Account successfully created but failed to save information . Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }
    }
    
    private function _saveAddress(){
        $userAddress = new UserAddress();
        $response = $userAddress->insert($this->_data['fullAddress'], $this->_userID);
        if ($response == 1){
            $this->_saveEmail();
        }else{
            $retCode = "01";
            $message = "Account successfully created but failed to save information. Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }
    }
    
    private function _saveEmail(){
        $email = new EmailAddress();
        $response = $email->insert($this->_data['emailAdd'], 1, $this->_userID);
        
        if ($response == 1){
            $retCode = "00";
            $message = "Account registration successful.";
            $this->_returnResponse($retCode, $message, null);
        }else{
            $retCode = "01";
            $message = "Account successfully created but failed to save information. Please contact our support team.";
            $this->_returnResponse($retCode, $message, null);
        }
    }
}

?>
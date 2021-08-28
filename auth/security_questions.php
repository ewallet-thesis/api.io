<?php

class SecurityQuestions{
    
    //declarations
    private $_data;
    const key = 'MySecretKeyForEncryptionAndDecry'; // 32 chars
    const iv = 'helloworldhellow'; // 16 chars
    const method = 'aes-256-cbc';
    
    public function __construct(){
        $this->getSecurityQuestions();
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

    private function getSecurityQuestions(){
        $questions = new SecurityQuestionsTable();
        $result = $questions->getSecurityQuestions();
        $data = array();
        while ($row = mysqli_fetch_assoc($result)){
            array_push($data, $row);
        }
        if ($result === false){
            $retCode = "01";
            $message = "Failed requesting data to server. Please try again later.";
            $this->_returnResponse($retCode, $message, null);
        }else{
            $retCode = '00';
            $message = 'Success fetching security questions.';
            $this->_returnResponse($retCode, $message, $data);
        }
    }
}

?>
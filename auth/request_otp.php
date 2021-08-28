<?php

    require_once '../twilio-php-main/src/Twilio/autoload.php'; 
        use Twilio\Rest\Client; 
    class RequestOTP{
        
        private $_contactNumber;
        private $_fields = array();
        private $_requiredFields = ['contactNumber'];
        private $_data;
        
        //inital call
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
            if ($data != null)$response['data'] = $data;
            echo json_encode($response);
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
                    $this->_contactNumber = @$this->_data['contactNumber'];
                    $this->sendOTP();
                }else{
                    $retCode = "01";
                    $message = "Required fields are missing or no value.";
                    $data = $missingFields;
                    $this->_returnResponse($retCode, $message, $data);
                }
            }
        }
    }
        // private function _dataChecker($data){
        //     foreach($data as $key => $val) {
        //         array_push($this->_fields, $key);
        //     }
        //     sort($this->_fields);
        //     sort($this->_requiredFields);
        //     if (($this->_fields) != ($this->_requiredFields)){
        //         // $output = array_merge(array_diff($this->_fields, $this->_requiredFields), 
        //         // array_diff($this->_requiredFields, $this->_fields));
        //         $retCode = "01";
        //         $message = "Requred fields " . str_replace(utf8_encode("\""),"",json_encode($this->_requiredFields));
        //         $this->_returnResponse($retCode, $message);
        //     }else{
        //         $this->_contactNumber = @$data['contactNumber'];
        //         $this->sendOTP();
        //     }
        // }
        
        //generate OTP
        private function generateOTPCode(){
            do {
                $num = sprintf('%06d', mt_rand(100, 999989));
            } while (preg_match("~^(\d)\\1\\1\\1|(\d)\\2\\2\\2$|0000~", $num));
            return $num;
        }
        
        private function saveOTP($code){
            $connection = new Connection();
            $sql = "INSERT INTO otp(otpCode, contactNumber)VALUES('".$code."','".$this->_contactNumber."')";
            // echo $sql;
            $result = mysqli_query($connection->connect(), $sql);
            if ($result){
                if($result > 0){
                    return 1;
                }else{
                    return 0;
                }
            }else{
                return false;
            }
        }
        
        private function sendOTP(){
            $sid    = "ACacd33b6c9ace7ff42cfa6fe3c2502e8c"; 
            $token  = "3ca976d72fa7e7830b805dda4c0a2ead"; 
            $twilio = new Client($sid, $token); 
            
            $code = $this->generateOTPCode();
            $isSuccessSaving = $this->saveOTP($code);
            if ($isSuccessSaving === false){
                $retCode = "01";
                $message = "Failed requesting data to server. Please try again later.";
                $this->_returnResponse($retCode, $message, null);
            }else if ($isSuccessSaving == 1){
                $number;
                if (substr($this->_contactNumber, 0, 1) === '0'){
                    $number = ltrim($this->_contactNumber, '0');
                }else{
                    $number = $this->_contactNumber;
                }
                $twilioMessage = $twilio->messages->create(
                    "+63".$number,
                    array(
                        "messagingServiceSid" => "MGefa56a80f505984063bb93d85908bf91",
                        "body" => "Your OTP is ".$code.". Don't share it with anyone.",
                        "statusCallback" => "https://abc1234.free.beeceptor.com"
                    )
                ); 
     
                if($twilioMessage->status == 'accepted'){
                    $retCode = "00";
                    $message = "OTP Sent.";
                    $this->_returnResponse($retCode, $message, null);
                }else{
                    $retCode = "01";
                    $message = "Failed to send OTP. Please try again later.";
                    $this->_returnResponse($retCode, $message, null);
                }
            }else{
                $retCode = "01";
                $message = "Failed to send OTP. Please try again later.";
                $this->_returnResponse($retCode, $message, null);
            }
        }
    }
?>
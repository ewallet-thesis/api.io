<?php
    class ValidateOTP{
        
        private $_contactNumber;
        private $_otp;
        private $_data;
        private $_fields = array();
        private $_requiredFields = ['contactNumber', 'otp'];
        
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
                    $this->_otp = @$this->_data['otp'];
                    $this->_contactNumber = @$this->_data['contactNumber'];
                    $this->validateOTP();
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
        //         $this->_otp = @$data['otp'];
        //         $this->_contactNumber = @$data['contactNumber'];
        //         $this->validateOTP();
        //     }
        // }
        
        private function validateOTP(){
            $connection = new Connection();
            $sql = 'SELECT * FROM otp WHERE contactNumber = "'.$this->_contactNumber.'" ORDER BY otpID DESC LIMIT 1';
            // $sql = 'SELECT * FROM otp WHERE contactNumber = "'.$this->_contactNumber.'"';
            
            $result = mysqli_query($connection->connect(), $sql);
            if ($result){
                if(mysqli_num_rows($result) > 0){
                    $response = mysqli_fetch_assoc($result);
                    if ($response['otpCode'] == $this->_otp){
                        $date1 = new DateTime($response['postedDateTime']);
                        $date2 = new DateTime(date("Y-m-d H:i:s"));
                        $diff_mins = abs($date1->getTimestamp() - $date2->getTimestamp()) / 60;
                        if ($diff_mins < 15){
                            $retCode = "00";
                            $message = "Valid OTP.";
                            $this->_returnResponse($retCode, $message, null);
                        }else{
                            $retCode = "01";
                            $message = "OTP Expired.";
                            $this->_returnResponse($retCode, $message, null);
                        }
                    }else{
                        $retCode = "01";
                        $message = "OTP is not valid.";
                        $this->_returnResponse($retCode, $message, null);
                    }
                }else{
                    // $retCode = "00";
                    // $message = "Valid OTP.";
                    // $this->_returnResponse($retCode, $message);
                    $retCode = "01";
                    $message = "OTP is not valid.";
                    $this->_returnResponse($retCode, $message, null);
                }
            }else{
                $retCode = "01";
                $message = "Failed requesting data to server. Please try again later.";
                $this->_returnResponse($retCode, $message, null);
            }
        }
    }
?>
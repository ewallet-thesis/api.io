<?php

class FundTransfer{
    
    private $_requiredFields = [
        'trnDescription',
        // 'notes',
        'amount',
        'sourceMobile',
        'targetMobile',
        // 'trnDateTime',
        // 'fee',
        // 'refID',
        'mobileRef'
        ];
    private $_fields = array();
    private $_data;
    private $_refID;
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
            array_push($this->_requiredFields, 'notes(optional)');
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
                array_push($this->_requiredFields, 'notes(optional)');
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
                    if ($this->_data['amount'] > 0){
                        $result = $this->transfer();
                    }else{
                        $retCode = "01";
                        $message = "Amount must be greater than 0.";
                        $this->_returnResponse($retCode, $message, null);
                    }
                }else{
                    $retCode = "01";
                    $message = "Required fields are missing or no value.";
                    array_push($missingFields, 'notes(optional)');
                    $data = $missingFields;
                    $this->_returnResponse($retCode, $message, $data);
                }
            }
        }
    }
    
    public function transfer(){
        $mSavings = new MobileSavings();
        $account = new PhoneAccountTable();
        $sourceID;
        $targetID;
        //checker
        $relational = new RelationalQueries();
        $sourceTarget = $relational->getSourceAndTarget($this->encrypt($this->_data['sourceMobile']), $this->encrypt($this->_data['targetMobile']));
        while($row = mysqli_fetch_assoc($sourceTarget)){
            if ($row['mobile'] == $this->encrypt($this->_data['sourceMobile'])){
                $sourceID = ($row['accountID']);
            }else{
                $targetID=($row['accountID']);
            }
        }
        
        if ($sourceID != null && $targetID != null){
            $sourceSavings;
            $targetSavings;
            $savings = $relational->getSavings($sourceID, $targetID);
            while($row = mysqli_fetch_assoc($savings)){
                if ($row['accountID'] == $sourceID){
                    $sourceSavings = ($row);
                }else{
                    $targetSavings=($row);
                }
            }
            
            $sourceAvailable =  $sourceSavings['availableBalance'] - $this->_data['amount'];
            $sourceTotal =  $sourceSavings['totalBalance'] - $this->_data['amount'];
            $targetAvailable =  $targetSavings['availableBalance'] + $this->_data['amount'];
            $targetTotal =  $targetSavings['totalBalance'] + $this->_data['amount'];
            if ($sourceAvailable >= 0){
                $isTransacted = $mSavings->update($sourceAvailable, $sourceTotal, $sourceID);
                if($isTransacted == 1){
                    $isRecived = $mSavings->update($targetAvailable, $targetTotal, $targetID);
                    if($isRecived == 1){
                        $th = new TransactionHistory();
                        $this->saveTransaction($th);
                        $data = $th->searchRef($this->_refID);
                        $retCode = "00";
                        $message = "Transaction successful.";
                        $this->_returnResponse($retCode, $message, $data);
                    }else{
                        $retCode = "01";
                        $message = "Transaction failed. If balance have been deducted, please contact our CSR.";
                        $this->_returnResponse($retCode, $message, null);
                    }
                }else{
                    $retCode = "01";
                    $message = "Transaction failed.";
                    $this->_returnResponse($retCode, $message, null);
                }
            }else{
                $retCode = "01";
                $message = "Transaction failed. Insufficient funds.";
                $this->_returnResponse($retCode, $message, null);
            }
            
        }else if ($sourceID == null){
            $retCode = "01";
            $message = "Invalid source account.";
            $this->_returnResponse($retCode, $message, null);
        }else{
            $retCode = "01";
            $message = "Invalid target account.";
            $this->_returnResponse($retCode, $message, null);
        }
        
        //end of checker
        
        // $sourceID = $account->search($this->encrypt($this->_data['sourceMobile']));
        // if (!empty(@$sourceID['accountID'])){
        //     sleep(1);
        //     $sourceSavings = $mSavings->search($sourceID['accountID']);
        //     if ($sourceSavings['availableBalance'] > 0){
        //         $target = $this->validate();
        //         if (count($target) > 0){
        //             $targetID = $account->search($this->encrypt($this->_data['targetMobile']));
        //             $targetSavings = $mSavings->search($targetID['accountID']);
        //             $sourceAvailable =  $sourceSavings['availableBalance'] - $this->_data['amount'];
        //             $sourceTotal =  $sourceSavings['totalBalance'] - $this->_data['amount'];
        //             $targetAvailable =  $targetSavings['availableBalance'] + $this->_data['amount'];
        //             $targetTotal =  $targetSavings['totalBalance'] + $this->_data['amount'];
        //             $isTransacted = $mSavings->update($sourceAvailable, $sourceTotal, $sourceID['accountID']);
        //             if($isTransacted == 1){
        //                 $isRecived = $mSavings->update($targetAvailable, $targetTotal, $targetID['accountID']);
        //                 if($isRecived == 1){
        //                     $th = new TransactionHistory();
        //                     $this->saveTransaction($th);
        //                     $data = $th->searchRef($this->_refID);
        //                     $retCode = "01";
        //                     $message = "Transaction successful.";
        //                     $this->_returnResponse($retCode, $message, $data);
        //                 }else{
        //                     $retCode = "01";
        //                     $message = "Transaction failed. If balance have been deducted, please contact our CSR.";
        //                     $this->_returnResponse($retCode, $message, null);
        //                 }
        //             }else{
        //                 $retCode = "01";
        //                 $message = "Transaction failed.";
        //                 $this->_returnResponse($retCode, $message, null);
        //             }
        //         }else{
        //             $retCode = "01";
        //             $message = "Invalid target mobile.";
        //             $this->_returnResponse($retCode, $message, null);
        //         }
        //     }else{
        //         $retCode = "01";
        //         $message = "Insuficient funds.";
        //         $this->_returnResponse($retCode, $message, null);
        //     }
        // }else{
        //     $retCode = "01";
        //     $message = "Invalid source number.";
        //     $this->_returnResponse($retCode, $message, null);
        // }
        
        
        // $mSavings->update($availableBalance, $totalBalance, $accountID);
        // $this->saveTransaction();
    }
    
    private function saveTransaction($th){
        $this->_refID = $this->generateRefID();
        $th->insert($this->_data['trnDescription'], $this->_data['amount'], $this->_data['sourceMobile'], $this->_data['targetMobile'], date("Y-m-d H:i:s"), 0, $this->_refID, $this->_data['mobileRef']);
    }
    
    private function validate(){
        $account = new PhoneAccountTable();
        sleep(.2);
        return $account->search($this->encrypt($this->_data['targetMobile']));
    }
    
    private function generateRefID(){
        return md5(base64_encode(date("Y-m-d H:i:s").$this->_data['sourceMobile']));
    }
    // ($trnDescription, $amount, $sourceMobile, $targetMobile, $trnDateTime, $fee, $refID, $mobileRef)
}

?>
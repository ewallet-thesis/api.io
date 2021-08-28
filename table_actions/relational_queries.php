<?php

class RelationalQueries{
    public function phoneAndMPin($mobile){
        $connection = new Connection();
        $sql = 'SELECT p.mobile, p.accountID, m.mpin
                FROM phone_account AS p
                INNER JOIN mobile_mpin AS m ON m.accountID = p.accountID
                WHERE p.mobile ="'.$mobile.'";
            ';
            
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
        //array
    }
    
    const key = 'MySecretKeyForEncryptionAndDecry'; // 32 chars
    const iv = 'helloworldhellow'; // 16 chars
    const method = 'aes-256-cbc';
    
    private function decrypt($text){
      return openssl_decrypt($text, self::method, self::key, 0, self::iv);
    }
    
    private function encrypt($text){
      return openssl_encrypt($text, self::method, self::key, 0, self::iv);
    }
    
    public function getUserInfo($accountID){
        $connection = new Connection();
        $sql = 'SELECT u.userID, p.mobile, u.firstname, u.middleInitial,
                u.lastname, u.gender, u.dateOfBirth, u.maritalStatus,
                u.createdDateTime
                FROM user_information AS u
                INNER JOIN phone_account AS p ON u.accountID = p.accountID
                WHERE p.accountID ="'.$accountID.'";
            ';
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
        //array
    }
    
    public function getEmails($userID){
        $connection = new Connection();
        $sql = 'SELECT e.emailID, e.emailAdd, e.isPrimary, e.addedDateTime
                FROM email_address AS e
                INNER JOIN user_information AS u ON e.userID = u.userID
                WHERE e.userID ="'.$userID.'";
            ';
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
        //array
    }
    
    public function getAddress($userID){
        $connection = new Connection();
        $sql = 'SELECT a.addressID, a.fullAddress, a.addedDateTime
                FROM user_address AS a
                INNER JOIN user_information AS u ON a.userID = u.userID
                WHERE a.userID ="'.$userID.'";
            ';
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
        //array
    }
    
    public function getDevices($accountID){
        $connection = new Connection();
        $sql = 'SELECT 
                d.deviceID,
                d.model,
                d.imei,
                d.isPrimary,
                d.isBlocked,
                d.addedDateTime
                FROM user_devices AS d
                INNER JOIN phone_account AS p ON p.accountID = d.accountID
                WHERE d.accountID ="'.$accountID.'";
            ';
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
        //array
    }
    
    public function getSourceAndTarget($source, $target){
        $connection = new Connection();
    	$sql = 'SELECT * FROM phone_account WHERE mobile = "'.$source.'" OR mobile = "'.$target.'"';
    	
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
    }
    
    public function getSavings($source, $target){
        $connection = new Connection();
    	$sql = 'SELECT * FROM mobile_savings WHERE accountID = "'.$source.'" OR accountID = "'.$target.'"';
    	
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
    }
    
    public function getHistory($mobile){
        $connection = new Connection();
    	$sql = 'SELECT * FROM transaction_history WHERE sourceMobile = "'.$mobile.'" OR targetMobile = "'.$mobile.'" ORDER BY trnDateTime DESC';
    	
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
    }
    
    public function getBalance($mobile){
        $connection = new Connection();
        $sql = 'SELECT s.savingsID, s.availableBalance, s.totalBalance, s.createdDateTime 
                FROM `phone_account` AS p
                INNER JOIN
                mobile_savings AS s on s.accountID = p.accountID
                WHERE p.mobile = "'.$mobile.'"';
                
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
        // s.accountID,
    }
    
    public function getAnswers($mobile){
        $connection = new Connection();
        $sql ='SELECT * FROM phone_account AS p
            INNER JOIN user_answer AS ua ON ua.accountID = p.accountID
            INNER JOIN security_questions AS sq ON ua.questionID = sq.questionID 
            WHERE p.mobile = "'.$mobile.'"';
                
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
    }
    
    public function deleteAccount($userID, $mobile){
        $connection = new Connection();
        $sql = 'DELETE FROM user_address WHERE userID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM email_address WHERE userID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM user_information WHERE userID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM transaction_history WHERE sourceMobile = "'.$mobile.'" OR targetMobile = "'.$mobile.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM mobile_savings WHERE accountID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM mobile_mpin WHERE accountID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM user_devices WHERE accountID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM phone_account WHERE accountID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM mpin_token WHERE mobile = "'.$this->encrypt($mobile).'";';
        $result = mysqli_query($connection->connect(), $sql);
        $sql = 'DELETE FROM user_answer WHERE accountID = "'.$userID.'";';
        $result = mysqli_query($connection->connect(), $sql);
        
        if(mysqli_num_rows($result) > 0){
            return 1;
        }else{
            return 0;
        }
    }
    
    public function relationalValidateAccount($mobile){
        $connection = new Connection();
        $sql = 'SELECT pa.mobile, ui.firstname, ui.middleInitial, ui.lastname,
            ui.gender, ui.dateOfBirth, ui.accountID, ed.emailID, ed.emailAdd, ms.savingsID,
            ms.availableBalance, ms.totalBalance
            FROM phone_account AS pa
            INNER JOIN user_information as ui ON ui.accountID = pa.accountID
            INNER JOIN email_address as ed ON ui.userID = ed.userID
            INNER JOIN mobile_savings as ms ON ms.accountID = pa.accountID
            WHERE pa.mobile = "'.$mobile.'"';
                
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return $result;
            }else{
                return null;
            }
        }else{
            return false;
        }
    }
}

?>
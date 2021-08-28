
<?php

ini_set('display_errors', '0');
class MPINToken{

    public function insert($token, $mobile){
        $connection = new Connection();
        // $connection->closeConnection();
        $sql = 'INSERT INTO mpin_token(token, mobile) VALUES ("'.$token.'", "'.$mobile.'");';
        
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
        //true or false
    }

    public function search($mobile, $token){
        $connection = new Connection();
        // $connection->closeConnection();
    	$sql = 'SELECT * FROM mpin_token WHERE mobile = "'.$mobile.'" AND token = "'.$token.'"';
    	
        $result = mysqli_query($connection->connect(), $sql);
        // echo $sql;
        $toReturn = array();
        if ($result){
            if(mysqli_num_rows($result) > 0){
                $toReturn = mysqli_fetch_array($result);
                return $toReturn;
            }else{
                return $toReturn;
            }
        }else{
            return false;
        }
    	//array
    }

    public function update($availableBalance, $totalBalance, $accountID){
        $connection = new Connection();
        // $connection->closeConnection();
    	$sql = 'UPDATE mobile_savings SET availableBalance = "'.$availableBalance.'", totalBalance = "'.$totalBalance.'" WHERE accountID = "'.$accountID.'"';
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
    	//true or false
    }

}

?>
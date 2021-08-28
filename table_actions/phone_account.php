<?php

class PhoneAccountTable{

    public function insert($mobile){
        $connection = new Connection();
        // $connection->closeConnection();
        $sql = 'INSERT INTO phone_account(mobile) VALUES ("'.$mobile.'");';
        
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

    public function search($mobile){
        $connection = new Connection();
        // $connection->closeConnection();
    	$sql = 'SELECT * FROM phone_account WHERE mobile = "'.$mobile.'"';
    // 	echo $sql;
        $result = mysqli_query($connection->connect(), $sql);
        // echo $sql;
        $returnValue = array();
        if ($result){
            if(mysqli_num_rows($result) > 0){
                $returnValue = mysqli_fetch_assoc($result);
                return $returnValue;
            }else{
                return $returnValue;
            }
        }else{
            return false;
        }
    	//array
    }

    public function update($mobile){
    	$sql = 'UPDATE phone_account SET mobile = "'.$mobile.'" WHERE mobile = "'.$mobile.'"';
    	//true or false
    }

}

?>
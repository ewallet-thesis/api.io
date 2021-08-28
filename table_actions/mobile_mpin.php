<?php

class MobileMPIN{

    public function insert($accountID, $mpin){
        $connection = new Connection();
        // $connection->closeConnection();
        $sql = 'INSERT INTO mobile_mpin(mpin, accountID) VALUES ("'.$mpin.'","'.$accountID.'");';
        
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

    public function search($accountID){
    	$sql = 'SELECT * FROM mobile_mpin WHERE accountID = "'.$accountID.'"';
    	//array
    }

    public function update($accountID, $mpin){
        $connection = new Connection();
        // $connection->closeConnection();
    	$sql = 'UPDATE mobile_mpin SET mpin = "'.$mpin.'" WHERE accountID = "'.$accountID.'"';
        
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
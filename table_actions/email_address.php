<?php

class EmailAddress{

    public function insert($emailAdd, $isPrimary, $userID){
        $connection = new Connection();
        // $connection->closeConnection();
        $sql = 'INSERT INTO email_address(emailAdd, isPrimary, userID) VALUES ("'.$emailAdd.'", "'.$isPrimary.'", "'.$userID.'");';
        
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

    public function search($email){
        $connection = new Connection();
        // $connection->closeConnection();
    	$sql = 'SELECT * FROM email_address WHERE emailAdd = "'.$email.'"';
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

    public function update($emailAdd, $isPrimary, $userID){
    	$sql = 'UPDATE email_address SET 
    			emailAdd = "'.$emailAdd.'", isPrimary = "'.$isPrimary.'"
    			WHERE userID = "'.$userID.'"';
    	//true or false
    }

}

?>

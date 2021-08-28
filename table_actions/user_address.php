<?php

class UserAddress{

    public function insert($fullAddress, $userID){
        $connection = new Connection();
        $sql = 'INSERT INTO user_address(fullAddress, userID) VALUES ("'.$fullAddress.'", "'.$userID.'");';
        
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

    public function search($userID){
    	$sql = 'SELECT * FROM user_address WHERE userID = "'.$userID.'"';
    	//array
    }

    public function update($fullAddress, $userID){
    	$sql = 'UPDATE user_address SET 
    			fullAddress = "'.$fullAddress.'"
    			WHERE userID = "'.$userID.'"';
    	//true or false
    }

}

?>

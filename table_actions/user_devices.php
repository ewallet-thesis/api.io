<?php

class UserDevices{

    public function insert($model, $imei, $isPrimary, $accountID){
        $connection = new Connection();
        $sql = '
        SET  @isActive = 	(select case when 
            count(distinct accountID) 
            then 0
            else 1
            end
            from user_devices
            where accountID = "'.$accountID.'" AND isPrimary = 1);

        INSERT INTO user_devices(model, imei, isPrimary, accountID)
    	VALUES(
            "'.$model.'",
            "'.$imei.'",
            "'.@isActive.'",
            "'.$accountID.'"
        )';
        
        $sql = 'INSERT INTO user_devices(model, imei, isPrimary, accountID) VALUES ("'.$model.'", "'.$imei.'", "'.$isPrimary.'", "'.$accountID.'");';
        
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
    	$sql = 'SELECT * FROM user_devices WHERE accountID = "'.$accountID.'"';
    	//array
    }

    public function updateIsPrimary($deviceID, $isPrimary, $accountID){
    	$sql = 'UPDATE user_devices SET isPrimary = "'.$isPrimary.'" WHERE accountID = "'.$accountID.'" AND deviceID = "'.$deviceID.'"';
    	//true or false
    }

    public function updateIsBlocked($deviceID, $isBlocked, $accountID){
    	$sql = 'UPDATE user_devices SET isBlocked = "'.$isBlocked.'" WHERE accountID = "'.$accountID.'" AND deviceID = "'.$deviceID.'"';
    	//true or false
    }

}

?>
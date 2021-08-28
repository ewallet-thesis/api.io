<?php

class UserInformation{

    public function insert($firstname, $middleInitial, $lastname, $gender, $dateOfBirth, $maritalStatus, $accountID){
        $connection = new Connection();
        $sql = 'INSERT INTO user_information(
        			firstname, middleInitial, lastname, gender, dateOfBirth, maritalStatus, accountID
    			) VALUES (
    				"'.$firstname.'",
    				"'.$middleInitial.'",
    				"'.$lastname.'",
    				"'.$gender.'",
    				STR_TO_DATE("'.$dateOfBirth.'", "%d-%m-%Y"),
    				"'.$maritalStatus.'",
    				"'.$accountID.'"
    			);';
        
        $result = mysqli_query($connection->connect(), $sql);
        // echo $sql;
        if ($result){
            if($result > 0){
                $reponse = $this->search($accountID);
                return $reponse['userID'];
            }else{
                return 0;
            }
        }else{
            return false;
        }
        //true or false
    }

    public function search($accountID){
        $connection = new Connection();
    	$sql = 'SELECT * FROM user_information WHERE accountID = "'.$accountID.'"';

        $result = mysqli_query($connection->connect(), $sql);
        $returnedData = array();
        if ($result){
            if(mysqli_num_rows($result) > 0){
                $returnedData = mysqli_fetch_assoc($result);
                return $returnedData;
            }else{
                return $returnedData;
            }
        }else{
            return false;
        }
    	//array
    }

    public function update($firstname, $middleInitial, $lastname, $gender, $dateOfBirth, $maritalStatus, $accountID){
    	$sql = 'UPDATE user_information SET 
    			firstname = "'.$firstname.'",
    			middleInitial = "'.$middleInitial.'",
    			lastname = "'.$lastname.'",
    			gender = "'.$gender.'",
    			dateOfBirth = "'.$dateOfBirth.'",
    			maritalStatus = "'.$maritalStatus.'"
    			WHERE accountID = "'.$accountID.'"';
    	//true or false
    }

}

?>

<?php

class MobileSavings{

    public function insert($availableBalance, $totalBalance, $accountID){
        $connection = new Connection();
        // $connection->closeConnection();
        $sql = 'INSERT INTO mobile_savings(availableBalance, totalBalance, accountID) VALUES ("100", "100", "'.$accountID.'");';
        
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
        $connection = new Connection();
        // $connection->closeConnection();
    	$sql = 'SELECT * FROM mobile_savings WHERE accountID = "'.$accountID.'"';
    	
        $result = mysqli_query($connection->connect(), $sql);
        // echo $sql;
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return mysqli_fetch_array($result);
            }else{
                return null;
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
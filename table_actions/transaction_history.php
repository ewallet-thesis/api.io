<?php

class TransactionHistory{

    public function insert($trnDescription, $amount, $sourceMobile, $targetMobile, $trnDateTime, $fee, $refID, $mobileRef){
        $connection = new Connection();
        $sql = 'INSERT INTO transaction_history(
        		trnDescription, amount, sourceMobile, targetMobile, trnDateTime, fee, refID, mobileRef
    		) VALUES (
    			"'.$trnDescription.'",
    			"'.$amount.'",
    			"'.$sourceMobile.'",
    			"'.$targetMobile.'",
    			"'.$trnDateTime.'",
    			"'.$fee.'",
    			"'.$refID.'",
    			"'.$mobileRef.'"
    		);';
    		
		$result = mysqli_query($connection->connect(), $sql);
// 		echo $sql;
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

    public function search($sourceMobile, $targetMobile){
    	$sql = 'SELECT * FROM transaction_history WHERE sourceMobile = "'.$sourceMobile.'" AND targetMobile = "'.$targetMobile.'"';
    	//array
    }

    public function searchRef($refID){
        $connection = new Connection();
    	$sql = 'SELECT * FROM transaction_history WHERE refID = "'.$refID.'"';
    	
		$result = mysqli_query($connection->connect(), $sql);
// 		echo $sql;
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return mysqli_fetch_assoc($result);
            }else{
                return null;
            }
        }else{
            return false;
        }
    	//array
    }

}

?>

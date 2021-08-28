<?php

class SaveAnswer{
    public function insert($accountID, $questionID1, $answer1, $questionID2, $answer2, $questionID3, $answer3){
        $connection = new Connection();
        $sql = 'INSERT INTO user_answer(answer, accountID, questionID)
            VALUES
            ("'.$answer1.'","'.$accountID.'", "'.$questionID1.'"),
            ("'.$answer2.'","'.$accountID.'", "'.$questionID2.'"),
            ("'.$answer3.'","'.$accountID.'", "'.$questionID3.'")';
                
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
    }
}

?>
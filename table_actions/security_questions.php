<?php

class SecurityQuestionsTable{
    public function getSecurityQuestions(){
        $connection = new Connection();
        $sql = 'SELECT * FROM security_questions';
                
        $result = mysqli_query($connection->connect(), $sql);
        if ($result){
            if(mysqli_num_rows($result) > 0){
                return ($result);
            }else{
                return null;
            }
        }else{
            return false;
        }
    }
}

?>
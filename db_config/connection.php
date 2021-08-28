<?php

    class Connection{
        public function connect(){
            $dbName = "bgqajax1is366u2slb60";
            $dbUsername = "uoajfxcybmov9l6p";
            $dbPassword = "75EIVDijU7ySottNtREl";
            $dbServer = "bgqajax1is366u2slb60-mysql.services.clever-cloud.com";
            // $dbName = "id17225681_mobilemoney";
            // $dbUsername = "id17225681_mobilemoney1";
            // $dbPassword = "m0B1l3M0n3y-123";
            // $dbServer = "localhost";
            
            try{
                $connection = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);
                
                if (!mysqli_connect_errno()){
                    // echo "connection success";
                    return $connection;
                }else{
                    // echo "failed to connect";
                    return null;
                }
            } catch( Exception $e ){
                return null;
            } 
            // finally {
            //     return null;
            // }
        }
        
        public function closeConnection(){
            mysqli_close($this->connect());
        }
    }
    

?>
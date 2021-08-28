<?php

    //headers
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: false");
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: *');
    header("HTTP/1.1 200 Success ");
    header("Content-Type: application/json");
    
    //accept data
    $JSONData = file_get_contents('php://input');
    $inputs = json_decode($JSONData, TRUE);
    
    //include files
    include "route.php";
    include "db_config/connection.php";
    include "common_components.php";
    include "auth/registration.php";
    include "auth/inquire_mobile.php";
    include "auth/inquire_email.php";
    include "auth/sign_in.php";
    // include "auth/request_otp.php";
    include "auth/validate_otp.php";
    include "auth/save_answers.php";
    include "auth/security_questions.php";
    include "auth/validate_answer.php";
    include "auth/change_mpin.php";
    include "auth/delete_account.php";
    include "table_actions/phone_account.php";
    include "table_actions/email_address.php";
    include "table_actions/mobile_savings.php";
    include "table_actions/transaction_history.php";
    include "table_actions/user_address.php";
    include "table_actions/user_devices.php";
    include "table_actions/user_information.php";
    include "table_actions/mobile_mpin.php";
    include "table_actions/relational_queries.php";
    include "table_actions/security_questions.php";
    include "table_actions/save_answer.php";
    include "table_actions/mpin_token.php";
    include "transactions/validate_account.php";
    include "transactions/fund_transfer.php"; 
    include "transactions/transaction_history.php";
    include "transactions/get_balance.php";
    include "transactions/validate_account_replicated.php";
    
    $route = new Route();
    
    $route->add('/', function(){
        header("HTTP/1.1 200 Success server connection ");
        header("Content-Type: application/json");
        $routes = ['/mobileWallet/signIn', '/mobileWallet/saveAnswers', '/mobileWallet/validateAnswers', '/mobileWallet/registration', '/mobileWallet/getSecurityQuestions','/mobileWallet/inquireMobile','/mobileWallet/inquireEmail','/mobileWallet/requestOTP','/mobileWallet/validateOTP', '/mobileWallet/validateAccount','/mobileWallet/getBalance', '/mobileWallet/FT','/mobileWallet/transactionHistory','/mobileWallet/changeMPIN', '/mobileWallet/deleteAccount'];
        $response = array();
        $response['retCode'] = "00";
        $response['message'] = "Welcome to E-Wallet Open API. See available routes bellow, you can also click to view and test the given routes.";
        $response['routes'] = $routes;
        echo json_encode($response);
    }, null);
    $route->add('/inquireMobile', 'InquireMobile');
    $route->add('/registration', 'Registration', $inputs);
    $route->add('/signIn', 'SignIn', $inputs);
    $route->add('/requestOTP', 'RequestOTP', $inputs);
    $route->add('/validateOTP', 'ValidateOTP', $inputs);
    // $route->add('/validateAccount', 'ValidateAccount', $inputs);
    $route->add('/validateAccount', 'ValidateAccountReplicated', $inputs);
    $route->add('/FT', 'FundTransfer', $inputs);
    $route->add('/transactionHistory', 'AccountTransactions', $inputs);
    $route->add('/getBalance', 'GetBalance', $inputs);
    $route->add('/getSecurityQuestions', 'SecurityQuestions', $inputs);
    $route->add('/saveAnswers', 'SaveAnswers', $inputs);
    $route->add('/validateAnswers', 'ValidateAnswers', $inputs);
    $route->add('/changeMPIN', 'ChangeMPIN', $inputs);
    $route->add('/inquireEmail', 'InquireEmail', $inputs);
    $route->add('/deleteAccount', 'DeleteAccount', $inputs);
    
    $route->submit();

?>
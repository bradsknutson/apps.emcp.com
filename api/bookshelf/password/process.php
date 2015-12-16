<?php

    $password = $_POST['password'];
    $email = $_POST['email'];

    include '../inc/functions.php';

    $code = $activation_code = $_POST['code'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    $linkAccountJson = callAPI($linkAccountURL, $linkAccountVars);
    $linkAccountData = json_decode($linkAccountJson, true);

    $addBookVars = array(
        'activation_code' => $code,
        'application_name' => 'SFDC',
        'account_id' => $account_id,    
    );

    if( $linkAccountData['errno'] ) {
        if( $linkAccountData['errno'] == 14 || $linkAccountData['errno'] == 15 ) {
            header('Location: ../password/?code='. $code .'&email='. $email .'&fname='. $fname .'&lname='. $lname .'&badpass=true');
            exit;
        } else {
            // Unhandled error
            echo $linkAccountJson;
        }
    } else {
        $addBookJson = callAPI($addBookURL, $addBookVars);
        $addBookData = json_decode($addBookJson, true);
        if( $addBookData['errno'] ) {
            // Unhandled error
            echo $addBookJson;
        } else {
            header('Location: ../success/?code='. $code .'&email='. $email);
            exit;
        }
    }

?>
<?php

    $password = $_POST['password'];

    include '../inc/functions.php';

    $email = $_POST['email'];
    $campaign_id = $_POST['campaign_id'];
    $account_id = getID($email, $mysqli);
    $campaign_vars = getCampaignVars($email, $campaign_id, $mysqli);

    $group_id = $campaign_vars[0]['group_id'];
    $duration = $campaign_vars[0]['duration'];

    $linkAccountVars = array(
        'application_name' => 'SFDC',
        'account_id' => $account_id,
        'username' => $email,
        'password' => $password,
    );

    $linkAccountJson = callAPI($linkAccountURL, $linkAccountVars);
    $linkAccountData = json_decode($linkAccountJson, true);

    $addBookUserVars = array(
        'group_id' => $group_id,
        'account_id' => $account_id,
        'application_name' => 'SFDC',
        'duration' => $duration,    
    );

    $addBookUserJson = callAPI($addBookUserURL, $addBookUserVars);
    $addBookUserData = json_decode($addBookUserJson, true);

    echo $addBookUserJson;

    if( $linkAccountData['errno'] ) {
        if( $linkAccountData['errno'] == 14 || $linkAccountData['errno'] == 15 ) {
            header('Location: ../password/?email='. urlencode($email) .'&campaign_id='. $campaign_id .'&badpass=true');
            exit;
        } else {
            // Unhandled error
            echo $linkAccountJson;
        }
    } else {
        $addBookUserJson = callAPI($addBookUserURL, $addBookUserVars);
        $addBookUserData = json_decode($addBookUserJson, true);
        if( $addBookUserData['errno'] ) {
            // Unhandled error
            echo $addBookUserJson;
        } else {
            
            echo $addBookUserJson;
                    
            header('Location: ../success/?email='. urlencode($email) .'&campaign_id='. $campaign_id );
            exit;

        }
    }



?>
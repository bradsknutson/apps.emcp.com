<?php

// Get Request with email, fname, lname, campaign_id, book_id, and length (optional)

include 'inc/functions.php';

redirect($email);

$account_id = getID($email, $mysqli);

$checkquery = "SELECT *
        FROM campaign_log
        WHERE account_id = '". $account_id ."'
        AND campaign_id = '". $campaign_id ."'";

$checkresult = $mysqli->query($checkquery);

if( $checkresult->num_rows == 0) {
    $addCodeJson = callAPI($addCodeURL, $addCodeVars);
    $addCodeData = json_decode($addCodeJson, true);
    $activation_code = $addCodeData['code'];
    
    $insert = "INSERT INTO campaign_log VALUES (NULL, '". $account_id ."', '". $campaign_id ."', '". $activation_code ."', '". $duration ."', NULL)";
    $insertresult = $mysqli->query($insert);
} else {
    while ($checkrow = $checkresult->fetch_assoc()) {
        $activation_code = $checkrow['code'];
    }
}

$vars = array(
	'activation_code' => $activation_code,
	'username' => $email,
	'lastname' => $lname,
	'firstname' => $fname,
	'email' => $email,
	'application_name' => 'SFDC',
    'password' => generatePassword(),
    'account_id' => $account_id,
);

$addBookVars = array(
    'activation_code' => $activation_code,
	'application_name' => 'SFDC',
    'account_id' => $account_id,    
);

$addAccountJson = callAPI($addAccountUrl, $vars);
$addAccountData = json_decode($addAccountJson, true);

if( $addAccountData['errno'] ) {
    if( $addAccountData['errno'] == 11 ) {
        // Account ID already exists for application => /webservice/addbook
        $addBookJson = callAPI($addBookURL, $addBookVars);
        $addBookData = json_decode($addBookJson, true);
        header('Location: success/?code='. $activation_code .'&email='. $email);
        exit;
    } else if( $addAccountData['errno'] == 12 ) {
        // Username already exists => Password required to move forward
        header('Location: password/?code='. $activation_code .'&email='. $email .'&fname='. $fname .'&lname='. $lname);
        exit;
    } else if( $addAccountData['errno'] == 22 ) {
        // Activation code is already linked to this user => Send to prevent page
        header('Location: prevent/?code='. $activation_code .'&email='. $email);
        exit;
    } else {
        // Unhandled error
        echo $addAccountJson;
    }
} else {
    header('Location: success/?code='. $activation_code .'&email='. $email);
    exit;
}

?>
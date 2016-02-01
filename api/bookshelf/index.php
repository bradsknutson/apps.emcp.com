<?php

// Get Request with email, fname, lname, campaign_id (campaign stores the group_id, book_id and duration)

include 'inc/functions.php';


redirect($email);

$account_id = getID($email, $mysqli);

$checkquery = "SELECT *
        FROM campaigns
        WHERE campaign_id = '". $campaign_id ."'";

$checkresult = $mysqli->query($checkquery);
 
if( $checkresult->num_rows == 0) {
    // No campaign
    
    header('Location: http://www.emcp.com');
    exit;
    
} else {
    while ($checkrow = $checkresult->fetch_assoc()) {
        $group_id = $checkrow['group_id'];
        $book_id = $checkrow['book_id'];
        $duration = $checkrow['duration'];
    }
}

$addExternalUserVars = array(
    'account_id' => $account_id,
    'username' => $email,
    'firstname' => $fname,
    'lastname' => $lname,
    'email' => $email,
    'application_name' => 'SFDC',
);

$addBookUserVars = array(
    'group_id' => $group_id,
    'account_id' => $account_id,
    'application_name' => 'SFDC',
    'duration' => $duration,
);

$addBookUserVars = array(
    'group_id' => $group_id,
    'account_id' => $account_id,
    'application_name' => 'SFDC',
    'duration' => $duration,
);

$userLogQuery = "SELECT * FROM user_log
        WHERE account_id = '". $account_id ."'
        AND campaign_id = '". $campaign_id ."'";

$userLogResult = $mysqli->query($userLogQuery);

if( $userLogResult->num_rows == 0) {
    // User->Campaign does no exists => /webservice/addexternaluser
    $insertUserLogQuery = "INSERT INTO user_log (account_id,campaign_id)
                            VALUES ('". $account_id ."','". $campaign_id ."')";
    
    $insertUserLogResult = $mysqli->query($insertUserLogQuery);
    
    $addExternalUserJson = callAPI($addExternalUserURL, $addExternalUserVars);
    $addExternalUserData = json_decode($addExternalUserJson, true);
    
    echo $addExternalUserJson;
    
    if( $addExternalUserData['errno'] == 12 ) {
        // Username already exists => /webservice/linkaccount

        header('Location: password/?email='. urlencode($email) .'&campaign_id='. $campaign_id );
        exit;

    } else {
        // Account created successfully => /webservice/addbookuser
            
        $addBookUserJson = callAPI($addBookUserURL, $addBookUserVars);
        $addBookUserData = json_decode($addBookUserJson, true);

        header('Location: success/?email='. urlencode($email) .'&campaign_id='. $campaign_id );
        exit;         
        
    }
    
} else {
    // User->Campaign exists => /webservice/gotobook
        
    header('Location: success/?email='. urlencode($email) .'&campaign_id='. $campaign_id );
    exit;    
    
}

?>
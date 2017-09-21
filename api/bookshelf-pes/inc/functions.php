<?php

include 'con.php';

if(isset($_GET['fname'])) {
    $fname = $_GET['fname'];
}
if(isset($_GET['lname'])) {
    $lname = $_GET['lname'];
}
$email = $_GET['email'];
$campaign_id = $_GET['campaign_id'];

//$base = 'https://staging.bookshelf.emcp.com'; // Staging
$base = 'https://paradigm.bookshelf.emcp.com'; // Production

$linkAccountURL = $base .'/webservice/linkaccount';
$addExternalUserURL = $base .'/webservice/addexternaluser';
$addBookUserURL = $base .'/webservice/addbookuser';
$goToBookURL = $base .'/webservice/gotobook';

$account_id = getID($email, $mysqli);

function generatePassword($length = 12) {
    $chars = 'bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ23456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}

function callAPI($url, $data) {
    
    $curl = curl_init();

	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);
    
    return $result; 
    
}

function getID($email, $mysqli) {
    $check = "SELECT id
            FROM account_id
            WHERE email = '". $email ."'";
    
    $result = $mysqli->query($check);

    if( $result->num_rows == 0) {
        $insert = "INSERT INTO account_id VALUES (NULL, '". $email ."')";
        $insertresult = $mysqli->query($insert);
        return $mysqli->insert_id;
    } else {
        while ($row = $result->fetch_assoc()) {
            return $row['id'];
        }
    }
    
    $result->close();
    $mysqli->close();
}

function getCampaignVars($email, $campaign_id, $mysqli) {
    $query = "SELECT *
                FROM campaigns
                WHERE campaign_id = '". $campaign_id ."'";
    
    $result = $mysqli->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }  
    
    $result->close();
    $mysqli->close();
    
    return $rows;
}

function redirect($a) {
    if( !$a ) {
        header('Location: http://www.emcp.com');
        exit;
    }
    return;
}

?>
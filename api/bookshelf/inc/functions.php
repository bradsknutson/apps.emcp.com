<?php

include 'con.php';

$fname = $_GET['fname'];
$lname = $_GET['lname'];
$book_id = $_GET['book_id'];
$campaign_id = $_GET['campaign_id'];
$length = $_GET['length'];
if( $length == '' || intval($length) > 90 ) {
    $duration = '21';
} else {
    $duration = $length;
}
if( !isset($password) ) {
    $password = $_GET['password'];
}
if( !isset($email) ) {
    $email = $_GET['email'];
}

$base = 'https://staging.bookshelf.emcp.com';

$addAccountUrl = $base .'/webservice/addaccount';
$addBookURL = $base .'/webservice/addbook';
$linkAccountURL = $base .'/webservice/linkaccount';
$goToBookURL = $base .'/webservice/linkbook';
$addCourseBookURL = $base .'/webservice/addcoursebook';
$addCodeURL = $base .'/webservice/addcode';

$addCodeVars = array(
    'application_name' => 'SFDC',
    'book_id' => $book_id,
    'duration' => $duration,
);

$account_id = getID($email, $mysqli);

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

$linkAccountVars = array (
	'application_name' => 'SFDC',
    'account_id' => $account_id,    
	'username' => $email,
    'password' => $password,
);



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

function redirect($a) {
    if( !$a ) {
        header('Location: http://www.emcp.com');
        exit;
    }
    return;
}

?>
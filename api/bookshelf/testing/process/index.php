<?php

// POST Request with service, email, fname, lname, book_id, and duration

include '../../inc/functions.php';

$web = $_POST['service'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$book_id = $_POST['book_id'];
$duration = $_POST['duration'];

$account_id = getID($email, $mysqli);

$baseURL = 'https://staging.bookshelf.emcp.com';
$service = $baseURL . $web;

/*
echo 'Web Service: '. $service;
echo '<br />';
echo 'First Name: '. $fname;
echo '<br />';
echo 'Last Name: '. $lname;
echo '<br />';
echo 'Email: '. $email;
echo '<br />';
echo 'Book ID: '. $book_id;
echo '<br />';
echo 'Duration: '. $duration;
echo '<br />';
echo 'Account ID: '. $account_id;
*/

$vars = array(
    'account_id' => $account_id,
	'application_name' => 'SFDC',
    'book_id' => $book_id,
    'duration' => $duration,
);

$json = callAPI($service, $vars);
$data = json_decode($json, true);

echo $json;

?>
<?php
        
    $db = 'emcpcom_apps_support';
    $user = 'emcpcom_apps_su';
    $pass = 'SquabGrillsFliestSpurn88';

    $mysqli =  mysqli_connect('localhost',$user,$pass,$db);

    if ($mysqli->connect_error) {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }

?>
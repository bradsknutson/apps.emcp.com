<?php

    include 'con.php';
    
    $uid = $mysqli->real_escape_string( $_POST['uid'] );
    $act = $mysqli->real_escape_string( $_POST['act'] );
    
    $query = "UPDATE user_activities SET act = '". $act ."' WHERE uid = ". $uid;
    $mysqli->query($query);

    $mysqli->close();

?>
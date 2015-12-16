<?php

    include 'con.php';

    $uid = $_POST['uid'];

    $query = 'SELECT act
                FROM user_activities
                WHERE uid = '. $uid;
    
    $result = $mysqli->query($query);
    
    while($row = $result->fetch_array()) {
        $act = $row['act'];
    }
    $c = $result->num_rows;
    $result->close();

    if( $c == '0' ) {
        $insert = 'INSERT INTO user_activities (uid,act)
                    VALUES ('. $uid .',"")';
        $push = $mysqli->query($insert);
    } else {
        echo $act;   
    }

?>
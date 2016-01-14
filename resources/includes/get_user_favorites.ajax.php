<?php

    include 'con.php';

    $uid = $_POST['uid'];

    $query = 'SELECT act
                FROM user_favorites
                WHERE uid = '. $uid;
    
    $result = $mysqli->query($query);
    
    while($row = $result->fetch_array()) {
        $act = $row['act'];
    }
    $c = $result->num_rows;
    $result->close();

    if( $c == '0' ) {
        $insert = 'INSERT INTO user_favorites (uid,act)
                    VALUES ('. $uid .',"")';
        $push = $mysqli->query($insert);
    } else {
        echo $act;   
    }

?>
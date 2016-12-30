<?php

    include 'functions.php';

    $id = $mysqli->real_escape_string( $_POST['id'] );
    $string = $mysqli->real_escape_string( $_POST['string'] );
    $destination = $mysqli->real_escape_string( $_POST['destination'] );

    $dupCheck = "SELECT id, string, destination, book_id, deleted
                    FROM redirects
                    WHERE string = '". $string ."'
                    AND book_id = '". $id ."'
                    AND deleted = '0'";

    $check = $mysqli->query($dupCheck);

    if( $check->num_rows === 0 ) { 
  
        $insert = "INSERT INTO redirects (string,destination,book_id)
                    VALUES ('". $string ."','". $destination ."','". $id ."')";

        $result = $mysqli->query($insert);

        echo '<p>Redirect generated for <strong>'. $string .'</strong> <a href="/redirects/links/edit/'. $mysqli->insert_id .'"><i class="fa fa-pencil" aria-hidden="true"></i></a></p>';
               
    } else { 

        $match = $check->fetch_assoc();
        
        echo '<p>Redirect already exists for <strong>'. $string .'</strong> and was <strong>not</strong> generated. <a href="/redirects/links/edit/'. $match['id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a></p>';

    }

?>
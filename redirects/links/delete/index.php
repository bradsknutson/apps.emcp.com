<?php

    include '../../includes/functions.php';

    $id = $mysqli->real_escape_string( $_GET['id'] );

    if( $id == '' ) {
        echo 'No ID given, nothing was deleted.';
    } else {

        $update = "UPDATE redirects
                    SET deleted = '1'
                    WHERE id = '". $id ."'";

        $result = $mysqli->query($update);

        echo "<script type=\"text/javascript\">$('.delete-link#". $id ."').parent().parent().fadeOut();</script>";
        
    }

?>
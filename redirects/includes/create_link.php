<?php

    include 'functions.php';

    $id = $mysqli->real_escape_string( $_POST['id'] );
    $string = $mysqli->real_escape_string( $_POST['string'] );
    $destination = $mysqli->real_escape_string( $_POST['destination'] );

    $insert = "INSERT INTO redirects (string,destination,book_id)
                VALUES ('". $string ."','". $destination ."','". $id ."')";
    
    $result = $mysqli->query($insert);

    echo '<script>
    
            console.log("Link created");

            $(\'#successModal\').modal(\'show\');

        </script>';

?>
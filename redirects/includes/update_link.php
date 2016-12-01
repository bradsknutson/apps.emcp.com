<?php

    include 'functions.php';

    $id = $_POST['id'];
    $string = $_POST['string'];
    $destination = $mysqli->real_escape_string( $_POST['destination'] );

    $update = "UPDATE redirects
                SET destination = '". $destination ."', string = '". $string ."'
                WHERE id = '". $id ."'";

    
    $result = $mysqli->query($update);

   echo '<script>
    
            console.log("Link updated");

            $(\'#successModal\').modal(\'show\');

        </script>';

?>
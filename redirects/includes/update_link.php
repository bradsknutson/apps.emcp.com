<?php

    include 'functions.php';

    $id = $mysqli->real_escape_string( $_POST['id'] );
    $string = $mysqli->real_escape_string( $_POST['string'] );
    $destination = $mysqli->real_escape_string( $_POST['destination'] );

    $update = "UPDATE redirects SET destination = '". $destination ."', string = '". $string ."' WHERE id = '". $id ."'";

    $result = $mysqli->query($update);

    echo '<script>
    
            console.log("ID: '. $id .' redirect updated");

            $(\'#successModal\').modal(\'show\');
            $(\'.save-link#\' + '. $id .').fadeOut().removeClass(\'saveActive\');
            
            if( $(\'.update-all\').length > 0 ) {
                $(\'#destination-'. $id .'\').attr(\'value\',\''. $destination .'\');
                $(\'.save-link#'. $id .'\').attr(\'data-destination\',\''. $destination .'\');
                $(\'.status-check-'. $id .'\').attr(\'id\',\''. $destination .'\');
                
                $(\'#redirect-'. $id .'\').attr(\'value\',\''. $string .'\');
                $(\'.save-link#'. $id .'\').attr(\'data-string\',\''. $string .'\');
                
                console.log(\'values updated\');
            }
            
            saveIconCheck();

        </script>';

?>
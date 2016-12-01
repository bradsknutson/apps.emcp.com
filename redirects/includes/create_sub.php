<?php

    include 'functions.php';

    $domain = $mysqli->real_escape_string( $_POST['domain'] );

    $check = "SELECT id FROM sub_domains
                WHERE sub = '". $domain ."'";
    
    $check_result = $mysqli->query($check);
    $check_info = $check_result->fetch_assoc();

    if( $check_result->num_rows > 0 ){
        echo '<script>
                console.log("Subdomain already exists.");
                
                // .fail-anchor
                $link = \'/redirects/domains/sub/'. $check_info['id'] .'\';
                $(\'.fail-anchor\').attr(\'href\',$link);
                
                $(\'#duplicateModal\').modal(\'show\');
                
            </script>';
    } else {
    
        
        
        $insert = "INSERT INTO sub_domains (sub)
                    VALUES ('". $domain ."')";

        $result = $mysqli->query($insert);

        echo '<script>
                console.log("New subdomain has id '. $mysqli->insert_id .'");
                
                // .success-anchor
                $link = \'/redirects/domains/sub/'. $mysqli->insert_id .'\';
                $(\'.success-anchor\').attr(\'href\',$link);
                
                $(\'#successModal\').modal(\'show\');
                
            </script>';
            
        
        
    }

?>
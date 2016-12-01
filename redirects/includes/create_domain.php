<?php

    include 'functions.php';

    $domain = $mysqli->real_escape_string( $_POST['domain'] );

    $check = "SELECT id FROM root_domains
                WHERE domain = '". $domain ."'";
    
    $check_result = $mysqli->query($check);
    $check_info = $check_result->fetch_assoc();

    if( $check_result->num_rows > 0 ){
        echo '<script>
                console.log("Domain already exists.");
                
                // .fail-anchor
                $link = \'/redirects/domains/'. $check_info['id'] .'\';
                $(\'.fail-anchor\').attr(\'href\',$link);
                
                $(\'#duplicateModal\').modal(\'show\');
                
            </script>';
    } else {
    
        
        
        $insert = "INSERT INTO root_domains (domain)
                    VALUES ('". $domain ."')";

        $result = $mysqli->query($insert);

        echo '<script>
                console.log("New domain has id '. $mysqli->insert_id .'");
                
                // .success-anchor
                $link = \'/redirects/domains/'. $mysqli->insert_id .'\';
                $(\'.success-anchor\').attr(\'href\',$link);
                
                $(\'#successModal\').modal(\'show\');
                
            </script>';
            
        
        
    }

?>
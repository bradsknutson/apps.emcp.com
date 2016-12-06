<?php

    include 'functions.php';

    $title = $mysqli->real_escape_string( $_POST['title'] );
    $domain_id = $mysqli->real_escape_string( $_POST['domain_id'] );
    $sub_id = $mysqli->real_escape_string( $_POST['sub_id'] );

    $check = "SELECT id FROM book
                WHERE domain_id = '". $domain_id ."'
                AND sub_id = '". $sub_id ."'";
    
    $check_result = $mysqli->query($check);
    $check_info = $check_result->fetch_assoc();

    if( $check_result->num_rows > 0 ){
        echo '<script>
                console.log("Book already exists.");
                
                // .fail-anchor
                $link = \'/redirects/books/'. $check_info['id'] .'\';
                $(\'.fail-anchor\').attr(\'href\',$link);
                
                $(\'#duplicateModal\').modal(\'show\');
                
            </script>';
    } else {
        
        $insert = "INSERT INTO book (title,domain_id,sub_id)
                    VALUES ('". $title ."','". $domain_id ."','". $sub_id ."')";

        $result = $mysqli->query($insert);

        echo '<script>
                console.log("New book has id '. $mysqli->insert_id .'");
                
                // .success-anchor
                $link = \'/redirects/books/'. $mysqli->insert_id .'\';
                $(\'.success-anchor\').attr(\'href\',$link);
                
                $(\'#successModal\').modal(\'show\');
                
            </script>'; 
        
    }

?>
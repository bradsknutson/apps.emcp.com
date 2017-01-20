<?php

    include 'functions.php';

    $title = $mysqli->real_escape_string( $_POST['title'] );
    $domain_id = $mysqli->real_escape_string( $_POST['domain_id'] );
    $sub_id = $mysqli->real_escape_string( $_POST['sub_id'] );
    $default_url = $mysqli->real_escape_string( $_POST['default_url'] );

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
        
        $alias_check = "SELECT id, book_id
                FROM book_alias
                WHERE domain_id = '". $domain_id ."'
                AND sub_id = '". $sub_id ."'";
    
        $alias_check_result = $mysqli->query($alias_check);
        $alias_check_info = $alias_check_result->fetch_assoc();
        
        if( $alias_check_result->num_rows > 0 ) {
            
            echo '<script>
                    console.log("Alias already exists.");

                    // .alias-fail-anchor
                    $link = \'/redirects/books/alias/manage/'. $alias_check_info['id'] .'\';
                    $(\'.alias-fail-anchor\').attr(\'href\',$link);
                    // .book-fail-anchor
                    $link = \'/redirects/books/'. $alias_check_info['book_id'] .'\';
                    $(\'.book-fail-anchor\').attr(\'href\',$link);

                    $(\'#aliasModal\').modal(\'show\');

                </script>'; 
            
        } else {
        
            $insert = "INSERT INTO book (title,domain_id,sub_id,default_url)
                        VALUES ('". $title ."','". $domain_id ."','". $sub_id ."','". $default_url ."')";

            $result = $mysqli->query($insert);
            $book_id_new = $mysqli->insert_id;
                        
            $insert_default_redirect = "INSERT INTO redirects (string,destination,book_id,deleted)
                                        VALUES ('','". $default_url ."','". $book_id_new ."','0')";
            
            $result_default_redirect = $mysqli->query($insert_default_redirect);

            echo '<script>
                    console.log("New book has id '. $book_id_new .'");

                    // .success-anchor
                    $link = \'/redirects/books/'. $book_id_new .'\';
                    $(\'.success-anchor\').attr(\'href\',$link);

                    $(\'#successModal\').modal(\'show\');

                </script>'; 
        }
        
    }

?>
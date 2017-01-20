<?php

    include 'functions.php';

    $book_id = $mysqli->real_escape_string( $_POST['book_id'] );
    $domain_id = $mysqli->real_escape_string( $_POST['domain_id'] );
    $sub_id = $mysqli->real_escape_string( $_POST['sub_id'] );

    $book_check = "SELECT id FROM book
                WHERE domain_id = '". $domain_id ."'
                AND sub_id = '". $sub_id ."'";
    
    $book_check_result = $mysqli->query($book_check);
    $book_check_info = $book_check_result->fetch_assoc();

    if( $book_check_result->num_rows > 0 ){
        
        
        echo '<script>
                console.log("Book already exists.");
                
                // .alias-book-exists
                $link = \'/redirects/books/'. $book_check_info['id'] .'\';
                $(\'.alias-book-exists\').attr(\'href\',$link);
                
                $(\'#duplicateBookModal\').modal(\'show\');
                
            </script>';
    } else {
        
        $alias_check = "SELECT id FROM book_alias
                    WHERE domain_id = '". $domain_id ."'
                    AND sub_id = '". $sub_id ."'";

        $alias_check_result = $mysqli->query($alias_check);
        $alias_check_info = $alias_check_result->fetch_assoc();        
        
        if( $alias_check_result->num_rows > 0 ){
            
            echo '<script>
                    console.log("Book alias already exists.");

                    // .alias-exists
                    $link = \'/redirects/books/alias/manage/'. $alias_check_info['id'] .'\';
                    $(\'.alias-exists\').attr(\'href\',$link);

                    $(\'#duplicateAliasModal\').modal(\'show\');

                </script>';
            
        } else {
        
            $insert = "INSERT INTO book_alias (book_id,domain_id,sub_id)
                        VALUES ('". $book_id ."','". $domain_id ."','". $sub_id ."')";

            $result = $mysqli->query($insert);

            echo '<script>
                    console.log("New book alias has id '. $mysqli->insert_id .'");

                    $(\'#successModal\').modal(\'show\');

                </script>'; 
            
        }

    }

?>
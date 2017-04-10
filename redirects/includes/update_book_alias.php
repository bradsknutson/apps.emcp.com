<?php

    include 'functions.php';

    $id = $mysqli->real_escape_string( $_POST['id'] );
    $book_id = $mysqli->real_escape_string( $_POST['book_id'] );
    $domain_id = $mysqli->real_escape_string( $_POST['domain_id'] );
    $sub_id = $mysqli->real_escape_string( $_POST['sub_id'] );

    $update = "UPDATE book_alias 
                SET book_id = '". $book_id ."', domain_id = '". $domain_id ."', sub_id = '". $sub_id ."' 
                WHERE id = '". $id ."'";
    
    $update_result = $mysqli->query($update);

    echo '<script>
            console.log("Alias updated");

            // .success-anchor
            $link = \'/redirects/books/alias/manage/'. $id .'\';
            $(\'.success-anchor\').attr(\'href\',$link);

            $(\'#successModal\').modal(\'show\');

        </script>';
        
?>
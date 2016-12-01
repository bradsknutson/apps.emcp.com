<?php

    include 'functions.php';

    $id = $mysqli->real_escape_string( $_POST['id'] );
    $title = $mysqli->real_escape_string( $_POST['title'] );
    $domain_id = $mysqli->real_escape_string( $_POST['domain_id'] );
    $sub_id = $mysqli->real_escape_string( $_POST['sub_id'] );

    $update = "UPDATE book SET title = '". $title ."', domain_id = '". $domain_id ."', sub_id = '". $sub_id ."' WHERE id = '". $id ."'";
    
    $update_result = $mysqli->query($update);

    echo '<script>
            console.log("Book updated");

            // .success-anchor
            $link = \'/redirects/books/'. $id .'\';
            $(\'.success-anchor\').attr(\'href\',$link);

            $(\'#successModal\').modal(\'show\');

        </script>';
        
?>
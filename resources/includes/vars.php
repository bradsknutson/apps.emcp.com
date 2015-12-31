<?php
    $book = $_GET['book'];
    $passport = $_GET['passport'];
    $uid = $_GET['uid'];

    
    if( empty($book) ) {
        header('Location: http://www.emcp.com');
        exit;
    }

?>
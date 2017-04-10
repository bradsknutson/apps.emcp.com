<?php    

    $database = $_GET['db'];

    $fm = new FileMaker();

    $fm->setProperty('database', 'Technical Support');
    $fm->setProperty('username', 'bknutson');
    $fm->setProperty('password', '875emcp');
    
?>
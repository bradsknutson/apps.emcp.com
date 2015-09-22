<?php

    $ip = '206.9.73.9';

    if($_SERVER['REMOTE_ADDR'] != $ip) {
        header("Location: http://www.emcp.com/");
    } else {
        
?>

<?php } ?>
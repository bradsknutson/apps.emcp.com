<?php

    require 'conn.php';

    $ip1 = '63.224.12.3'; // EMCP Building Address
    $ip2 = '71.220.36.110'; // Brad Home IP
    $ip3 = '209.181.150.39'; // Brian Home IP

    function redirect($location) {

        header("HTTP/1.1 301 Moved Permanently"); 
        header('Location: '. $location);
        exit;
        
    }

?>
<?php

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
        $http = 'https://';
    } else { 
        $http = 'http://';
    }

    $dirs = explode( '/', getcwd() );

    $base = $http . $dirs[4] .'/'. $dirs[6] .'/';

?>
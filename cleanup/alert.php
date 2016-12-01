<?php

    $loc = getcwd() . '/test.php';

    $fname = $loc;
    $fhandle = fopen($fname, 'r');

    $size = filesize($fname);

    if( $size == '140' ) {
        echo 'ok';
    } else {
        echo 'WARNING: File size difference detected.';
        
        $hook = 'https://hooks.zapier.com/hooks/catch/194096/69an4l/';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $hook);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
            http_build_query(array('infected' => 'true')));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);

        curl_close ($ch);        
        
    }

?>
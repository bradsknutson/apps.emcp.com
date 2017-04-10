<?php

    $file = $_POST['file'];
    $filename = $file .'.json';
    $contents = $_POST['contents'];

    $dir = '/chroot/home/emcpcom/apps.emcp.com/html/support/lib/js/';

    file_put_contents($dir . $filename, $contents);

    echo $contents;

?>
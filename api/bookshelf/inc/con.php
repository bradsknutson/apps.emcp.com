<?php 

$mysqli = new mysqli("localhost", "emcpcom_resourc", "RodeoCutterHopeTicker96 ","emcpcom_resources");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>
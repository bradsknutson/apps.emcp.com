<?php 

$mysqli = new mysqli("localhost", "emcpcom_pes_ids", "GenieHaltsCheapVassal99","emcpcom_pes_ids");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>
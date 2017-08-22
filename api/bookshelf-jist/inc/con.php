<?php 

$mysqli = new mysqli("localhost", "emcpcom_jist_id", "CheepSkinKeelChoir22","emcpcom_jist_ids");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>
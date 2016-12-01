<?php

$url = $_POST['url'];

$handle = curl_init($url);
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

$response = curl_exec($handle);

$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
if($httpCode == 404) {
    echo '404';
} else if($htpCode == 403) {
    echo '403';
}else if($httpCode == 500) {
    echo '500';
} else {
    echo 'OK';
}

curl_close($handle);

?>
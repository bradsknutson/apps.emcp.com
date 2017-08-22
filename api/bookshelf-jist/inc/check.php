<?php

    include 'functions.php';

    $account_id = getID($email, $mysqli);

    $checkquery = "SELECT *
            FROM campaign_log
            WHERE account_id = '". $account_id ."'
            AND campaign_id = '". $campaign_id ."'";
    
    $checkresult = $mysqli->query($checkquery);

    if( $checkresult->num_rows == 0) {
        $insert = "INSERT INTO campaign_log VALUES (NULL, '". $account_id ."', '". $campaign_id ."', '". $activation_code ."', '". $duration ."', NULL)";
        $insertresult = $mysqli->query($insert);
    } else {
        while ($checktrow = $checkresult->fetch_assoc()) {
            $activation_code = $checktrow['code'];
        }
    }

?>
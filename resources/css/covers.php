<?php

    ob_start("ob_gzhandler");

    header("Content-type: text/css");

    include '../includes/con.php';
    $id = $_GET['id'];

    $getCovers = 'SELECT a.activity_name, b.level, b.cover, COUNT(c.id) as count
                    FROM resource_master a, resource_asset_data b, resource_meta_data c
                    WHERE b.resource_id = a.id
                    AND c.resource_id = a.id
                    AND c.level = b.level
                    AND b.program_id = "'. $id .'"
                    GROUP BY b.cover';

    $getCoversResult = $mysqli->query($getCovers);
    
    while($getCoversRow = $getCoversResult->fetch_array()) {
        $getCoversRows[] = $getCoversRow;
    }
    $getCoversResult->close();

    foreach( $getCoversRows as $i ) {
        $activity = strtolower(str_replace("'", "", str_replace("&","and", str_replace(" ","_", $i['activity_name']) ) ) );
        
        $css .= ".cover-". $activity ."l". $i['level'] ." { background-image: url('../img/covers/". $i['cover'] ."'); }\r\n";   
    } 

    echo $css;

?>
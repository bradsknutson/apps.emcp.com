<?php

    $getUnitCount = 'SELECT level, COUNT(DISTINCT unit) AS unitCount FROM `resource_meta_data` WHERE program_id = "'. $id .'" GROUP BY level';
    $getUnitCountResult = $mysqli->query($getUnitCount);
    
    while($getUnitCountRow = $getUnitCountResult->fetch_array()) {
        $getUnitCountRows[] = $getUnitCountRow;
    }        
    $getUnitCountResult->close();

?>
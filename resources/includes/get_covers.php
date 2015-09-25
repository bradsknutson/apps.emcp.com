<?php

    $getCovers = 'SELECT a.activity_name, b.level, b.cover
                    FROM resource_master a, resource_asset_data b
                    WHERE b.resource_id = a.id
                    AND b.program_id = "'. $id .'"';

    $getCoversResult = $mysqli->query($getCovers);
    
    while($getCoversRow = $getCoversResult->fetch_array()) {
        $getCoversRows[] = $getCoversRow;
    }
    $result->close();

    // cover-workbookl1

    $css = '<style>';
    foreach( $getCoversRows as $i ) {
        $activity = str_replace("'", "", explode(' ', $i['activity_name']) );
        
        $css .= '.cover-'. strtolower($activity[0]) .'l'. $i['level'] .' { background-image: url(\''. $base .'img/covers/'. $i['cover'] .'\');background-size: 100% } ';   
    }
    $css .= '</style>';

?>
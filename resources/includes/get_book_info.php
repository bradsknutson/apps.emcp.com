<?php

    $query = 'SELECT * FROM `program_master` WHERE string = "'.$book.'"';
    $result = $mysqli->query($query);
    
    while($row = $result->fetch_array()) {
        $title = $row['title'];
        $id = $row['program_id'];
    }

    if($result->num_rows == 0) {
        if(!empty($book)) {
            header('Location: http://www.emcp.com');
            exit;
        }
    }
    $result->close();

    $heroQuery = 'SELECT * FROM `program_meta_data` WHERE program_id = "'. $id .'"';
    $heroResult = $mysqli->query($heroQuery);

    while($heroRow = $heroResult->fetch_array()) {
        $title = $heroRow['title'];
        $hero = $heroRow['hero_img'];
        $levels = $heroRow['levels'];
        $unit_title = $heroRow['unit_title'];
        $lesson_title = $heroRow['lesson_title'];
    }

    $heroResult->close();

    $stringQuery = 'SELECT * FROM `program_master` WHERE program_id = "'. $id .'" LIMIT 0, 1';
    $stringResult = $mysqli->query($stringQuery);

    while($stringRow = $stringResult->fetch_array()) {
        $string = $stringRow['string'];   
    }

    $stringResult->close();

    if( $book != $string ) {
        header("HTTP/1.1 301 Moved Permanently"); 
        header('Location: '. $base . $string .'/');
        exit;
    }

?>
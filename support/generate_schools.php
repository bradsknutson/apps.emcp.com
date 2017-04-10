<?php

    ob_start('ob_gzhandler');

    require_once('FileMaker.php');
    include('functions.php');

    $query = $fm->newFindAllCommand('Institutions'); 
    $result = $query->execute();

    $records = $result->getRecords(); 
    $foundSet = $result->getFoundSetCount(); 
    $fields = $result->getFields;

    
    $start = '[';

        foreach($records as $record) {
            $schools .= '{"name":"'. $record->getField('listName') .'","zip":"'. $record->getField('ZIP') .'","type":"'. $record->getField('Type') .'"},';
        }

    $end = ']';

    $schoolsJSON = $start . rtrim($schools,",") . $end;
    $data = array('file' => 'schools', 'contents' => $schoolsJSON);
        
    $url = 'http://apps.emcp.com/support/generate_json.php';

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        // handle error
    }

    echo $result;

?>
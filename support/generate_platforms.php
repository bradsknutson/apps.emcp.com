<?php

    ob_start('ob_gzhandler');

    require_once('FileMaker.php');
    include('functions.php');

    $query = $fm->newFindAllCommand('Platforms'); 
    $result = $query->execute();

    $records = $result->getRecords(); 
    $foundSet = $result->getFoundSetCount(); 
    $fields = $result->getFields;

    
    $start = '[';

    foreach($records as $record) {
        
        // Organization Type Processing
        $orgList = '';
        $orgArray = preg_split("/\\r\\n|\\r|\\n/", $record->getField('relatedPublisher') );
        $orgStart = '[';
        foreach($orgArray as $orgItem) {
            if( $orgItem == 'EMC' ) {
                $orgList .= '{"type": "K-12"},';    
            }
            if( $orgItem == 'Paradigm' ) {
                $orgList .= '{"type": "Post Secondary"},';    
            }
            if( $orgItem == 'JIST' ) {
                $orgList .= '{"type": "Federally Funded"},';    
            }
        }
        $orgEnd = ']';
        $orgDisplay = $orgStart . rtrim($orgList,",") . $orgEnd;
        
        /*
        // Category Processing
        $catList = '';
        $catArray = preg_split('/\R/', $record->getField('Umbrella') );
        $catStart = '[';
        foreach($catArray as $catItem) {
            $catList .= '{"category": "'. $catItem .'"},';
        }    
        $catEnd = ']';
        $catDisplay = $catStart . rtrim($catList,",") . $catEnd;
        // Code to add categories to json: ,"categories":'. $catDisplay .'
        */
        
        if( $record->getField('displayOnWebForm') == 'Yes' ) {

            // Display
            $platforms .= '{"name":"'. $record->getField('Platform Name') .'","types":'. $orgDisplay .',"requireProduct":"'. $record->getField('requireProduct') .'","webName":"'. $record->getField('customerFriendlyName') .'"},';

        }
    }

    $end = ']';

    $platformsJSON = $start . rtrim($platforms,",") . $end;
    $data = array('file' => 'platforms', 'contents' => $platformsJSON);
        
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
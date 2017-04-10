<?php

    ob_start('ob_gzhandler');

    require_once('FileMaker.php');
    include('functions.php');

    $query = $fm->newFindAllCommand('Types'); 
    $result = $query->execute();

    $records = $result->getRecords(); 
    $foundSet = $result->getFoundSetCount(); 
    $fields = $result->getFields;

    
    $start = '[';

        foreach($records as $record) {
        
            $platformList = '';

            $platformArray = preg_split("/\\r\\n|\\r|\\n/", $record->getField('Platform') );
            $platformStart = '[';
            foreach($platformArray as $platformItem) {
                $platformList .= '{"platform": "'. $platformItem .'"},';
            }
            $platformEnd = ']';
            $platformDisplay = $platformStart . rtrim($platformList,",") . $platformEnd;            
            
            if( $record->getField('displayOnWebForm') == 'Yes' ) {
                $help .= '{"type":"'. $record->getField('Type') .'","webName":"'. $record->getField('customerFriendlyName') .'","platforms":'. $platformDisplay .'},';
            }
        }

    $end = ']';

    $helpJSON = $start . rtrim($help,",") . $end;
    $data = array('file' => 'help', 'contents' => $helpJSON);
        
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
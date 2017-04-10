<?php

    ob_start('ob_gzhandler');

    require_once('FileMaker.php');
    include('functions.php');

    $query = $fm->newFindAllCommand('Products'); 
    $result = $query->execute();

    $records = $result->getRecords(); 
    $foundSet = $result->getFoundSetCount(); 
    $fields = $result->getFields;

    
    $start = '[';

    foreach($records as $record) {
        
        $platformItem = '';        
        $platforms = preg_split("/\\r\\n|\\r|\\n/", $record->getField('Platform') );
        $platformStart = '[';
        foreach($platforms as $platform) {
            $platformItem .= '{"platform": "'. $platform .'"},';   
        }            
        $platformEnd = ']';
        $platformDisplay = $platformStart . rtrim($platformItem,",") . $platformEnd;
        
        $orgTypeItem = '';        
        $orgTypes = preg_split("/\\r\\n|\\r|\\n/", $record->getField('webCustomerType') );
        $orgTypeStart = '[';
        foreach($orgTypes as $orgType) {
            if( $orgType == 'K-12' ) {
                $orgTypeItem .= '{"type": "K-12"},';   
            } else if( $orgType == 'Post-Secondary' ) {
                $orgTypeItem .= '{"type": "Post Secondary"},';   
            } else if ( $orgType == 'Fed-Funded' ) {
                $orgTypeItem .= '{"type": "Federally Funded"},';   
            }
        }            
        $orgTypeEnd = ']';
        $orgTypeDisplay = $orgTypeStart . rtrim($orgTypeItem,",") . $orgTypeEnd;
                
        /*
        $catList = '';
        $catArray = preg_split("/\\r\\n|\\r|\\n/", $record->getField('Umbrella') );
        $catStart = '[';
        foreach($catArray as $catItem) {
            $catList .= '{"category": "'. $catItem .'"},';
        }
        $catEnd = ']';
        $catDisplay = $catStart . rtrim($catList,",") . $catEnd;
        // code to add Umbrella to json: ,"umbrella":'. $catDisplay .'
        */
        
        // Display on Web Form Processing
        if( $record->getField('displayOnWebForm') == 'Yes' ) {
            $products .= '{"name":'. json_encode($record->getField('Name')) .',"webName":'. json_encode($record->getField('customerFriendlyName')) .',"platforms":'. $platformDisplay .',"types":'. $orgTypeDisplay .'},';
        }
        
    }

    $end = ']';

    $productsJSON = $start . rtrim($products,",") . $end;
    $data = array('file' => 'products', 'contents' => $productsJSON);
        
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
    
    echo $productsJSON;

?>
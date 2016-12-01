<?php

function listFolderFiles($dir) {
    
    global $clean_mode;
    
    $n = findBadCodeExamples();
    $badCode = infectionArr($n);
    
    $ffs = scandir($dir);
    
    foreach($ffs as $ff) {
                
        if($ff != '.' && $ff != '..' && $ff != '.DS_Store' && $ff != '.git'){
            
            $loc = $dir .'/'. $ff;
            
            if( is_dir($dir .'/'. $ff)) { // is folder
                
                listFolderFiles( $dir .'/'. $ff);
                
            } else { // is item
                
                $ft = explode('.', $ff);
                
                if( end($ft) == 'php') {
                    
                    echo cleanFile($loc,$badCode);
                    
                }
                        
            }
        }
        
    }
    
}

function cleanFile($loc,$badCode) {
   
    $valid = false;
    
    for( $i = '0'; $i <= sizeof($badCode); $i++ ) {

        $fname[$loc] = $loc;
        $fhandle[$loc] = fopen($fname[$loc], 'r');

        while (($buffer[$loc] = fgets($fhandle[$loc])) !== false) {
            if (strpos($buffer[$loc], $badCode[$i]) !== false) {
                
                rewind($fhandle[$loc]);

                $content[$loc] = fread($fhandle[$loc], filesize($fname[$loc]));
                $content[$loc] = str_replace($badCode[$i], '', $content[$loc]);

                $fhandle[$loc] = fopen($fname[$loc],'w');
                fwrite($fhandle[$loc],$content[$loc]);
                
                $valid = TRUE;

                break;
            }      
        }
        
    }

    $return = $loc;

    if( $valid == TRUE ) {

        $return .=  ' - <strong>Infection removed</strong><br />';
    } else {
        $return .= ' - Clean<br />';
    }

    fclose($fhandle[$loc]);        
    
    return $return;
       
}

function findBadCodeExamples() {
    
    $dir = '/chroot/home/emcpcom/apps.emcp.com/html/cleanup';
    $i = '0';
    
    $files = scandir($dir);
    foreach($files as $file) {
        
        $ft = explode('.', $file);
        
        if( end($ft) == 'txt') {
            if (strpos($file, 'code') !== false) {
                $i++;
            }
        }
        
    }
    
    return $i;
}

function infectionFileArr($n){
    
    $badCodeFiles = array();
    for( $i = '1'; $i <= $n; $i++ ) {
        $badCodeFiles[] = '/chroot/home/emcpcom/apps.emcp.com/html/cleanup/code'. $i .'.txt';
    }
    
    return $badCodeFiles;
    
}

function infectionArr($n) {
    
    $badCode = array();
    
    $fileArr = infectionFileArr($n);
    
    foreach($fileArr as $file) {
        $badCode[] = getBadCode($file);
    }
    
    return $badCode;
    
}

function getBadCode($fn) {
    
    $fhandle = fopen($fn, 'r');
    $content = fread($fhandle, filesize($fn));
    fclose($fhandle);
    
    return $content;
    
}

function infectionScanner($dir) {
    
    global $scan;
    $valid == FALSE;
    
    $n = findBadCodeExamples();
    $badCode = infectionArr($n);
    
    $files = scandir($dir);
    
    if($scan == 'global' ) {

        foreach($files as $file) {
            if($file != '.' && $file != '..' && $file != '.DS_Store' && $file != '.git') {
                $loc = $dir .'/'. $file;            
                if( is_dir($loc) ) { // is folder
                    infectionScanner( $loc );
                } else { // is item
                    $ft = explode('.', $file);
                    if( end($ft) == 'php') {   
                        for( $i = '0'; $i < sizeof($badCode); $i++ ) {
                            $fname[$loc] = $loc;
                            $fhandle[$loc] = fopen($fname[$loc], 'r');
                            while (($buffer[$loc] = fgets($fhandle[$loc])) !== false) {
                                if (strpos($buffer[$loc], $badCode[$i]) !== false) {
                                    $valid = TRUE;
                                    global $valid;
                                    exit('Error detected');
                                }      
                            }
                            fclose($fhandle);
                        }
                    }
                }
            }
        }
        
    } else {

        foreach($files as $file) {
            

            if($file != '.' && $file != '..' && $file != '.DS_Store' && $file != '.git') {

                $loc = $dir .'/'. $file;            

                if( is_dir($loc) ) { // is folder

                    infectionScanner( $loc );

                } else { // is item

                    $ft = explode('.', $file);

                    if( end($ft) == 'php') {

                        echo $loc;    

                        for( $i = '0'; $i < sizeof($badCode); $i++ ) {

                            $fname[$loc] = $loc;
                            $fhandle[$loc] = fopen($fname[$loc], 'r');

                            while (($buffer[$loc] = fgets($fhandle[$loc])) !== false) {
                                if (strpos($buffer[$loc], $badCode[$i]) !== false) {

                                    $valid = TRUE;
                                    $triggered = $i + 1;

                                    break;
                                }      
                            }

                            fclose($fhandle);

                        }

                        if($valid === TRUE) {
                            echo ' - <strong>Infected (code '. $triggered .')</strong><br />';
                        } else {
                            echo ' - Clean<br />';
                        }


                    }

                }

            }

        }
    }

}
<?php

$domain = $_GET['domain'];
$clean_mode = $_GET['clean'];
$scan = $_GET['scan'];

include 'functions.php';

if(isset($domain)) {

    if( $clean_mode == 'true' && !isset($scan) ) {
        listFolderFiles( '/chroot/home/emcpcom/'. $_GET['domain'] .'/html' );
    }

    if( isset($scan)) {
        infectionScanner( '/chroot/home/emcpcom/'. $_GET['domain'] .'/html' );    
    }

} else {
    $root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../');
    
    $doms = scandir($root);
    
    foreach($doms as $dom) {
                
        if($dom != '.' && $dom != '..' && $dom != '.pki' && $dom != '.ssh' && $dom != 'var'){
            
            $loc = $root .'/'. $dom;
            
            if( is_dir($loc)) {
                
                $test = file_get_contents('http://apps.emcp.com/cleanup/?domain='. $dom .'&scan=global');
                
                if(empty($test)) {
                    $report = $dom .' - <span style="color:green;">OK</span>';
                } else {
                    $report = '<a href="http://apps.emcp.com/cleanup/?domain='. $dom .'&scan=true" target="_blank">'. $dom .' - <span style="color:red;">Error detected</span></a>';
                }
                
                echo $report .'<br />';
            }
            
        }
        
    }
}
    
?>
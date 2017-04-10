<?php
    
    $ebookSlug = $_POST['ebookSlug'];
    $albumSlug = $_POST['albumSlug'];
    $albumTitle = $_POST['albumTitle'];
    $albumArt = $_POST['albumArt'];
    $albumFilesJson = $_POST['albumFiles'];
    $basePath = '/chroot/home/emcpcom/resources.emcp.com/html/ebooks/audio-albums/';
    
    $albumArtLoc = '//resources.emcp.com/ebooks/audio-albums/'. $albumSlug .'/'. $albumArt;

    $albumFilesObj = json_decode($albumFilesJson);

    $albumFileList = '';
    $albumScope = '';
    foreach( $albumFilesObj as $track ) {
        $albumScope .= '
                       "//emc.bookshelf.emcp.com/file/code/'. $ebookSlug .'/OEBPS/audio/'. $track->fileName .'",';
        
        $albumFileList .= '                         <div data-md-wavesurfer-source="" data-src="//emc.bookshelf.emcp.com/file/code/'. $ebookSlug .'/OEBPS/audio/'. $track->fileName .'" data-title="'. $track->trackTitle .'"></div>
        ';
    }
    $albumScope = substr($albumScope, 0, -1);

    $html = file_get_contents('/chroot/home/emcpcom/apps.emcp.com/html/build/audio-album/templates/copy.php');

    $html = str_replace('[TITLE]', $albumTitle, $html);
    $html = str_replace('[COVER]', $albumArtLoc, $html);
    $html = str_replace('[SCOPE]', $albumScope, $html);
    $html = str_replace('[AUDIO_FILE_LIST]', $albumFileList, $html);

    $indexPath = $basePath . $albumSlug .'/index.php';

    $index = fopen( $indexPath, "w") or die("Unable to open file!");
    fwrite($index, $html);
    fclose($index);

    $return = '<h3>Bookshelf Embed URL</h3>
            <p>https://resources.emcp.com/ebooks/audio-albums/'. $albumSlug .'/?bookshelf=true</p>
            <h3>Stand Alone URL</h3>
            <p>https://resources.emcp.com/ebooks/audio-albums/'. $albumSlug .'/</p>';

    echo $return;

?>
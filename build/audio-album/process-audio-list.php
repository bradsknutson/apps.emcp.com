<?php

    $albumDirectory = $_POST['albumDirectory'];

    $preSanitizedFilename = basename($_FILES['audioFileList']['name']);

    $sanitizedFilename = preg_replace('/[^a-zA-Z0-9\-\._]/','', $preSanitizedFilename ); 

    $baseDirectory = '/chroot/home/emcpcom/resources.emcp.com/html/ebooks/audio-albums/';
    $uploadFile = $baseDirectory . $albumDirectory .'/'. $sanitizedFilename;

    // Check filetype
    if( $_FILES['audioFileList']['type'] != 'text/csv' ) {

        $return = '<script type="text/javascript">
                var input = $(\'.form-step-2\').find(\':text\');
                input.val(\'File extension '. $_FILES['albumArt']['type'] .' not supported.\');
        </script>';

    } else {
        
        // Check filesize
        if($_FILES['audioFileList']['size'] > 500000 ){

            $return = '<script type="text/javascript">
                    var input = $(\'.form-step-2\').find(\':text\');
                    input.val(\'File size is larger than 500KB.\');
            </script>';

        } else {
            
            // Check if file already exists
            if( file_exists( $uploadFile ) ) {
                $name = pathinfo($sanitizedFilename, PATHINFO_FILENAME);
                $extension = pathinfo($sanitizedFilename, PATHINFO_EXTENSION);
                $increment = '';

                while( file_exists($baseDirectory . $albumDirectory .'/'. $name . $increment . '.' . $extension) ) {
                    $increment++;
                }

                $basename = $name . $increment . '.' . $extension;
            } else {
                $basename = $sanitizedFilename;
            }

            move_uploaded_file($_FILES['audioFileList']['tmp_name'], $baseDirectory . $albumDirectory .'/' . $basename );
            
            ini_set("auto_detect_line_endings", true);
            $csv = array_map('str_getcsv', file($baseDirectory . $albumDirectory .'/' . $basename));
            array_shift($csv);
            
            $length = sizeOf($csv);
            
            $jsObj = 'var csv = [';
            foreach($csv as $row) {
                
                $sort = explode('_', $row[0]);
                
                $jsObj .= '{audioFileTitle: "'. $row[1] .'",audioFileName: "'. $row[0] .'",sort: "'. $sort[0] .'"},';
            }
            $jsObj .= '];';
            
            $return = '<script type="text/javascript">
                '. $jsObj .'
                var filesHtml = \'<div class="row"><div class="col-md-2 border-bottom">Sort</div><div class="col-md-5 border-bottom">File Name</div><div class="col-md-5 border-bottom">Track Title</div></div>\';
                csv.forEach( function(e) {
                    filesHtml += \'<div class="row is-table-row">\';
                    filesHtml += \'     <div class="col-md-2 border-cell"><input type="text" name="rowSort" value="\' + e.sort + \'"></div>\';
                    filesHtml += \'     <div class="col-md-5 border-cell"><input type="text" name="rowAudioFileName" value="\' + e.audioFileName + \'"></div>\';
                    filesHtml += \'     <div class="col-md-5 border-cell"><input type="text" name="rowAudioFileTitle" value="\' + e.audioFileTitle + \'"></div>\';
                    filesHtml += \'</div>\';
                });
                $(\'.display-audio-file-list-container\').html(filesHtml);
                $(\'.display-audio-file-list\').fadeIn();
            </script>';
            
        }

    }

    echo $return;

?>
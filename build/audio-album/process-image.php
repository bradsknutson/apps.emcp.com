<?php

    $albumDirectory = $_POST['albumDirectory'];

    $preSanitizedFilename = basename($_FILES['albumArt']['name']);

    $sanitizedFilename = preg_replace('/[^a-zA-Z0-9\-\._]/','', $preSanitizedFilename ); 

    $baseDirectory = '/chroot/home/emcpcom/resources.emcp.com/html/ebooks/audio-albums/';
    $uploadFile = $baseDirectory . $albumDirectory .'/'. $sanitizedFilename;

    // Check filetype
    if( $_FILES['albumArt']['type'] != 'image/png' && $_FILES['albumArt']['type'] != 'image/jpg' && $_FILES['albumArt']['type'] != 'image/jpeg') {

        $return = '<script type="text/javascript">
                var input = $(\'.form-step-2\').find(\':text\');
                input.val(\'File extension '. $_FILES['albumArt']['type'] .' not supported.\');
        </script>';

    } else {
        
        // Check filesize
        if($_FILES['albumArt']['size'] > 1000000 ){

            $return = '<script type="text/javascript">
                    var input = $(\'.form-step-2\').find(\':text\');
                    input.val(\'File size is larger than 1MB.\');
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

            move_uploaded_file($_FILES['albumArt']['tmp_name'], $baseDirectory . $albumDirectory .'/' . $basename );
            
            $return = '<script type="text/javascript">
                var imageLoc = "'. $albumDirectory .'/' . $basename .'";
                $(\'.display-album-art-container\').html(\'<img src="http://resources.emcp.com/ebooks/audio-albums/\' + imageLoc + \'" />\');
                $(\'.display-album-art\').fadeIn();
                $(\'.image-name\').attr(\'id\',\''. $basename .'\');
            </script>';
            
        }

    }

    echo $return;

?>
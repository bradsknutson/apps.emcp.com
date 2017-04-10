<?php

    $title = $_POST['title'];

    $titleDirectory = strtolower(str_replace(' ','-',$title));

    $baseDirectory = '/chroot/home/emcpcom/resources.emcp.com/html/ebooks/audio-albums/';

    if ( !file_exists($baseDirectory . $titleDirectory) ) {
        mkdir($baseDirectory . $titleDirectory, 0777, true);
        $return = '<script type="text/javascript">
            $(\'input[name="albumDirectory"]\').val(\''. $titleDirectory .'\');
            $(\'input[name="albumDirectory2"]\').val(\''. $titleDirectory .'\');
            $(\'.album-name\').attr(\'id\',\''. $title .'\');
            $(\'.directory-name\').attr(\'id\',\''. $titleDirectory .'\');
            $(\'.step-1\').fadeOut(\'fast\', function() {
                $(\'.step-2\').fadeIn();
            });
        </script>';
    } else {
        $return = '<script type="text/javascript">
            $(\'input[name="title"]\').parent().addClass(\'has-error\');
            $(\'.step-1 .dialog-options\').fadeOut(\'fast\', function() {
                $(\'.form-step-1\').append(\'<p class="step-1-error">This album already exists, please try another title.</p>\');
            });
        </script>';
    }

    echo $return;

?>
<?php
    
    $book = $_POST['book'];
    $page = $_POST['page'];
    $type = $_POST['type'];
    if ($type == 'json') {
            $ext = '.js';
            $folder = 'Data';
    }
    if ($type == 'html') {
            $ext = '.xhtml';
            $folder = 'Content';
    }
    $content = $_POST['content'];

    $path = 'uploads/'. $book .'/OEBPS/'. $folder .'/'. $page . $ext;

    if (is_writable($path)) {
        echo 'The file at path '. $path .' is writable! ';
    } else {
        echo 'The file at path '. $path .' is not writable. ';   
    }

    echo 'File perms: '. substr(sprintf('%o', fileperms($path)), -4) .' ';

    clearstatcache();

    $file = fopen($path,'w') or die(" - No changes were written to the file.");

    echo fwrite($file, $content);

    fclose($file);

?>
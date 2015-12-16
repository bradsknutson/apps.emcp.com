<?php
    $base = 'https://apps.emcp.com/editor/';

    $book = $_GET['book'];
    $type = $_GET['type'];
    $page = $_GET['page'];

    if ($type == 'json') {
        $ext = '.js';
        $title = 'JSON';
        $folder = 'Data';
    }
    if ($type == 'html') {
        $ext = '.xhtml';
        $title = 'HTML';
        $folder = 'Content';
    }

?>
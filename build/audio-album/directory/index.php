<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>Audio Album Generator</title>
        <link rel="stylesheet" href="http://apps.emcp.com/editor/css/bootstrap.min.css">
        <link rel="stylesheet" href="http://apps.emcp.com/editor/css/style.css">
        <link rel="stylesheet" href="http://apps.emcp.com/redirects/lib/css/style.css">
        <link rel="stylesheet" href="http://apps.emcp.com/redirects/lib/css/font-awesome.css">
        <link rel="stylesheet" href="../lib/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="http://apps.emcp.com/editor/js/bootstrap.min.js"></script>
        <script src="http://apps.emcp.com/redirects/lib/js/script.js"></script>
        <script src="../lib/js/script.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h2>Audio Album List</h2>
                        <p>A list of all previously generated Audio Albums.</p>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="row is-table border-bottom">
                            <div class="col-md-4">Album</div>
                            <div class="col-md-4">Bookshelf Embed URL</div>
                            <div class="col-md-4">Stand Alone URL</div>
                        </div>
                        <?php
                        
                            $dir = '/chroot/home/emcpcom/resources.emcp.com/html/ebooks/audio-albums/';

                            $dirs = scandir($dir);

                            foreach($dirs as $d ) {
                                if( $d != '.' && $d != '..' && $d != 'lib' ) {
                                    echo '<div class="row is-table border-bottom">
                                        <div class="col-md-4"><a href="https://resources.emcp.com/ebooks/audio-albums/'. $d .'/" target="_blank">'. $d .'</a></div>
                                        <div class="col-md-4"><a href="https://resources.emcp.com/ebooks/audio-albums/'. $d .'/?bookshelf=true" target="_blank"><i class="fa fa-link" aria-hidden="true"></i>
</a></div>
                                        <div class="col-md-4"><a href="https://resources.emcp.com/ebooks/audio-albums/'. $d .'/" target="_blank"><i class="fa fa-link" aria-hidden="true"></i>
</a></div>
                                    </div>
                                    ';
                                }
                            }
                        
                        ?>
                    </div>        
                </div>
            </div>
        </div>
    </body>
</html>
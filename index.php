<?php

    $dom = $_SERVER['HTTP_HOST'];

    if( $dom == 'paradigmpublishing.snap2016.com') {
        header("Location: https://paradigmpublishing.instructure.com");
        exit();
    } else {

        $ip1 = '63.224.12.3'; // EMCP Building Address
        $ip2 = '184.100.121.17'; // Brad Home IP
        $ip3 = '209.181.150.39'; // Brian Home IP

        if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
            header("Location: http://www.emcp.com/");
        } else {
        
?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>EMCP Apps</title>
        <link rel="stylesheet" href="editor/css/bootstrap.min.css">
        <link rel="stylesheet" href="editor/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="editor/js/bootstrap.min.js"></script>
        <style>
            .btn:active:focus, .btn:active, .btn:focus {
                outline: none !important;   
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>EMCP Apps</h1>
                        <p>Choose Your App From the List Below.</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                            <?php

                                if ($handle = opendir('/chroot/home/emcpcom/apps.emcp.com/html')) {

                                    while (false !== ($entry = readdir($handle))) {

                                        if ($entry != "." && $entry != ".." && $entry != 'index.php' && $entry != '.htaccess' && $entry != 'cleanup' && $entry != 'editor' && $entry != 'api') {

                                            echo '<button type="button" class="btn btn-default btn-lg btn-block">'. $entry .'</button>';
                                        }
                                    }

                                    closedir($handle);
                                }
                            ?>
                                <button type="button" class="btn btn-primary btn-lg btn-block">Submit</button>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3"></div>
                    </div>
                </div>                    
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('.btn-default').click(function() {
                    $('.btn').removeClass('clicked active');
                    $(this).addClass('clicked active').parent().attr('id', $(this).text());
                });
                $('.btn-primary').click(function() {
                    if($('.clicked.active').length) {
                        $folder = $('.clicked').text();
                        window.location = document.location + $folder + '/';
                    }
                });
            });
        </script>
    </body>
</html>
<?php }

}?>
<?php

    $ip = '206.9.73.9';

    if($_SERVER['REMOTE_ADDR'] != $ip) {
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

                                        if ($entry != "." && $entry != ".." && $entry != 'index.php') {

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
<?php } ?>
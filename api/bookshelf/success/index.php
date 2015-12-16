<?php

    include '../inc/functions.php';
    $code = $_GET['code'];

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>EMC School | Bookshelf</title>
        <link rel="stylesheet" href="https://apps.emcp.com/editor/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://apps.emcp.com/api/bookshelf/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://apps.emcp.com/editor/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <?php if( $code && $email ) { ?>
                    <div class="jumbotron">
                        <h1>Thanks for your interest!</h1>
                        <p>Your eBook is is on it's way.  One moment while we redirect you.</p>
                    </div>
                    <form class="linkbook" action="<?php echo $goToBookURL; ?>" method="POST">
                        <input id="activation_code" name="activation_code" value="<?php echo $code; ?>" />
                        <input id="application_name" name="application_name" value="SFDC" />
                        <input id="account_id" name="account_id" value="<?php echo $account_id; ?>" />                        
                    </form>
                    <script type="text/javascript">
                        $(document).ready(function(){  
                            setTimeout(function(){
                                $('.linkbook').submit();
                            },1000);
                        });
                    </script>
                    <div class="row loading-row">
                        <div class="loading">
                            <div class="loader">
                                <div class="box"></div>
                                <div class="box"></div>
                                <div class="box"></div>
                                <div class="box"></div>
                            </div>
                         </div>
                    </div>
                    <?php } else { ?>
                    <div class="jumbotron">
                        <h1>Something went wrong.</h1>
                        <p>Something's not working properly. Please try again.</p>
                    </div>
                    <?php } ?>
                </div>
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
    </body>
</html>

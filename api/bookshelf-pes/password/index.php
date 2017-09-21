<?php 

    include '../inc/functions.php';
    $badpass = $_GET['badpass'];

    $campaign_id = $_GET['campaign_id'];

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>Paradigm Education Solutions | Bookshelf</title>
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
                    <div class="jumbotron">
                        <?php if( $badpass ) { ?>
                        <h1>Incorrect password.</h1>
                        <p>Hmm...the password you are using doesn't seem to be working. Please try again. If you're not sure what your password is, try <a href="http://emc.bookshelf.emcp.com/account/forgotPassword" target="_blank">resetting your password</a>.</p>
                        <? } else { ?>
                        <h1>Your eBook is almost ready.</h1>
                        <p>We just need one more thing. It looks like you already have an account on our Bookshelf platform. In order to add your new eBook to your existing account, we need you to autheticate with your Bookshelf password.</p>
                        <? } ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <form class="bookshelf-password" method="POST" action="process.php">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>
                                    <input type="hidden" id="email" name="email" value="<?php echo $email; ?>" />
                                    <input type="hidden" id="email" name="campaign_id" value="<?php echo $campaign_id; ?>" />
                                    <input class="btn btn-primary" type="submit" style="display:none;" value="Submit">
                                    <button type="button" class="btn btn-danger btn-lg btn-block">Submit</button>
                                </form>
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
                $('.btn-danger').click(function() {
                    $('.bookshelf-password').submit();
                });
            });
        </script>
    </body>
</html>

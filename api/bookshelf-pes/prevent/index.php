<?php

    include '../inc/functions.php';
    $code = $_GET['code'];

    $check = $_GET['email'];

    redirect($check);

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
                        <h1>Thanks for your interest!</h1>
                        <p>It looks like you've already redeemed your free digital eSample.  Click the button below to access your book, or contact your <a href="http://store.emcp.com/local-account-manager-locator" target="_blank">Account Manager</a> to learn more.</p>
                    </div>                    
                    <div class="row">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6">
                            <form class="linkbook prevent" action="<?php echo $goToBookURL; ?>" method="POST">
                                <input id="activation_code" name="activation_code" value="<?php echo $code; ?>" />
                                <input id="application_name" name="application_name" value="SFDC" />
                                <input id="account_id" name="account_id" value="<?php echo $account_id; ?>" />
                                <button type="button" class="btn btn-danger btn-lg btn-block" id="prevent-access">Access eBook</button>
                                <button type="button" class="btn btn-danger btn-lg btn-block" id="prevent-contact">Contact Your Account Manager</button>
                            </form>
                        </div>
                        <div class="col-sm-12 col-md-3"></div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
        <script type="text/javascript">
        $(document).ready(function() {
            $('button#prevent-access').click(function() {
                $('form').submit(); 
            });
            $('button#prevent-contact').click(function() {
                window.location.replace('http://store.emcp.com/local-account-manager-locator');
            });
        });
        </script>
    </body>
</html>

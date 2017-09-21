<?php

    include '../inc/functions.php';

    $account_id = getID($email, $mysqli);
    $campaign_vars = getCampaignVars($email, $campaign_id, $mysqli);

    $book_id = $campaign_vars[0]['book_id'];

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
                    <?php if( $email && $campaign_id ) { ?>
                    <div class="jumbotron">
                        <h1>Thanks for your interest!</h1>
                        <p>Your eBook is loading.</p>
                    </div>
                    <form class="linkbook" action="<?php echo $goToBookURL; ?>" method="GET">
                        <input id="book_id" name="book_id" value="<?php echo $book_id; ?>" />
                        <input id="account_id" name="account_id" value="<?php echo $account_id; ?>" />   
                        <input id="application_name" name="application_name" value="SFDC" />                     
                    </form>
                    <script type="text/javascript">
                        $(document).ready(function(){  
                            setTimeout(function(){
                                $('.linkbook').submit();
                            },500);
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

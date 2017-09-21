<?php

    include '../inc/functions.php';
    $fname = $_GET['fname'];
    $lname = $_GET['lname'];
    $email = $_GET['email'];
    $campaign_id = $_GET['campaign_id'];
    $time = time();

    $account_id = getID($email, $mysqli);

    $zapier = 'https://hooks.zapier.com/hooks/catch/194096/2z64ib/';

    $webhook = $zapier .'?fname='. $fname .'&lname='. $lname .'&email='. $email .'&campaign_id='. urlencode($campaign_id) .'&account_id='. $account_id .'&timestamp='. $time;

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $webhook
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);


?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>JIST Career Solutions | Bookshelf</title>
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
                        <h2>Whoa! We weren't expecting this type of demand.</h2>
                        <p>Something went wrong due to the volume of requests we are currently receiving.  We're going to look into this and get back to you shortly.  We'll have your eBook to you in no time at all!</p>
                        <p>In one moment, we'll redirect you to our home page.</p>
                        <script type="text/javascript">
                            console.log('First Name: <?php echo $fname; ?>');
                            console.log('Last Name: <?php echo $lname; ?>');
                            console.log('Email: <?php echo $email; ?>');
                            console.log('Campaign ID: <?php echo $campaign_id; ?>');
                            console.log('Account ID: <?php echo $account_id; ?>');
                            console.log('Zapier URL: <?php echo $zapier; ?>');
                            console.log('Webhook: <?php echo $webhook; ?>');
                            console.log('Ping sent!');
                            window.setTimeout(function(){ 
                                window.location.href = "http://jist.com";
                            }, 8000);
                        </script>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
    </body>
</html>

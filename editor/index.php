<?php

    include 'includes/vars.php';

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>ePub Editor</title>
        <link rel="stylesheet" href="<?php echo $base; ?>css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $base; ?>css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="<?php echo $base; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo $base; ?>js/script.js"></script>
    </head>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>ePub Editor</h1>
                        <p>Select the book for which you wish to edit interactive activities.</p>
                    </div>
                    <form class="json-book-selector">
                        <div class="form-group">
                            <label for="page">Select a Book</label>
                            <select class="form-control" id="page">
                            <?php

                                $path = 'uploads/';

                                if ($handle = opendir($path)) {

                                    while (false !== ($entry = readdir($handle))) {

                                        if ($entry != "." && $entry != "..") {

                                            echo '<option value="'. rtrim($entry, ".js") .'">'. $entry .'</option>';
                                        }
                                    }

                                    closedir($handle);
                                }

                            ?>
                            </select>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Submit">
                    </form>
                </div>                    
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
    </body>
</html>
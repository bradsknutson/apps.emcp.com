<?php 

    include 'includes/vars.php';

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>ePub Editor - Book: <?php echo $book; ?><?php if($page){ echo ' Page: '. $page; } ?></title>
        <link rel="stylesheet" href="<?php echo $base; ?>css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $base; ?>css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="<?php echo $base; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo $base; ?>js/script.js"></script>
    </head>
    </head>
    <body class="<?php echo $book; ?>" id="<?php echo $page; ?>">
        <div class="type" id="<?php echo $type; ?>"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Book Selected: <?php echo $book; ?></h1>
                        <p>To edit the JSON for a page, select the file from the dropdown below.</p>
                    </div>
                    <form class="json-page-selector">
                        <div class="form-group">
                            <label for="page">JSON</label>
                            <select class="form-control" id="page">
                            <?php

                                $path = 'uploads/'. $book .'/OEBPS/Data/';

                                if ($handle = opendir($path)) {

                                    while (false !== ($entry = readdir($handle))) {

                                        if ($entry != "." && $entry != "..") {

                                            echo '<option value="'. rtrim($entry, ".js") .'">'. rtrim($entry, ".js") .'</option>';
                                        }
                                    }

                                    closedir($handle);
                                }

                            ?>
                            </select>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Submit">
                    </form>
                    <form class="html-page-selector">
                        <div class="form-group">
                            <label for="page">HTML</label>
                            <select class="form-control" id="page">
                            <?php

                                $path = 'uploads/'. $book .'/OEBPS/Content/';

                                if ($handle = opendir($path)) {

                                    while (false !== ($entry = readdir($handle))) {

                                        if ($entry != "." && $entry != ".." && $entry != "images" && $entry != "icons" && $entry != "" ) {

                                            echo '<option value="'. str_replace(".xhtml", "", $entry) .'">'. str_replace(".xhtml", "", $entry) .'</option>';
                                        }
                                    }

                                    closedir($handle);
                                }

                            ?>
                            </select>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Submit">
                    </form>
                    <?php if(!empty($page)) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><?php echo $title; ?> for Page <?php echo $page; ?></div>
                        <div class="panel-body">
                            <form class="edit-json">
                                <div class="form-group">
                                    <textarea class="form-control" rows="10"></textarea>
                                </div>
                                <input class="btn btn-primary" type="submit" value="Submit">
                            </form>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $('.edit-json input[type="submit"]').hide();
                            $book = $('body').attr('class');
                            $page = $('body').attr('id');
                            $type = $('.type').attr('id');
                            $path = '<?php echo $base; ?>uploads/' + $book + '/OEBPS/<?php echo $folder; ?>/' + $page + '<?php echo $ext; ?>';
                            var $content;
                            
                            var $ajax = $.ajax({
                                url: $path,
                                type: "POST",
                                dataType: "text",
                            });
                            $ajax.done(function(data) {
                                $('.panel-body textarea').text(data);
                            });
                            
                            $('.edit-json textarea').keyup(function() {
                                $('.edit-json input[type="submit"]').fadeIn();
                            });
                            
                            $('.edit-json').submit(function(event) {
                                
                                var ajaxRequest = $.ajax({
                                    url: '<?php echo $base; ?>save.php',
                                    type: "POST",
                                    data: { 
                                        book: $book,
                                        page: $page,
                                        type: $type,
                                        content: $('.edit-json textarea').val()
                                    }
                                });
                                ajaxRequest.done(function(data) {
                                });	
                                
                                event.preventDefault();
                            });
                            
                        });
                    </script>
                    <?php } ?>
                </div>                    
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
    </body>
</html>
<?php 

    include 'includes/base.php';

    include 'includes/vars.php';
    include 'includes/con.php';
    include 'includes/get_book_info.php';
    include 'includes/get_covers.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title; ?> | Digital Resource Center</title>

    <link rel="shortcut icon" href="<?php echo $base; ?>img/favicon.ico" type="image/x-icon" />
          
    <script src="<?php echo $base; ?>js/jquery-1.9.1.min.js"></script>

    <link rel="stylesheet" href="<?php echo $base; ?>css/style.css"/>

    <script src="<?php echo $base; ?>js/jquery.scrollto.min.js"></script>
    
    <script src="<?php echo $base; ?>js/sly.js"></script>
    <script src="<?php echo $base; ?>js/ajax.js"></script>
    <script src="<?php echo $base; ?>js/custom.js"></script>
    
    <?php echo $css; ?>
    
</head>
<body> 	
    <div id="xcenter">
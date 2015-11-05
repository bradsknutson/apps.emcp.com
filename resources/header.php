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
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link rel="shortcut icon" href="<?php echo $base; ?>img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $base; ?>css/style.css"/ class="styled">   
    <link rel="stylesheet" href="<?php echo $base; ?>css/covers.php?id=<?php echo $id; ?>" />
          
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    
    <?php include 'includes/preload-js-only.php'; ?>
</head>
<body> 	
    <div id="xcenter">
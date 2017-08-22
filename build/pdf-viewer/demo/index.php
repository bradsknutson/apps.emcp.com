<?php

    require 'conn.php';

    $id = $_GET['id'];

    $query = "SELECT *
            FROM pdfs
            WHERE id = '". $id ."'";

    $result = $mysqli->query($query);
    $info = $result->fetch_assoc();
    $result->close();   

    $pdf_title = $info['title'];
    $pdf_filename = $info['path'];
    
    $browser = get_browser();
    // $browser->browser;

    function isIphone($user_agent=NULL) {
        if(!isset($user_agent)) {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        return (strpos($user_agent, 'iPhone') !== FALSE);
    }
    function isIpad($user_agent=NULL) {
        if(!isset($user_agent)) {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        return (strpos($user_agent, 'iPad') !== FALSE);
    }

    function isSafari($user_agent=NULL) {
        if(!isset($user_agent)) {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        return ( strpos($user_agent, 'Safari') !== FALSE && strpos($user_agent, 'Chrome') === FALSE);
    }

?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $pdf_title; ?></title>
    <link rel="stylesheet" href="lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="lib/css/font-awesome.css">
    <link rel="stylesheet" href="lib/css/style.css?v=1.0">
</head>

<body>
    
    <?php if( isIphone() || isIpad() ) { ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <p>Download the Resource Document.</p>
                <p>Open the resource in an app and fill in the document, or save to Dropbox or another app for use on a desktop or laptop computer.</p>
                <div id="downloadIcon" class="downloadIconIsIphone">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    
    <?php } else if( isSafari() ) { 
    
        header('Location: '. $pdf_filename);
        exit;
    
    } else { ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <h2 class="instructions">Instructions</h2>
                <div class="row">
                    <div class="col-md-1">
                        <div class="instructions-icon">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col-md-11">
                        <p class="step-1">Step 1. Download the Resource Document.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <div class="instructions-icon">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col-md-11">
                        <p class="step-2">Step 2. Open the resource in a program like Adobe Acrobat or Preview on a Mac and fill in the document.  Save the file.</p>
                        <p>Note: Changes to the document made in the browser may not be saved.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <div class="instructions-icon">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col-md-11">
                        <p class="step-3">Step 3. Return to Passport and upload your completed document.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h1><?php echo $pdf_title; ?></h1>
                <iframe src="file.php?id=<?php echo $id; ?>" id="clickHandler"></iframe>
            </div>
            <div class="col-md-1">
                <div id="downloadIcon">
                    <i class="fa fa-download" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    
    <?php } ?>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="//apps.emcp.com/editor/js/bootstrap.min.js"></script>
    <script src="//apps.emcp.com/redirects/lib/js/script.js"></script>
    <script src="lib/js/script.js"></script>
    <script>
        $(document).ready(function(){
            $('#clickHandler').iframeTracker({
                blurCallback: function(){
                    savePDF('<?php echo $pdf_filename; ?>', '<?php echo $pdf_title; ?>');
                }
            });
            $('#downloadIcon').on('click', function() {
                 savePDF('<?php echo $pdf_filename; ?>', '<?php echo $pdf_title; ?>');                
            });
            $('.instructions-icon').on('click', function() {
                 savePDF('<?php echo $pdf_filename; ?>', '<?php echo $pdf_title; ?>');  
            });
        });    
    </script>
</body>
</html>
<?php

    date_default_timezone_set('America/Chicago');

    $id = $_GET['id'];
    $type = $_GET['type'];
    $lang = $_GET['lang'];
    $iCultureBase = 'http://www.emcp.com/ilife/iculturenew/'. $type;

    if( $type == 'inews' ) {
        $url = $iCultureBase .'/adnx-user.php?ax=emcp.com/ilife/i&lang='. $lang .'&xid='. $id .'-'. sha1( date('mHdmYmdHY') );
    } else if( $type == 'ipassport' ) {
        $url = $iCultureBase .'/adnx-user.php?ax=emcp.com/ilife/i&lang='. $lang .'&xid='. $id .'-'. sha1( date('mHdmYmdHY') );
    } else if( $type == 'isongs' ) {
        $url = $iCultureBase .'/adnx-user.php?ax=emcp.com/ilife/i&lang='. $lang .'&xid='. $id .'-'. sha1( date('mHdmYmdHY') );
    } else if( $type == 'ivideos' ) {
        $url = $iCultureBase .'/adnx-user.php?ax=emcp.com/ilife/i&lang='. $lang .'&xid='. $id .'-'. sha1( date('mHdmYmdHY') );
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>iCulture | Digital Resource Center</title>

        <link rel="shortcut icon" href="http://apps.emcp.com/resources/img/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="http://apps.emcp.com/resources/css/style.css"/>   
        <link rel="stylesheet" href="http://apps.emcp.com/resources/css/covers.php?id=1" />

        <script async src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script async src="http://apps.emcp.com/resources/js/custom.js"></script>
    </head>
    <body>
        <div class="modalContainer iCultureModal iCultureModal<?php echo $type; ?> iCultureModal<?php echo $lang; ?>">
            <iframe scrolling="no" src="<?php echo $url; ?>" frameborder="0"></iframe>
            <div class="modalClose anim"></div>
        </div>
    </body>
</html>
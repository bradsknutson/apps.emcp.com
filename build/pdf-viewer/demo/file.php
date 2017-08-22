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
    
?>
<html>
<head>
    <link rel="stylesheet" href="/lib/css/style.css?v=1.0">
</head>
<body>
    <object data="<?php echo $pdf_filename; ?>" type="application/pdf">
        <embed src="<?php echo $pdf_filename; ?>" type="application/pdf" />
    </object>
</body>
</html>
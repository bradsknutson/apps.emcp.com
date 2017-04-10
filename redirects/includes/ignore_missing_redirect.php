<?php

    include 'functions.php';

    $book_id = $mysqli->real_escape_string( $_POST['book_id'] );
    $string = $mysqli->real_escape_string( $_POST['string'] );

    $insert = "INSERT INTO dne_exempt (string,book_id)
                    VALUES ('". $string ."','". $book_id ."')";

    $result = $mysqli->query($insert);

    echo '<script>

            $("#successModal").modal("show");
            $(\'.missing-ignore[data-string="'. $string .'"]\').parent().parent().fadeOut(\'slow\');

        </script>';

?>
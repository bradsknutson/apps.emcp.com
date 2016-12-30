<?php

$book = $_POST['id'];

$uploaddir = '/chroot/home/emcpcom/apps.emcp.com/files/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);


// Check filetype
if( $_FILES['userfile']['type'] != 'text/csv' ) {
    
    $form = '<form style="display:none;" name="auto" action="/redirects/links/upload/'. $book .'" method="POST"><input name="noncsv" value="true"></form><script type="text/javascript">window.onload = function() { document.forms[\'auto\'].submit(); }</script>';
    echo $form;
    
} 

// Check filesize
if($_FILES['file_upload']['size'] > 500000){
    $form = '<form style="display:none;" name="auto" action="/redirects/links/upload/'. $book .'" method="POST"><input name="filesize" value="true"></form><script type="text/javascript">window.onload = function() { document.forms[\'auto\'].submit(); }</script>';
    echo $form;
}

// Check if file already exists
if( file_exists( $uploaddir . basename($_FILES['userfile']['name']) ) ) {
    $name = pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME);
    $extension = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
    $increment = '';

    while( file_exists($uploaddir . $name . $increment . '.' . $extension) ) {
        $increment++;
    }

    $basename = $name . $increment . '.' . $extension;
} else {
    $basename = basename($_FILES['userfile']['name']);
}

if( $_FILES['userfile']['type'] == 'text/csv' && $_FILES['file_upload']['size'] <= 500000 ) {

    move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir . $basename );

}


  require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        if( $book != '' ) {

            $title = "SELECT a.title, b.domain, c.sub, a.id
                    FROM book a, root_domains b, sub_domains c
                    WHERE a.id = '". $book ."'
                    AND a.domain_id = b.id
                    AND a.sub_id = c.id";
            
            $title_result = $mysqli->query($title);
            $title_string = $title_result->fetch_assoc();
            $title_result->close();

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Processing</h1>
                            <h2>Links uploaded will be for this book: <?php echo $title_string['title']; ?></h2>
                            <p>Links for this book will use the domain <strong><?php if($title_string['sub'] != '') { echo $title_string['sub'] .'.'; } ?><?php echo $title_string['domain']; ?></strong></p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $title_string['id']; ?>"><?php echo $title_string['title']; ?></a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/links/upload/<?php echo $title_string['id']; ?>">Upload</a></li>
                                <li class="breadcrumb-item active">Processing</li>
                            </ol>
                            <p><a href="http://apps.emcp.com/redirects/links/upload/<?php echo $title_string['id']; ?>"><i class="fa fa-book" aria-hidden="true"></i> Go Back</a></p>
                        </div>
                        <div class="col-md-offset-1 col-md-10">
                            
                            <?php

                                ini_set("auto_detect_line_endings", true);
                                $csv = array_map('str_getcsv', file($uploaddir . $basename));
                                array_shift($csv);
            
                                $length = sizeOf($csv);
            
                            ?>
                        
                            <p>File uploaded: <strong><?php echo $basename; ?></strong>. Number of rows to be imported: <strong><span class="numRows"><?php echo $length; ?></span></strong></p>
                            
                            <p title="Editing Redirects Before Importing" data-content="Before the import, please look over this list of redirects and make sure they are correct. Remove any rows that you do not want to import by clicking the corresponding trash icon. All values in the table below are editable, so take this time to confirm all redirects are correct.  When you're ready, submit the redirects by clicking the Import button at the bottom of the page." data-toggle="popover" data-placement="top" data-trigger="hover click">Edit or remove links before importing <i class="fa fa-question-circle" aria-hidden="true"></i></p>
                                                        
                            <div class="row">
                                <div class="col-md-5 border-bottom">
                                    Redirect String
                                </div>
                                <div class="col-md-5 border-bottom">
                                    Destination URL
                                </div>
                                <div class="col-md-2 border-bottom">
                                    <div>&nbsp;</div>
                                </div>
                            </div>
                            
                            <?php
            
                                foreach($csv as $row) {
                                    echo '<div class="row is-table-row">
                                            <div class="col-md-5 border-cell"><input type="text" id="upload-string" value="'. $row[0] .'"></div>
                                            <div class="col-md-5 border-cell"><input type="text" id="upload-destination" value="'. $row[1] .'"></div>
                                            <div class="col-md-2 border-cell delete-col"><a href="/redirects/"><i class="fa fa-trash" aria-hidden="true"></i></a></div>
                                        </div>';
                                }
            
                            ?>
                            
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron jumbotron-margin-top">
                            <h2>Importing <span class="numRows"><?php echo $length; ?></span> redirects.</h2>
                            <button type="button" class="btn btn-primary btn-lg import-redirects">Import</button>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    
                    $catch = '0';
                    
                    var i = 0;
                    $('.is-table-row').each(function() {
                        i++; 
                        $(this).attr('id',i);
                    });
                    
                    $('.confirm-catch').on('click', function() {

                        $catch = '1';
                        $id = $('.passedID').attr('id');
                        removeLinkFromImport($id);
                        
                    });
                    
                    $('.delete-col a').on('click', function(e) {
                        
                        e.preventDefault(); 
                        
                        $id = $(this).parent().parent().attr('id');
                        
                        if( $catch == '0' ) {
                        
                            $('.passedID').attr('id',$id);
                            $('#removerowModal').modal('show');
                            
                        } else {
                            
                            removeLinkFromImport($id);
                            
                        }
                    });
                    
                    $('.import-redirects').on('click', function() {
                        $('.is-table-row').each(function() {
                            
                            $id = $(this).attr('id');
                            $string = $(this).find('#upload-string').val();
                            $destination = $(this).find('#upload-destination').val();
                            
                            importLink($string,$destination);
                            
                        });
                    });
                        
                    $('.redirect-to-book').on('click', function() {
                        window.location = '/redirects/books/<?php echo $book; ?>'; 
                    });
                    
                });
                
                $(document).ajaxStop(function() {
                     $('#importsuccessModal').modal('show');
                });
                
                function removeLinkFromImport(passedID) {
                    $('.is-table-row#' + passedID).fadeOut(300, function() {
                        $(this).remove(); 
                    });
                    
                    $numRows = $('.is-table-row').length - 1;
                    $('.numRows').html($numRows);
                }
                
                function importLink(s,d) {
                    
                    $.ajax({
                        method: "POST",
                        url: "http://apps.emcp.com/redirects/includes/import_link.php",
                        data: { 
                            id: '<?php echo $book; ?>',
                            string: s,
                            destination: d
                        }
                    }).done(function(data) {
                        $('.import-log').append(data);
                    }); 
                }
                
            </script>
            <div class="error-handling"></div>
            <div class="remove-row-modal">
                <div class="modal fade" id="removerowModal" tabindex="-1" role="dialog" aria-labelledby="removerowModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="removerowModalLabel">Delete redirect?</h2>
                            </div>
                            <div class="modal-body">
                                <h3>You are attempting to remove this redirect from the import.</h3>
                                <p>Please confirm that you want to remove this redirect. This is the last time you will see this warning, and you will be able to remove more redirects from this import without being prompted.  Please note: a deleted row will not be included in the import and the redirect will not be created.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default decline-catch" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-default confirm-catch" data-dismiss="modal">Confirm</button>
                                <div class="passedID"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="import-success-modal">
                <div class="modal fade" id="importsuccessModal" tabindex="-1" role="dialog" aria-labelledby="importsuccessModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="importsuccessModalLabel">Upload complete.</h2>
                            </div>
                            <div class="modal-body">
                                <h3>The import log is below.</h3>
                                <span class="import-log">
                                    <p>&nbsp;</p>
                                </span>
                                <p>After closing this modal, you will be redirected back to the book page with a list of all redirects, where you can edit or generate more.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default redirect-to-book" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php

    } else {

            header('Location: /redirects/');
            
        }

    } 
        

        
        
/*
// Debugging info.
echo '<pre>'. $basename .'</pre>';


echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir . $basename )) {
    echo "Success.\n";
} else {
    echo "Failure.\n";
}
echo '</pre>';

echo '<pre>Book ID = '. $book .'. Here is some more debugging info:';
echo '</pre><pre>';
print_r($_FILES);
echo '</pre>';
*/

?>
<?php

    $book = $_GET['id'];
    $noncsv = $_POST['noncsv'];
    $filesize = $_POST['filesize'];

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
                            <h1>Redirect Import</h1>
                            <h2>Import links for book: <?php echo $title_string['title']; ?></h2>
                            <p>Links for this book use the domain <strong><?php if($title_string['sub'] != '') { echo $title_string['sub'] .'.'; } ?><?php echo $title_string['domain']; ?></strong></p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $title_string['id']; ?>"><?php echo $title_string['title']; ?></a></li>
                                <li class="breadcrumb-item active">Upload</li>
                            </ol>
                            <p><a href="/redirects/books/<?php echo $title_string['id']; ?>"><i class="fa fa-book" aria-hidden="true"></i> Go Back</a> &nbsp; <a href="/redirects/links/upload/redirects-upload-template.csv"><i class="fa fa-download" aria-hidden="true"></i> Download Template</a></p>
                        </div>
                        <div class="col-md-offset-2 col-md-8">
                            
                            <p><strong>Note:</strong> <a href="/redirects/links/upload/redirects-upload-template.csv">Download the template</a>, add your redirect strings and destination URLs, and save as a .csv file.  If you have questions, please take a look at <a href="/redirects/links/upload/example_upload.csv">this example file</a> or contact Web Dev.</p>
                            <p>&nbsp;</p>
                        
                            <form enctype="multipart/form-data" action="/redirects/links/upload/process.php" method="POST">
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="hidden" name="MAX_FILE_SIZE" value="500000" /><input name="userfile" type="file" style="display: none;">
                                        </span>
                                    </label>
                                    <input type="hidden" name="id" value="<?php echo $book; ?>">
                                    <input type="text" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default">Import</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    
                    $(':submit').hide();
                    
                    $(document).on('keyup keypress', function(e) {
                        var keyCode = e.keyCode || e.which;
                        if( keyCode === 13 ) {
                            e.preventDefault();
                            return false;
                        }
                    });
                    
                    $(document).on('change', ':file', function() {
                        var input = $(this),
                            numFiles = input.get(0).files ? input.get(0).files.length : 1,
                            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                        input.trigger('fileselect', [numFiles, label]);
                    });
                 
                    $(':file').on('fileselect', function(event, numFiles, label) {
                        var input = $(this).parents('.input-group').find(':text'),
                        log = label;
                        
                        var extension = label.replace(/^.*\./, '');
                        if( extension != 'csv' ) {
                            $('#noncsvModal').modal('show');
                            $(':submit').hide();
                        } else {
                            $(':submit').fadeIn();
                        }

                        if( input.length ) {
                            input.val(log);
                        } else {
                            if( log ) {
                                alert(log);
                            }
                        }
                    });
                    
                    <?php
                        if( $noncsv != '' ) {
                            echo '$("#noncsvModal").modal("show");';
                        }
                        if( $filesize != '' ) {
                            echo '$("#filesizeModal").modal("show");';
                        }
                    ?>
                    
                });
                
                function clearForm() {
                    console.log('Form cleared');
                    $('form')[0].reset();
                }
            </script>
            <div class="error-handling"></div>
            <div class="non-csv-modal">
                <div class="modal fade" id="noncsvModal" tabindex="-1" role="dialog" aria-labelledby="noncsvModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="noncsvModalLabel">File type not accepted.</h2>
                            </div>
                            <div class="modal-body">
                                <h3>Only .csv formatted files are allowed.</h3>
                                <p>Please format your upload into a standard .csv file before importing it.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clearForm()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="file-size-modal">
                <div class="modal fade" id="filesizeModal" tabindex="-1" role="dialog" aria-labelledby="filesizeModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="filesizeModalLabel">File size limit exceeded.</h2>
                            </div>
                            <div class="modal-body">
                                <h3>The file size limit is 500 KB.</h3>
                                <p>If you have a large import file, please contact Web Dev to manually perform the import.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clearForm()" >Close</button>
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

?>

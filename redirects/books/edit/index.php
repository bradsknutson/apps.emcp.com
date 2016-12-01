<?php

    $id = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        $domain = "SELECT *
                FROM root_domains";
        
        $domain_result = $mysqli->query($domain);
        
        $sub = "SELECT *
                FROM sub_domains";
        
        $sub_result = $mysqli->query($sub);
        
        $book = "SELECT a.id, a.title, b.domain, b.id AS domain_id, c.sub, c.id AS sub_id
                FROM book a, root_domains b, sub_domains c
                WHERE a.id = '". $id ."'
                AND a.domain_id = b.id
                AND a.sub_id = c.id";
        
        $book_result = $mysqli->query($book);
        $book_string = $book_result->fetch_assoc();
        $book_result->close();
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Editing Book:</h1>
                        <h2><?php echo $book_string['title']; ?></h2>
                        <p>Edit the details below for this book.</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $book_string['id']; ?>"><?php echo $book_string['title']; ?></a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                        <p>Note: If you do not see the domain or subdomain in the list below, go back to the <a href="http://apps.emcp.com/redirects/">Home</a> page and create a new domain first.</p>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="destination-value" class="col-md-3 control-label">Book Name</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="name-value" value="<?php echo $book_string['title']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label">Domain</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="domain-choice">
                                        <option value="">Choose Domain</option>
                                        <?php
                                        
                                            while($row = $domain_result->fetch_assoc()) {
                                                echo '<option value="'. $row['id'] .'">'. $row['domain'] .'</option>';
                                            }
        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label">Sub Domain (Optional)</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="sub-choice">
                                        <option value="">Choose Sub Domain</option>
                                        <?php
                                        
                                            while($row = $sub_result->fetch_assoc()) {
                                                
                                                if( $row['sub'] == '' ) {
                                                    $sub_domain = 'Root Domain';
                                                } else {
                                                    $sub_domain = $row['sub'];
                                                }
                                                
                                                echo '<option value="'. $row['id'] .'">'. $sub_domain .'</option>';
                                            }
        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="domain-pre-selected" id="<?php echo $book_string['domain_id']; ?>"></div>
                            <div class="sub-pre-selected" id="<?php echo $book_string['sub_id']; ?>"></div>
                            
                            <div class="form-group">
                                <div class="col-md-offset-1">
                                    <button type="submit" class="btn btn-default">Save Book</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>                    
            <div class="col-sm-12 col-md-3"></div>
        </div>
        <script>
            $(document).ready(function() {
                
                $warning = '0';
                $domain_id = $('.domain-pre-selected').attr('id');
                $sub_id = $('.sub-pre-selected').attr('id');
                
                $('#domain-choice option[value=' + $domain_id + ']').attr('selected','selected');
                $('#sub-choice option[value=' + $sub_id + ']').attr('selected','selected');
                
                $('.close-modal').click(function() {
                    window.location.replace("http://apps.emcp.com/redirects/books/<?php echo $book_string['id']; ?>"); 
                });
                
                $('#name-value').keyup(function() {
                    if( $(this).val() != '' ) {
                        if( $('#domain-choice').val() != '' ) {
                            $('form button[type="submit"]').removeAttr('disabled');
                        } else {
                            $('form button[type="submit"]').attr('disabled','disabled');
                        }
                    } else {
                        $('form button[type="submit"]').attr('disabled','disabled');
                    }
                });                    
                
                $('#sub-choice').change(function() {    
                    if( $('#sub-choice').val() != $sub_id || $('#domain-choice').val() != $domain_id ) {
                        $warning = '1';
                    } else {
                        $warning = '0';
                    }
                });
                
                $('#domain-choice').change(function() {
                    if( $(this).val() != '' ) {
                        if( $('#name-value').val() != '' ) {
                            $('form button[type="submit"]').removeAttr('disabled');
                        } else {
                            $('form button[type="submit"]').attr('disabled','disabled');
                        }
                    } else {
                        $('form button[type="submit"]').attr('disabled','disabled');
                    }
                       
                    if( $('#sub-choice').val() != $sub_id || $('#domain-choice').val() != $domain_id ) {
                        $warning = '1';
                    } else {
                        $warning = '0';
                    }
                    
                });
                    
                $('.save-anyways').click(function() { 
                    $warning = '0';
                    $('#warningModal').modal('hide');
                    $('form').submit();
                });
                $('.go-back').click(function() { 
                    $warning = '1';
                });
                
                $('form').submit(function(e) {
                    
                    e.preventDefault();
                    
                    if( $warning == '0' ) {
                        
                        $title = $('#name-value').val();
                        $did = $('#domain-choice').val();
                        $sid = $('#sub-choice').val();
                        
                        $.ajax({
                            method: "POST",
                            url: "http://apps.emcp.com/redirects/includes/update_book.php",
                            data: { 
                                id: <?php echo $id ?>,
                                title: $title,
                                domain_id: $did,
                                sub_id: $sid
                            }
                        }).done(function(data) {
                            $('.error-handling').html(data);
                        }); 
                        
                    } else {
                        $('#warningModal').modal('show');
                    }
                    
                    
                });
                
            });
        </script>
        <div class="error-handling transition"></div>

        <div class="warning-modal">
            <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content panel-warning">
                        <div class="modal-header panel-heading">
                            <h4 class="modal-title">Are you sure?</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure your changes are correct?  Changing the domain or subdomain can break any existing redirects in use for this book.  The domain or subdomain should only be modified before a book has been published.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default go-back" data-dismiss="modal">Go Back</button>
                            <button type="button" class="btn btn-primary save-anyways">Save Anyways</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>

        <div class="success-modal">
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h2 class="modal-title" id="myModalLabel">Excellent.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>This book has been updated.</h3>
                            <p>If you want to create another book, you can do so by <a href="/redirects/books/new/">clicking here</a>.</p>
                            <p>Closing this modal will bring you back to the book maintenance page.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default close-modal" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
        
    }

    $domain_result->close();
    $sub_result->close();

?>

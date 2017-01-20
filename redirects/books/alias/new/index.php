<?php

    $id = $_GET['id'];

    require '../../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../../includes/header.php';
        
        $book = "SELECT a.id, a.title, b.domain, c.sub
                FROM book a, root_domains b, sub_domains c
                WHERE a.id = '". $id ."'
                AND a.sub_id = c.id
                AND a.domain_id = b.id";
        $book_result = $mysqli->query($book);
        $book_info = $book_result->fetch_assoc();
        
        $domain = "SELECT *
                FROM root_domains
                ORDER BY domain ASC";
        
        $domain_result = $mysqli->query($domain);
        
        $sub = "SELECT *
                FROM sub_domains
                ORDER BY sub ASC";
        
        $sub_result = $mysqli->query($sub);
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Create Alias</h1>
                        <p>This alias will be for the book <strong><?php echo $book_info['title']; ?></strong></p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $book_info['id']; ?>"><?php echo $book_info['title']; ?></a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/alias/<?php echo $book_info['id']; ?>">Aliases</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label" title="Domain" data-content="Select the domain you wish to use for this alias." data-toggle="popover" data-placement="top" data-trigger="hover click">Domain <i class="fa fa-info-circle help-book-domain" aria-hidden="true"></i></label>
                                <div class="col-md-9">
                                    <select class="form-control" id="domain-choice">
                                        <option value="default">Choose Domain</option>
                                        <?php
                                        
                                            while($row = $domain_result->fetch_assoc()) {
                                                echo '<option value="'. $row['id'] .'">'. $row['domain'] .'</option>';
                                            }
        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label" title="Sub Domain" data-content="Select the sub domain you wish to wish to use for this alias. If you do not want a sub domain, choose 'Root Domain'" data-toggle="popover" data-placement="top" data-trigger="hover click">Sub Domain <i class="fa fa-info-circle help-book-sub" aria-hidden="true"></i></label>
                                <div class="col-md-9">
                                    <select class="form-control" id="sub-choice">
                                        <option value="default">Choose Sub Domain</option>
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
                            <div class="col-md-offset-1">
                                <button type="submit" class="btn btn-default">Submit</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>                    
            <div class="col-sm-12 col-md-3"></div>
        </div>
        <script>
            $(document).ready(function() {
                
                $('form button[type=submit]').attr('disabled','disabled');
                
                $(document).on('change','select#domain-choice',function() {
                    $dom_id = $(this).find('option:selected').val();
                    
                    if( $dom_id == '1' ) {
                        $('select#domain-choice option[value=3]').hide();
                    } else {
                        $('select#domain-choice option[value=3]').show();                        
                    }
                    
                    if( $('select#domain-choice option:selected').val() != 'default' ) {
                        if( $('select#sub-choice option:selected').val() != 'default' ) {
                            $('form button[type=submit]').removeAttr('disabled');        
                        } else {
                            $('form button[type=submit]').attr('disabled','disabled'); 
                        }
                    } else {
                        $('form button[type=submit]').attr('disabled','disabled');
                    }
                });
                $(document).on('change','select#sub-choice',function() {
                    $sub_id = $(this).find('option:selected').val();
                    
                    if( $('select#domain-choice option:selected').val() != 'default' ) {
                        if( $('select#sub-choice option:selected').val() != 'default' ) {
                            $('form button[type=submit]').removeAttr('disabled');        
                        } else {
                            $('form button[type=submit]').attr('disabled','disabled'); 
                        }
                    } else {
                        $('form button[type=submit]').attr('disabled','disabled');
                    }
                });
                
                $('.clear-form').click(function() {
                    $('select').val('');
                    $('form button[type=submit]').attr('disabled','disabled');
                });

                $('form').submit(function(e) {
                    
                    e.preventDefault();
                    
                    $.ajax({
                        method: "POST",
                        url: "http://apps.emcp.com/redirects/includes/create_book_alias.php",
                        data: { 
                            book_id: '<?php echo $id; ?>',
                            domain_id: $('select#domain-choice option:selected').val(),
                            sub_id: $('select#sub-choice option:selected').val()
                        }
                    }).done(function(data) {
                        $('.error-handling').html(data);
                    });
                    
                });
                
                $(document).on('click','.redirect-to-alias',function() {
                    window.location = '/redirects/books/alias/<?php echo $id; ?>'    
                });
                
            });
        </script>
        <div class="error-handling transition"></div>
        <div class="success-modal">
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h2 class="modal-title" id="myModalLabel">Great!</h2>
                        </div>
                        <div class="modal-body">
                            <h3>Your new alias has been created.</h3>
                            <p>Closing this modal will bring you to the alias management page.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default clear-form redirect-to-alias" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="duplicate-modal">
            <div class="modal fade" id="duplicateBookModal" tabindex="-1" role="dialog" aria-labelledby="duplicateBookLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="clear-form">&times;</span>
                            </button>
                            <h2 class="modal-title" id="duplicateBookLabel">Hold On.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>A book with this domain already exists.</h3>
                            <p>You are attempting to create an alias, but the combination of this domain and sub domain already exists for a book.  <a class="alias-book-exists" href="/redirects/books/">Clicking here</a> to edit the book.</p>
                            <p>To instead create another alias, close this modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default clear-form" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="duplicate-alias-modal">
            <div class="modal fade" id="duplicateAliasModal" tabindex="-1" role="dialog" aria-labelledby="duplicateAliasModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="clear-form">&times;</span>
                            </button>
                            <h2 class="modal-title" id="duplicateAliasModalLabel">Oops.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>This alias already exists.</h3>
                            <p>This combination of domain and sub domain already exist as an alias. If you want to modify it, you can manage this alias by clicking here <a class="alias-exists" href="/redirects/books/alias/">clicking here</a>.</p>
                            <p>To instead create another alias, close this modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default clear-form" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
        
    }

?>

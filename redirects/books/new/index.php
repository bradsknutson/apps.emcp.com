<?php

    $id = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        $domain = "SELECT *
                FROM root_domains";
        
        $domain_result = $mysqli->query($domain);
        
        $sub = "SELECT *
                FROM sub_domains";
        
        $sub_result = $mysqli->query($sub);
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Create a new book.</h1>
                        <p>Choose a domain and sub-domain (optional) for your book.</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                        <p>Note: If you do not see the domain or subdomain in the list below, go back to the <a href="http://apps.emcp.com/redirects/">Home</a> page and create a new domain first.</p>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="destination-value" class="col-md-3 control-label" title="Book Name" data-content="The Book Name is a user-friendly internal only value used for organizational purposes." data-toggle="popover" data-placement="top" data-trigger="hover click">Book Name <i class="fa fa-info-circle help-book-name" aria-hidden="true"></i></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="name-value" value="">
                                </div>
                            </div>                            
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label" title="Domain" data-content="Select the domain for which you want the book to use for redirects. For Paradigm products, paradgigmeducation.com is usually the correct domain." data-toggle="popover" data-placement="top" data-trigger="hover click">Domain <i class="fa fa-info-circle help-book-domain" aria-hidden="true"></i></label>
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
                                <label for="string-value" class="col-md-3 control-label" title="Sub Domain" data-content="Select the sub domain you wish to appear in front of the domain to be used in redirects.  For example, if you wish to use ODW4.paradigmeducation.com for your redirects, choose the ODW4 sub domain from this dropdown." data-toggle="popover" data-placement="top" data-trigger="hover click">Sub Domain <i class="fa fa-info-circle help-book-sub" aria-hidden="true"></i></label>
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
                            <div class="form-group">
                                <label for="default-url" class="col-md-3 control-label" title="Default URL" data-content="This is the default URL for redirects for this book that do not exist.  For example, if someone were to mistype URL for this domain and subdomain combination, this is the page you want them to end up on. Consider it a fallback URL. Typically a marketing or product page for the book." data-toggle="popover" data-placement="top" data-trigger="hover click">Default URL <i class="fa fa-info-circle help-book-sub" aria-hidden="true"></i></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="default-url" value="">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-offset-1">
                                    <button type="submit" class="btn btn-default" disabled="disabled">Create Book</button>
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
                
                $('#name-value').keyup(function() {
                    if( $(this).val() != '' ) {
                        if( $('#domain-choice').val() != '' ) {
                            if( $('#sub-choice').val() != '' ) {
                                if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#default-url").val())){
                                    $('form button[type="submit"]').removeAttr('disabled');
                                } else {
                                    $('form button[type="submit"]').attr('disabled','disabled'); 
                                }
                            } else {
                                $('form button[type="submit"]').attr('disabled','disabled');
                            }
                        } else {
                            $('form button[type="submit"]').attr('disabled','disabled');
                        }
                    } else {
                        $('form button[type="submit"]').attr('disabled','disabled');
                    }
                });
                
                $('#domain-choice').change(function() {
                    if( $(this).val() != '' ) {
                        if( $('#name-value').val() != '' ) {
                            if( $('#sub-choice').val() != '' ) {
                                if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#default-url").val())){
                                    $('form button[type="submit"]').removeAttr('disabled');
                                } else {
                                    $('form button[type="submit"]').attr('disabled','disabled'); 
                                }
                            } else {
                               $('form button[type="submit"]').attr('disabled','disabled'); 
                            }
                        } else {
                            $('form button[type="submit"]').attr('disabled','disabled');
                        }
                    } else {
                        $('form button[type="submit"]').attr('disabled','disabled');
                    }
                });
                
                $('#sub-choice').change(function() {
                    if( $(this).val() != '' ) {
                        if( $('#name-value').val() != '' ) {
                            if( $('#domain-choice').val() != '' ) {
                                if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#default-url").val())){
                                    $('form button[type="submit"]').removeAttr('disabled');
                                } else {
                                    $('form button[type="submit"]').attr('disabled','disabled'); 
                                }
                            } else {
                               $('form button[type="submit"]').attr('disabled','disabled'); 
                            }
                        } else {
                            $('form button[type="submit"]').attr('disabled','disabled');
                        }
                    } else {
                        $('form button[type="submit"]').attr('disabled','disabled');
                    }
                });
                
                $("#default-url").keyup(function() {
                    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#default-url").val())){
                        if( $('#domain-choice').val() != '' ) {
                            if( $('#name-value').val() != '' ) {
                                $('form button[type=submit]').removeAttr('disabled');
                            } else {
                                $('form button[type=submit]').attr('disabled','disabled');
                            }
                        } else {
                            $('form button[type=submit]').attr('disabled','disabled');
                        }
                    } else {             
                        $('form button[type=submit]').attr('disabled','disabled');
                    }                    
                });                
                    
                $('form').submit(function(e) {
                    
                    e.preventDefault();
                    
                    $title = $('#name-value').val();
                    $domain_id = $('#domain-choice').val();
                    $sub_id = $('#sub-choice').val();
                    $default_url = $('#default-url').val();
                    
                    $.ajax({
                        method: "POST",
                        url: "http://apps.emcp.com/redirects/includes/create_book.php",
                        data: { 
                            title: $title,
                            domain_id: $domain_id,
                            sub_id: $sub_id,
                            default_url: $default_url
                        }
                    }).done(function(data) {
                        $('.error-handling').html(data);
                    }); 
                                        
                    
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
                            <h2 class="modal-title" id="myModalLabel">Excellent.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>Your new book has been created.</h3>
                            <p>If you would like to manage your new book or create links for it, you can do so by <a class="success-anchor" href="">clicking here</a>.</p>
                            <p>If you want to create another book, you can do so by closing this modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Ignore</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="duplicate-modal">
            <div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h2 class="modal-title" id="myModalLabel">Oops.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>The book you are attempting to create already exists.</h3>
                            <p>If you would like to manage this book or any of it's links, you can do so by <a class="fail-anchor" href="">clicking here</a>.</p>
                            <p>If you want to create another book, you can do so by closing this modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Ignore</button>
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

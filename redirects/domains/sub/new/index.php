<?php

    $id = $_GET['id'];

    require '../../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../../includes/header.php';
        
        $sub = "SELECT *
                FROM sub_domains";
        
        $sub_result = $mysqli->query($sub);
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Create a new subdomain.</h1>
                        <p>Fill out the information below to create a new subdomain.</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/domains/">Domains</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/domains/sub/">Subdomains</a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                        <p>Note: Do not include the domain, this page is only for creating new subdomains.  You will tie a subdomain to a domain when you <a href="/redirects/books/new">create a new book</a>.</p>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="destination-value" class="col-md-3 control-label">Subomain</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="name-value" value="">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-offset-1">
                                    <button type="submit" class="btn btn-default" disabled="disabled">Create Subomain</button>
                                </div>
                            </div>
                        </form>
                        
                        <a type="button" class="btn btn-success btn-lg btn-block create-new-sub" href="/redirects/domains/new/">Create New Domain</a>
                    </div>
                </div>
            </div>                    
            <div class="col-sm-12 col-md-3"></div>
        </div>
        <script>
            $(document).ready(function() {
                
                $('#name-value').keyup(function() {
                    
                    $str = $(this).val();

                    if( $str.match(/http/gi) ){
                        $replaced = $('#name-value').val().replace(/http/gi,'');
                        $('#name-value').val($replaced);
                    }
                    if( $str.match(/www./gi) ){
                        $replaced = $('#name-value').val().replace(/www./gi,'');
                        $('#name-value').val($replaced);
                    }            
                    if( $str.match(/[^a-zA-Z0-9_-]+/g) ){
                        $replaced = $('#name-value').val().replace(/[^a-zA-Z0-9_-]+/g,'');
                        $('#name-value').val($replaced);
                    }
                    
                    if(/\./.test($str)) {
                        $replaced = $('#name-value').val().replace(/./g,'');
                        $('form button[type="submit"]').attr('disabled','disabled');
                        
                        $('#myModal').modal('show');
                    }
                    
                    if( $str != '' ) {
                        
                        $('form button[type="submit"]').removeAttr('disabled');
                        
                    } else {
                        $('form button[type="submit"]').attr('disabled','disabled');
                    }
                });

                $('form').submit(function(e) {
                    
                    e.preventDefault();
                    
                    $domain = $('#name-value').val();
                    
                        $.ajax({
                            method: "POST",
                            url: "http://apps.emcp.com/redirects/includes/create_sub.php",
                            data: { 
                                domain: $domain
                            }
                        }).done(function(data) {
                            $('.error-handling').html(data);
                        }); 
                    
                });
                
            });
        </script>
        <div class="error-handling transition"></div>
        <div class="sub-domain-error-modal">
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h2 class="modal-title" id="myModalLabel">Hang on a second.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>This tool is for creating subdomains only.</h3>
                            <p>This tool only supports one level of subdomains.  DTP16 is acceptable, but DTP.16 is not. This page is only for creating a single subdomain.  Examples include "DTP16", "DTC", "MSO16" and others.  No root domains are allowed on this page.</p>
                            <p>It's possible you are attempting to associate a subdomain with a root domain, in which case you should be creating a new book.  If you would like to create a new book, you can do so <a href="/redirects/books/new/">by clicking here</a>.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Ignore</button>
                        </div>
                    </div>
                </div>
            </div>
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
                            <h3>Your domain has been created.</h3>
                            <p>If you would like to manage your new domain, you can do so by <a class="success-anchor" href="">clicking here</a>.</p>
                            <p>If you want to create another domain, you can do so by closing this modal.</p>
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
                            <h3>The domain you are attempting to create already exists.</h3>
                            <p>If you would like to manage this domain, you can do so by <a class="fail-anchor" href="">clicking here</a>.</p>
                            <p>If you want to create another domain, you can do so by closing this modal.</p>
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

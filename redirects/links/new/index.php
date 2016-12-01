<?php

    $id = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        $book = "SELECT a.id, a.title, b.domain, c.sub
                FROM book a, root_domains b, sub_domains c
                WHERE a.id = '". $id ."'
                AND a.sub_id = c.id
                AND a.domain_id = b.id";
        $book_result = $mysqli->query($book);
        $book_info = $book_result->fetch_assoc();
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Link Editor</h1>
                        <p>Link will use this domain: <?php if($book_info['sub'] != '') { echo $book_info['sub'] .'.'; } ?><?php echo $book_info['domain']; ?>/{redirect will go here}</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $book_info['id']; ?>"><?php echo $book_info['title']; ?></a></li>
                            <li class="breadcrumb-item active">New</li>
                        </ol>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label">Redirect String <i class="fa fa-info-circle help-redirect-string" aria-hidden="true"></i>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="string-value" value="">
                                </div>
                            </div>
                            
                            <div class="help-text-redirect-string">
                                <p>The <strong>Redirect String</strong> is the string of text that appears at the <strong>end</strong> of the URL. <?php if($book_info['sub'] != '') { echo $book_info['sub'] .'.'; } ?><?php echo $book_info['domain']; ?>/<strong>{redirect will go here}</strong></p>
                                <p>Do not enter http:// or www. Do not enter the subdomain or domain.  Only enter the string at the end of the redirect URL.</p>
                                <p>No spaces, periods or # symbols are allowed.  Alphanumerical characters and slashes only.</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="destination-value" class="col-md-3 control-label">Destination URL <i class="fa fa-info-circle help-destination-url" aria-hidden="true"></i>
</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="destination-value" value="">
                                </div>
                            </div>
                            
                            <div class="help-text-destination-url">
                                <p>The destination URL is the web page that you want the link to go to.  This is usually a OneDrive URL, Google Drive, an internally hosted web page, or an external web page.</p>
                                <p>Please <strong>do include</strong> http:// at the beginning of the link.</p>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-offset-1">
                                    <button type="submit" class="btn btn-default" disabled="disabled">Generate Redirect</button>
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
                
                $('.clear-form').click(function() {
                    $('input').val('');
                    $('form button[type=submit]').attr('disabled','disabled');
                });
                
                $("#destination-value").keyup(function() {
                    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#destination-value").val())){
                        $('form button[type=submit]').removeAttr('disabled');
                    } else {
                        $('form button[type=submit]').attr('disabled','disabled');
                    }                    
                });

                $('form').submit(function(e) {
                    
                    e.preventDefault();
                    
                    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#destination-value").val())){
                    
                        $.ajax({
                            method: "POST",
                            url: "http://apps.emcp.com/redirects/includes/create_link.php",
                            data: { 
                                destination: $('#destination-value').val(),
                                id: '<?php echo $id; ?>',
                                string: $('#string-value').val()
                            }
                        }).done(function(data) {
                            $('.error-handling').html(data);
                        }); 
                        
                    }
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
                            <h3>Your new redirect has been created.</h3>
                            <p>If you would like to edit redirects for this book, you can do so by <a href="/redirects/books/<?php echo $id; ?>">clicking here</a>.</p>
                            <p>To create another redirect, close this modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default clear-form" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="error-character-modal">
            <div class="modal fade" id="characterErrorModal" tabindex="-1" role="dialog" aria-labelledby="errorCharacterModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h2 class="modal-title" id="errorCharacterModalLabel">Hang on a second.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>Special characters are not allowed.</h3>
                            <p>Redirects cannot be created with periods (.) hashtags (#) or spaces.</p>
                            <p>We've removed this character for you and you can continue creating your redirect.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

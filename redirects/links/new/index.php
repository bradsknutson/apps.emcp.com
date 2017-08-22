<?php

    $id = $_GET['id'];
    $string = $_POST['string'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
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
                        <h1>Redirect Generator</h1>
                        <p>This URL:<br />
                            <?php if($book_info['sub'] != '') { echo $book_info['sub'] .'.'; } ?><?php echo $book_info['domain']; ?>/<span class="redirect-goes-here"><?php if( $string != '' ) { echo '<strong>'. $string .'</strong>'; } else { echo '{redirect will go here}'; } ?></span></p>
                        <p>Will redirect to:<br />
                            <strong><span class="destination-goes-here">&nbsp;</span></strong></p>
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
                                <label for="string-value" class="col-md-3 control-label" title="Redirect String" data-content="This is the part of the URL that is displayed after the sub domain and domain." data-toggle="popover" data-placement="top" data-trigger="hover click">Redirect String <i class="fa fa-info-circle" aria-hidden="true"></i></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="string-value" value="<?php echo $string; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="destination-value" class="col-md-3 control-label" title="Destination URL" data-content="The Destination URL is the web page that the user will see when clicking on our redirected link.  This is the final destination, and the content we want the end user to actually see. http:// is required." data-toggle="popover" data-placement="top" data-trigger="hover click">Destination URL <i class="fa fa-info-circle help-destination-url" aria-hidden="true"></i></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="destination-value" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-1">
                                    <button type="submit" class="btn btn-default" disabled="disabled">Generate Redirect</button>
                                    <button class="btn btn-default btn-tester" disabled="disabled">Test Destination</button>
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
                
                $('#string-value').keyup(function() {
                    $current = '<strong>' + $(this).val() + '</strong>';
                    
                    if( $(this).val() == '' ) {
                        $current = '{redirect will go here}';
                    }
                    
                    $('.redirect-goes-here').html($current);
                    
                });
                
                $("#destination-value").keyup(function() {
                    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#destination-value").val())){
                        $('form button[type=submit]').removeAttr('disabled');
                        $('.btn-tester').removeAttr('disabled');
                        $('.destination-goes-here').html( $(this).val() );
                        
                    } else {
                        $('form button[type=submit]').attr('disabled','disabled');
                        $('.btn-tester').attr('disabled','disabled');
                        $('.destination-goes-here').html( '&nbsp;' );
                    }                    
                });

                $(document).on('click', '.btn-tester', function(e) {
                    $url = $('#destination-value').val();
                    
                    window.open($url);
                    
                    e.preventDefault();
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
        <div class="error-character-modal">
            <div class="modal fade" id="dupModal" tabindex="-1" role="dialog" aria-labelledby="dupModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h2 class="modal-title" id="dupModalLabel">Duplicate Warning.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>It looks like this redirect already exists for this book.</h3>
                            <p>You can manage it by <a class="link-duplicate-catch" href="">clicking here</a>, or you can close this modal and go back and edit the redirect string to create a new link.</p>
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

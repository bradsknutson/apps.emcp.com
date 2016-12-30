<?php

    $id = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        $link = "SELECT a.string, b.domain, c.sub, a.book_id, a.destination, d.title
                FROM redirects a, root_domains b, sub_domains c, book d
                WHERE a.id = '". $id ."'
                AND a.book_id = d.id
                AND d.domain_id = b.id
                AND d.sub_id = c.id";
        $link_result = $mysqli->query($link);
        $link_info = $link_result->fetch_assoc();
        $link_result->close();
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Redirect Editor</h1>
                        <p><?php if($link_info['sub'] != '') { echo $link_info['sub'] .'.'; } ?><?php echo $link_info['domain']; ?>/<?php echo $link_info['string']; ?></p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $link_info['book_id']; ?>"><?php echo $link_info['title']; ?></a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="string-value" class="col-md-3 control-label">Redirect String</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="string-value" value="<?php echo $link_info['string']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="destination-value" class="col-md-3 control-label">Destination URL</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="destination-value" value="<?php echo $link_info['destination']; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-offset-1">
                                    <button type="submit" class="btn btn-default">Save</button>
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
                
                $('.close-modal').click(function() {
                    window.location.replace("http://apps.emcp.com/redirects/books/<?php echo $link_info['book_id']; ?>"); 
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
                            url: "http://apps.emcp.com/redirects/includes/update_link.php",
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
                            <h2 class="modal-title" id="myModalLabel">Success.</h2>
                        </div>
                        <div class="modal-body">
                            <h3>This redirect has been updated.</h3>
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

?>

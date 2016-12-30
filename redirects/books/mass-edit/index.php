<?php

    $book = $_GET['id'];

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
                            <h1>Bulk Redirect Editor</h1>
                            <h2>Book: <?php echo $title_string['title']; ?></h2>
                            <p>Links for this book use the domain <strong><?php if($title_string['sub'] != '') { echo $title_string['sub'] .'.'; } ?><?php echo $title_string['domain']; ?></strong></p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/<?php echo $title_string['id']; ?>"><?php echo $title_string['title']; ?></a></li>
                                <li class="breadcrumb-item active">Bulk Edit</li>
                            </ol>
                            <p><a href="/redirects/books/<?php echo $title_string['id']; ?>"><i class="fa fa-book" aria-hidden="true"></i> Go Back</a>&nbsp;&nbsp;&nbsp;<a href="/redirects/books/edit/<?php echo $title_string['id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Book</a></p>
                        </div>
                        <div class="row">
                            <a class="btn btn-success btn-lg btn-block create-new-link" href="http://apps.emcp.com/redirects/links/new/<?php echo $title_string['id']; ?>">Create New Link</a>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-1"></div>
                    <div class="col-sm-12 col-md-10">
                        <div class="row">
                            <div class="col-md-3 border-bottom">
                                Redirect String
                            </div>
                            <div class="col-md-7 border-bottom">
                                Destination URL
                            </div>
                            <div class="col-md-1 border-bottom">
                                <div class="status-start">Status</div>
                            </div>
                            <div class="col-md-1 border-bottom">
                                <div>&nbsp;</div>
                            </div>
                        </div>
                        <?php

                            $link = "SELECT * FROM redirects
                                    WHERE book_id = '". $book ."'
                                    AND deleted = '0'
                                    ORDER BY string ASC";
                            $link_result = $mysqli->query($link);

                            while($row = $link_result->fetch_assoc()) {

                                echo '<div class="row is-table-row">
                                    <div class="col-md-3 border-cell">
                                        <input type="text" id="redirect-'. $row['id'] .'" value="'. $row['string'] .'" />
                                    </div>
                                    <div class="col-md-7 border-cell">
                                        <input type="text" id="destination-'. $row['id'] .'" value="'. $row['destination'] .'" />
                                    </div>
                                    <div class="col-md-1 border-cell status-check status-check-'. $row['id'] .'" id="'. $row['destination'] .'">
                                        <div class="response-code">&nbsp;</div>
                                    </div>
                                    <div class="col-md-1 border-cell">
                                        <a class="save-link" id="'. $row['id'] .'" href="/redirects/links/edit/'. $row['id'] .'" data-string="'. $row['string'] .'" data-destination="'. $row['destination'] .'"><i class="fa fa-save" aria-hidden="true"></i></a>
                                    </div>
                                </div>';
                            }

                            $link_result->close();

                        ?>
                    </div>
                    <div class="col-sm-12 col-md-1"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    
                    $('.status-start').on('click', function() {
                        $('.status-check').each(function() {

                            $destination_url = $(this).attr('id');
                            $redirect_id = $(this).attr('class').split(' ')[3].split('-')[2];

                            ajaxCall($destination_url,$redirect_id);

                        });
                    });
                    
                    
                    $('.save-link, .update-all').each(function() {
                        $(this).hide();
                    })
                    
                    $('input').on('keyup', function() {
                        
                        if( $(this).val().indexOf(' ')>=0 ) {
                            $val = $(this).val();
                            $val = $val.replace(/ /g,'');
                            $(this).val($val);
                        }
                        
                        $id = $(this).attr('id').split('-')[1];
                        
                        if( $('#destination-' + $id).val() != $('.save-link#' + $id).attr('data-destination') || $('#redirect-' + $id).val() != $('.save-link#' + $id).attr('data-string') ) {
                            if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#destination-" + $id).val())) {
                                $('.save-link#' + $id).fadeIn().addClass('saveActive');
                            }
                        } else {
                            $('.save-link#' + $id).fadeOut().removeClass('saveActive');
                        }
                        
                        saveIconCheck();
                        
                    });
                    
                    $('.save-link').on('click', function(e) {
                       
                        $id = $(this).attr('id');
                        $string = $('#redirect-' + $id).val();
                        $destination = $('#destination-' + $id).val();
                        
                        updateLink($id,$string,$destination);
                        
                        e.preventDefault();
                        
                    });
                    
                    $('.update-all').on('click', function() {
                        $('.saveActive').each(function() {
                            
                            $id = $(this).attr('id');
                            $string = $('#redirect-' + $id).val();
                            $destination = $('#destination-' + $id).val();
                            
                            updateLink($id,$string,$destination);
                            
                            $('.update-all').fadeOut();
                            
                        }); 
                    });
                    
                    function ajaxCall($url,$id) {
                    
                        $.ajax({
                            method: "POST",
                            url: "http://apps.emcp.com/redirects/includes/status.php",
                            async: true,
                            data: { url: $url }
                        }).done(function(data) {
                            $('.status-check-' + $id).find('.response-code').text(data);
                        }); 
                    
                    }
                    
                });
            </script>
            <div class="update-all">
                <i class="fa fa-save" aria-hidden="true"></i> Update All
            </div>
            <div class="error-handling"></div>
            <div class="delete-link-modal">
                <div class="modal fade" id="deleteLinkModal" tabindex="-1" role="dialog" aria-labelledby="deleteLinkModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="deleteLinkModalLabel">Caution.</h2>
                            </div>
                            <div class="modal-body">
                                <h3>You are attempting to delete a redirect.</h3>
                                <p>If you wish to delete a redirect, click the confirm button below.  This action <strong>cannot be undone</strong> and if a link is deleted in error it will need to be manually recreated.</p>
                                <p>If you clicked delete in error, click cancel to leave the redirct as is.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default delete-cancel" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-default delete-confirm" data-dismiss="modal">Confirm</button>
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

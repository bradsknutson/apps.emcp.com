<?php

    $book = $_GET['id'];

    require '../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../includes/header.php';
        
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
                            <h1>Book:</h1>
                            <h2><?php echo $title_string['title']; ?></h2>
                            <p>Links for this book use the domain <strong><?php if($title_string['sub'] != '') { echo $title_string['sub'] .'.'; } ?><?php echo $title_string['domain']; ?></strong></p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item active"><?php echo $title_string['title']; ?></li>
                            </ol>
                            <p><a href="/redirects/books/edit/<?php echo $title_string['id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Book</a></p>
                        </div>
                        <div class="row">
                            <a class="btn btn-success btn-lg btn-block create-new-link" href="http://apps.emcp.com/redirects/links/new/<?php echo $title_string['id']; ?>">Create New Link</a>
                            <div class="row">
                                <div class="col-md-8 border-bottom">
                                    Redirect String
                                </div>
                                <div class="col-md-2 border-bottom">
                                    <div class="status-start">Status</div>
                                </div>
                                <div class="col-md-1 border-bottom">
                                    <div>&nbsp;</div>
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

                                    $string = $row['string'];
                                    if( $string == '' ) {
                                        $string = 'Domain Root';
                                    }

                                    echo '<div class="row is-table-row">
                                        <div class="col-md-8 border-bottom">
                                            <a class="btn-block" href="/redirects/links/edit/'. $row['id'] .'">'. $string .'</a>
                                        </div>
                                        <div class="col-md-2 border-bottom status-check status-check-'. $row['id'] .'" id="'. $row['destination'] .'">
                                            <div class="response-code">&nbsp;</div>
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            <a href="/redirects/links/edit/'. $row['id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            <a class="delete-link" id="'. $row['id'] .'" href="/redirects/links/delete/'. $row['id'] .'"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </div>
                                    </div>';
                                }

                                $link_result->close();

                            ?>
                        </div>
                    </div>               
                    <div class="col-sm-12 col-md-3"></div>
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
    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Books Listing</h1>
                            <p>Choose a book.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item active">Books</li>
                            </ol>
                        </div>                    
                        <div class="row">
                            <div class="col-sm-12 col-md-3"></div>
                            <div class="col-sm-12 col-md-6">
                                <a type="button" class="btn btn-success btn-lg btn-block create-new-book-v2" href="new/">Create New Book</a>
                                <?php

                                    $book = "SELECT * FROM book
                                            ORDER BY title ASC";
                                    $book_result = $mysqli->query($book);

                                    while($row = $book_result->fetch_assoc()) {
                                        echo '<a type="button" class="btn btn-default btn-lg btn-block sort-by-book" href="'. $row['id'] .'">'. $row['title'] .'</a>';
                                    }

                                    $book_result->close();

                                ?>
                            </div>
                            <div class="col-sm-12 col-md-3"></div>
                        </div> 
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
        </body>
    </html>
    <?php
        }

    ?>
<?php

    } 

?>

<?php

    $book = $_GET['id'];

    require '../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        $page = $_GET['page'];
        if( $page == '' ) {
            $page = 1;
        }
        
        include '../includes/header.php';
        
        if( $book != '' ) {
            
            // ********** BOOK INFO ********** \\
            $title = "SELECT a.title, b.domain, c.sub, a.id
                    FROM book a, root_domains b, sub_domains c
                    WHERE a.id = '". $book ."'
                    AND a.domain_id = b.id
                    AND a.sub_id = c.id";
            
            $title_result = $mysqli->query($title);
            $title_string = $title_result->fetch_assoc();
            $title_result->close();            
            // ********** END BOOK INFO ********** \\
            
            // ********** MISSING REDIRECTS ********** \\            
            $missing = "SELECT b.id, count(string) AS count, a.string
                    FROM log_dne a, book b, root_domains c, sub_domains d
                    WHERE b.domain_id = c.id
                    AND b.sub_id = d.id
                    AND b.id = '". $book ."'
                    AND a.domain = c.domain
                    AND a.sub = d.sub
                    AND a.string NOT LIKE '%robots.txt%'
                    AND a.string NOT LIKE '%wp-login%'
                    AND a.string NOT LIKE '%favico%'
                    GROUP BY a.string";

            $missing_result = $mysqli->query($missing);
            $missing_num_rows = $missing_result->num_rows;            
            // ********** END MISSING REDIRECTS ********** \\
            
            $count = "SELECT count(*) AS count FROM redirects
                    WHERE book_id = '". $book ."'
                    AND deleted = '0'";

            $count_result = $mysqli->query($count);
            $count_value = $count_result->fetch_assoc();
            $count_result->close();

            // $count_value['count'];
            
            $displaySize = 100;
            
            $page_num = ceil( $count_value['count']/$displaySize );
            $val_low = ($page - 1) * $displaySize;
            $val_high = $val_low + ($displaySize);
            $val_low_display = $val_low + 1;
            
            if( $val_high > $count_value['count'] ) {
                $val_high = $count_value['count'];
            }
            
            if( $page > $page_num ) {
                header("Location: /redirects/books/". $book ."/page/". $page_num);
            }
            
            if( $title_string['sub'] != '' ) { 
                $domText = $title_string['sub'] .'.'. $title_string['domain']; 
            } else {
                $domText = $title_string['domain'];
            }

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Book:</h1>
                            <h2><?php echo $title_string['title']; ?></h2>
                            <p><?php echo $count_value['count']; ?> links using the domain <strong><?php echo $domText; ?></strong></p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item active"><?php echo $title_string['title']; ?></li>
                            </ol>
                            <p><a href="#toggle" class="redirect-toggle"><i class="fa fa-toggle-on" aria-hidden="true"></i> Toggle Display</a> &nbsp;<a href="/redirects/books/edit/<?php echo $title_string['id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Book</a> &nbsp;<a href="/redirects/links/upload/<?php echo $title_string['id']; ?>"><i class="fa fa-upload" aria-hidden="true"></i> Import Redirects</a> &nbsp;<a href="/redirects/books/mass-edit/<?php echo $title_string['id']; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Bulk Edit</a></p>
                            <p><a href="/redirects/books/alias/new/<?php echo $title_string['id']; ?>"><i class="fa fa-file" aria-hidden="true"></i> Create Alias</a> &nbsp;<a href="/redirects/books/alias/<?php echo $title_string['id']; ?>"><i class="fa fa-link" aria-hidden="true"></i> Manage Aliases</a> &nbsp;<a href="/redirects/stats/book/<?php echo $book; ?>/sort/hits/asc"><i class="fa fa-bar-chart" aria-hidden="true"></i> Statistics</a></p>
                            <?php if( $missing_num_rows > 0 ) { ?>
                            <p><a href="/redirects/books/missing/<?php echo $book; ?>"><i class="fa fa-question" aria-hidden="true"></i> <?php echo $missing_num_rows; ?> potential missing redirect(s) detected.</a></p>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <h4><a href="http://apps.emcp.com/redirects/links/new/<?php echo $title_string['id']; ?>"><i class="fa fa-link" aria-hidden="true"></i> Create New Link</a></h4>
                            <div class="row is-table-row-modified">
                                <div class="col-md-8 fade-container">
                                    <div class="col-md-12 border-bottom shown-cols">
                                        Redirect String
                                    </div>
                                    <div class="col-md-12 border-bottom hidden-cols">
                                        Full Redirect
                                    </div>
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
                                        ORDER BY string ASC
                                        LIMIT ". $val_low .", ". $displaySize;
            
            
                                // LIMIT 0, $displaySize
            
                                $link_result = $mysqli->query($link);
                                $link_num_rows = $link_result->num_rows;

                                while($row = $link_result->fetch_assoc()) {

                                    $string = $row['string'];
                                    if( $string == '' ) {
                                        $string = 'Domain Root';
                                        $URLstring = '';
                                    } else {
                                        $URLstring = '/'. $row['string'];
                                    }

                                    echo '<div class="row is-table-row-modified">
                                        <div class="col-md-8 fade-container is-table-row">
                                            <div class="col-md-12 border-bottom shown-cols">
                                                <a class="btn-block" href="/redirects/links/edit/'. $row['id'] .'">'. $string .'</a>
                                            </div>
                                            <div class="col-md-12 border-bottom hidden-cols">
                                                <a href="/redirects/links/edit/'. $row['id'] .'">http://'. $domText . $URLstring .'</a>
                                            </div>
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
                        <?php if( $count_value['count'] > 100 ) { ?>
                        <div class="text-center">
                            <ul class="pagination">
                                <?php if( $page != '1' ) { ?>
                                <li>
                                    <a href="/redirects/books/<?php echo $book; ?>" aria-label="First">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/redirects/books/<?php echo $book; ?>/page/<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&lsaquo;</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php
                                    
                                    $range_low = $page - 5;
                                    $range_high = $page + 5;
            
                                    $i = $range_low;
                                    while( $i <= $range_high ) {
                                        
                                        if( $i > 0 && $i <= $page_num) {
                                        
                                            if( $i == $page ) {
                                                echo '<li class="active"><span>'. $i .'</span></li>';
                                            } else {
                                                echo '<li><a href="/redirects/books/'. $book .'/page/'. $i .'">'. $i .'</a></li>';
                                            }
                                            
                                        }
                                        $i++;
                                    }
                                            
                                ?>
                                <?php if( $page != $page_num ) { ?>
                                <li>
                                    <a href="/redirects/books/<?php echo $book; ?>/page/<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&rsaquo;</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/redirects/books/<?php echo $book; ?>/page/<?php echo $page_num; ?>" aria-label="Last">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                        <div class="row h5 text-center"><?php echo 'Page '. $page .' of '. $page_num .'. Displaying values '. $val_low_display .' to '. $val_high; ?></div>
                        <div class="row text-center"><p>Having trouble finding a link? <a href="/redirects/search/">Try searching for it</a>.</p></div>
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
                    
                    $('.hidden-cols').hide();
                    
                    $(document).on('click', '.redirect-toggle', function(e) {
                         
                        e.preventDefault();
                        
                        $('.hidden-cols').fadeIn();
                        $('.shown-cols').fadeOut();
                        
                        $(this).addClass('redirect-toggled');
                        $(this).find('i').attr('class','fa fa-toggle-off');
                        
                    });
                    
                    $(document).on('click', '.redirect-toggled', function(e) {
                         
                        e.preventDefault();
                        
                        $('.hidden-cols').fadeOut();
                        $('.shown-cols').fadeIn();
                        
                        $(this).removeClass('redirect-toggled');
                        $(this).find('i').attr('class','fa fa-toggle-on');
                    });
                    
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
                            <p><a href="/redirects/books/new/"><i class="fa fa-file" aria-hidden="true"></i>
 Create New Book</a></p>
                        </div>                    
                        <div class="row">
                            <div class="col-sm-12 col-md-1"></div>
                            <div class="col-sm-12 col-md-10">
                                <?php

                                    $book = "SELECT * FROM book
                                            ORDER BY title ASC";
                                    $book_result = $mysqli->query($book);

                                    while($row = $book_result->fetch_assoc()) {
                                        echo '<div class="row is-table-row border-bottom">
                                                <a class="btn-block sort-by-book" href="'. $row['id'] .'">'. $row['title'] .'</a>
                                            </div>';
                                    }

                                    $book_result->close();

                                ?>
                            </div>
                            <div class="col-sm-12 col-md-1"></div>
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

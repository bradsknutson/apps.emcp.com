<?php

    $id = $_GET['id'];

    $var = $_GET['var'];
    $sort = $_GET['sort'];



    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        /* Get Hit Tracking Data */
        
        $book_title = "SELECT title
                        FROM book
                        WHERE id = '". $id ."'";
            
        $book_title_result = $mysqli->query($book_title);
        $book_title_string = $book_title_result->fetch_assoc();
        $book_title_result->close();
        
        $title = $book_title_string['title'];
        
        $stats = "SELECT a.link_id as redirect_id, count(a.link_id) as count, c.string, b.title, d.domain, e.sub, b.id as book_id, c.destination
                                            FROM log_existing a, book b, redirects c, root_domains d, sub_domains e
                                            WHERE a.link_id = c.id
                                            AND c.book_id = b.id
                                            AND b.domain_id = d.id
                                            AND b.sub_id = e.id
                                            AND b.id = '". $id ."'
                                            GROUP BY a.link_id
                                            ";
        $stats_result = $mysqli->query($stats);
        $stats_num_rows = $stats_result->num_rows;
        $stats_array = array();
        
        while($row = $stats_result->fetch_assoc()) {
            array_push($stats_array,$row);
        }
        
        /* Get All Redirects to Cross-match */
        
        $redirects_all = "SELECT * FROM redirects
                    WHERE book_id = '". $id ."'
                    AND deleted = '0'
                    ORDER BY string ASC";
            
        $redirects_all_result = $mysqli->query($redirects_all);
        $redirects_all_num_rows = $redirects_all_result->num_rows;
        $redirects_array = array();
        while($row = $redirects_all_result->fetch_assoc()) {
            array_push($redirects_array,$row);
        }
        
        /* Sorting Options */
        
        if( $var == 'string' && $sort == 'asc' || $var == '' && $sort == '' ) {
            $string_sort = 'desc';
            $book_sort = 'asc';
            $hits_sort = 'asc';
            
            $link .= "ORDER BY c.string ASC";            
        } else if( $var == 'string' && $sort == 'desc' ) {
            $string_sort = 'asc';
            $book_sort = 'asc';
            $hits_sort = 'asc';
            
            $link .= "ORDER BY c.string DESC";
        } else if( $var == 'book' && $sort == 'asc' ) {
            $string_sort = 'asc';
            $book_sort = 'desc';
            $hits_sort = 'asc';
            
            $link .= "ORDER BY b.title ASC";
        } else if( $var == 'book' && $sort == 'desc' ) {
            $string_sort = 'asc';
            $book_sort = 'asc';
            $hits_sort = 'asc';
            
            $link .= "ORDER BY b.title DESC";
        } else if( $var == 'hits' && $sort == 'asc' ) {
            $string_sort = 'asc';
            $book_sort = 'asc';
            $hits_sort = 'desc';
            
            $link .= "ORDER BY count DESC";
        } else if( $var == 'hits' && $sort == 'desc' ) {
            $string_sort = 'asc';
            $book_sort = 'asc';
            $hits_sort = 'asc';
            
            $link .= "ORDER BY count ASC";
        }  

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Statistics</h1>
                            <h2>Redirect Hit Tracking</h2>
                            <p>For book: <strong><?php echo $title; ?></strong></p>
                            <p>Below is a list of redirects with the number of times they've been used.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books">Books</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/<?php echo $id; ?>"><?php echo $title; ?></a></li>
                                <li class="breadcrumb-item active">Stats</li>
                            </ol>
                            <p><a href="#toggle" class="redirect-toggle"><i class="fa fa-toggle-on" aria-hidden="true"></i> Toggle Display</a>  &nbsp; <a href="/redirects/stats/book/csv/<?php echo $id; ?>" class="redirect-export"><i class="fa fa-download"></i> Export</a></p>
                        </div>
                        <div class="row">
                            <div class="row is-table-row-modified">
                                <div class="col-md-11 fade-container">
                                    <div class="col-md-12 border-bottom shown-cols">
                                        Redirect String
                                    </div>
                                    <div class="col-md-12 border-bottom hidden-cols">
                                        Full Redirect
                                    </div>
                                </div>
                                
                                <div class="col-md-1 border-bottom">
                                    Hits
                                </div>
                            </div>
                            <?php
            
                                $final_array = array();
        
                                foreach ($redirects_array as $redir) {

                                // $redir['id'];
                                // $redir['string'];
                                // $redir['destination'];
                                // $redir['book_id'];
                                // $redir['deleted'];


                                    foreach ($stats_array as $stat) {

                                        // $stat['redirect_id'];
                                        // $stat['count'];
                                        // $stat['string'];
                                        // $stat['domain'];
                                        // $stat['sub'];
                                        // $stat['book_id'];
                                        // $stat['destination'];

                                        $array_row = array();
                                        if( $redir['id'] === $stat['redirect_id'] ) {

                                            $redirect_id = $stat['redirect_id'];
                                            $count = $stat['count'];
                                            $string = $stat['string'];
                                            $domain = $stat['domain'];
                                            $sub = $stat['sub'];
                                            $book_id = $stat['book_id'];
                                            $destination = $stat['destination'];
                                            break;

                                        } else {

                                            $redirect_id = $redir['id'];
                                            $count = '0';
                                            $string = $redir['string'];
                                            $domain = $stat['domain'];
                                            $sub = $stat['sub'];
                                            $book_id = $redir['book_id'];
                                            $destination = $redir['destination'];

                                        }
                                    }
                                    
                                    if( $string == '' ) {
                                        $string = 'Domain Root';
                                    }
                                        
                                    echo '<div class="row is-table-row-modified">
                                        <div class="col-md-11 fade-container is-table-row">
                                            <div class="col-md-12 border-bottom shown-cols">
                                                <a class="btn-block" href="/redirects/links/edit/'. $redirect_id .'">'. $string .'</a>
                                            </div>
                                            <div class="col-md-12 border-bottom hidden-cols">
                                                <a href="/redirects/links/edit/'. $redirect_id .'">'. $sub .'.'. $domain .'/'. $string .'</a>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-1 border-bottom">
                                            '. $count .'
                                        </div>
                                    </div>';

                                }

                            ?>
                        </div>
                    </div>               
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>    
            <script>
                $(document).ready(function() {
                    
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
        </body>
    </html>
<?php

    } 

?>

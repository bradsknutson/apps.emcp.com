<?php

    $var = $_GET['var'];
    $sort = $_GET['sort'];

    require '../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../includes/header.php';
        
        $link = "SELECT a.link_id as redirect_id, count(a.link_id) as count, c.string, b.title, d.domain, e.sub, b.id as book_id, c.destination
                                            FROM log_existing a, book b, redirects c, root_domains d, sub_domains e
                                            WHERE a.link_id = c.id
                                            AND c.book_id = b.id
                                            AND b.domain_id = d.id
                                            AND b.sub_id = e.id
                                            GROUP BY a.link_id
                                            ";
        
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
                            <p>Below is a list of redirects with the number of times they've been used.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/redirects/">Home</a></li>
                                <li class="breadcrumb-item active">Stats</li>
                            </ol>
                            <p><a href="#toggle" class="redirect-toggle"><i class="fa fa-toggle-on" aria-hidden="true"></i> Toggle Display</a></p>
                        </div>
                        <div class="row">
                            <div class="row is-table-row-modified">
                                <div class="col-md-10 fade-container">
                                    <div class="col-md-5 border-bottom shown-cols">
                                        <a href="/redirects/stats/sort/string/<?php echo $string_sort; ?>" class="sort-a sort-string">Redirect String <i class="fa fa-sort" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-md-7 border-bottom shown-cols">
                                        <a href="/redirects/stats/sort/book/<?php echo $book_sort; ?>" class="sort-a sort-book">Book <i class="fa fa-sort" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-md-12 border-bottom hidden-cols">
                                        Full Redirect <i class="fa fa-sort" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="col-md-1 border-bottom">
                                    <a href="/redirects/stats/sort/hits/<?php echo $hits_sort; ?>" class="sort-a sort-hits">Hits <i class="fa fa-sort" aria-hidden="true"></i></a>
                                </div>
                                <div class="col-md-1 border-bottom">
                                    Edit
                                </div>
                            </div>
                            <?php
            
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
                                    
                                    if( $row['sub'] == '' ) {
                                        $domain = $row['domain'];
                                    } else {
                                        $domain = $row['sub'] .'.'. $row['domain'];
                                    }

                                    echo '<div class="row is-table-row-modified">
                                        <div class="col-md-10 fade-container is-table-row">
                                            <div class="col-md-5 border-bottom shown-cols">
                                                <a class="btn-block" href="/redirects/links/edit/'. $row['redirect_id'] .'">'. $string .'</a>
                                            </div>
                                            <div class="col-md-7 border-bottom shown-cols">
                                                <a class="btn-block" href="/redirects/books/'. $row['book_id'] .'">'. $row['title'] .'</a>
                                            </div>
                                            <div class="col-md-12 border-bottom hidden-cols">
                                                <a href="/redirects/links/edit/'. $row['redirect_id'] .'">http://'. $domain . $URLstring .'</a>
                                            </div>
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            '. $row['count'] .'
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            <a href="/redirects/links/edit/'. $row['redirect_id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
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

<?php

    $searchTerm = $_GET['id'];

    require '../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../includes/header.php';
        
        if( $searchTerm != '' ) {
            
            $search = "SELECT d.id, d.string, a.title, a.id AS book_id, b.domain, c.sub, d.destination
                    FROM book a, root_domains b, sub_domains c, redirects d
                    WHERE (d.string LIKE '%". $searchTerm ."%'
                    OR c.sub LIKE '%". $searchTerm ."%')
                    AND a.id = d.book_id
                    AND a.domain_id = b.id
                    AND a.sub_id = c.id
                    AND d.deleted = '0'
                    ORDER BY d.string ASC";

            $search_result = $mysqli->query($search);   

            $row_cnt = $search_result->num_rows;

            if( $row_cnt > 0 ) { // ****** SEARCH RESULTS FOUND ******

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Search:</h1>
                            <h2><?php echo $searchTerm; ?></h2>
                            <p>Below is a list of redirect strings that matched your above search query, <?php echo $searchTerm; ?>.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item search-link"><a href="http://apps.emcp.com/redirects/search/">Search</a></li>
                                <li class="breadcrumb-item active"><?php echo $searchTerm; ?></li>
                            </ol>
                            <p><a href="/redirects/search/" class="search-link"><i class="fa fa-search" aria-hidden="true"></i> Search Again</a></p>
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="col-md-4 border-bottom">
                                    Redirect String
                                </div>
                                <div class="col-md-5 border-bottom">
                                    Book
                                </div>
                                <div class="col-md-2 border-bottom">
                                    <div class="status-start">Status</div>
                                </div>
                                <div class="col-md-1 border-bottom">
                                    <div>&nbsp;</div>
                                </div>
                            </div>
                            <?php

                                while($row = $search_result->fetch_assoc()) {
                                    
                                    if( $row['string'] == '' ) {
                                        $string = 'Root Domain';
                                    } else {
                                        $string = $row['string'];
                                    }

                                    echo '<div class="row is-table-row">
                                        <div class="col-md-4 border-bottom">
                                            <a class="btn-block" href="/redirects/links/edit/'. $row['id'] .'">'. $string .'</a>
                                        </div>
                                        <div class="col-md-5 border-bottom">
                                            <a class="btn-block" href="/redirects/books/'. $row['book_id'] .'">'. $row['title'] .'</a>
                                        </div>
                                        <div class="col-md-2 border-bottom status-check status-check-'. $row['id'] .'" id="'. $row['destination'] .'">
                                            <div class="response-code">&nbsp;</div>
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            <a href="/redirects/links/edit/'. $row['id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        </div>
                                    </div>';
                                }

                                $search_result->close();

                            ?>
                        </div>
                    </div>               
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {                
                    
                    $('.search-link').click(function(e) {

                        e.preventDefault();

                        $('#searchModal').modal('show');

                    });              
                    
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
            <div class="search-modal">
                <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="searchModalLabel">Search for a redirect.</h2>
                            </div>
                            <div class="modal-body">
                                <p>Enter the redirect string you want to find.</p>
                                <form class="form-horizontal">
                                    <div class="form-group row">
                                        <input type="text" class="form-control input-lg" id="search" placeholder="Search">
                                    </div>                   
                                    <div class="form-group row">
                                        <button type="submit" class="btn btn-default btn-lg">Submit</button>
                                    </div> 
                                </form>
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
            } else { // ****** NO SEARCH RESULTS FOUND ******
            ?>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 col-md-3"></div>
                            <div class="col-sm-12 col-md-6">
                                <div class="jumbotron">
                                    <h1>Search:</h1>
                                    <h2>No Results for <?php echo $searchTerm; ?></h2>
                                    <p>There were no results found for your search, please try <a href="/redirects/search/" class="search-link">searching again</a>.</p>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                        <li class="breadcrumb-item search-link"><a href="http://apps.emcp.com/redirects/search/">Search</a></li>
                                        <li class="breadcrumb-item active">No Results</li>
                                    </ol>
                                    <p><a href="/redirects/search/" class="search-link"><i class="fa fa-search" aria-hidden="true"></i> Search Again</a></p>
                                </div>
                            </div>               
                            <div class="col-sm-12 col-md-3"></div>
                        </div>
                    </div>
                </body>
            </html>    
            <script>
                $(document).ready(function() {                
                    
                    $('.search-link').click(function(e) {

                        e.preventDefault();

                        $('#searchModal').modal('show');

                    });   
                });
            </script>
            <div class="search-modal">
                <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="searchModalLabel">Search for a redirect.</h2>
                            </div>
                            <div class="modal-body">
                                <p>Enter the redirect string you want to find.</p>
                                <form class="form-horizontal">
                                    <div class="form-group row">
                                        <input type="text" class="form-control input-lg" id="search" placeholder="Search">
                                    </div>                   
                                    <div class="form-group row">
                                        <button type="submit" class="btn btn-default btn-lg">Submit</button>
                                    </div> 
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            
        } else { // ****** SEARCH PAGE ******
    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Search</h1>
                            <p>Find a link by searching for the redirect string.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item active">Search</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $('#searchModal').modal('show');
                });
            </script>
            <div class="search-modal">
                <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="searchModalLabel">Search for a redirect.</h2>
                            </div>
                            <div class="modal-body">
                                <p>Enter the redirect string you want to find.</p>
                                <form class="form-horizontal">
                                    <div class="form-group row">
                                        <input type="text" class="form-control input-lg" id="search" placeholder="Search">
                                    </div>                   
                                    <div class="form-group row">
                                        <button type="submit" class="btn btn-default btn-lg">Submit</button>
                                    </div> 
                                </form>
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
<?php

    } 

?>

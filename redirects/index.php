<?php

    require 'includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include 'includes/header.php';
        
        $countRedirectsQuery = 'SELECT * FROM redirects';
        $countLoggedQuery = 'SELECT * FROM log_existing';

        $countRedirectsQueryResult = $mysqli->query($countRedirectsQuery);
        $countRedirectsQueryCount = $countRedirectsQueryResult->num_rows;
        
        $countLoggedQueryResult = $mysqli->query($countLoggedQuery);
        $countLoggedQueryCount = $countLoggedQueryResult->num_rows; 
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>EMCP Redirects</h1>
                        <p>This tool is used to manage and generate new redirects.<br />
                            <strong><?php echo number_format($countRedirectsQueryCount); ?></strong> redirects have been used <strong><?php echo number_format($countLoggedQueryCount); ?></strong> times.</p>
                        <p><a href="/redirects/search/" class="search-link"><i class="fa fa-search" aria-hidden="true"></i> Search for a redirect</a></p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <h2>Sort by book.</h2>
                                <p><a href="/redirects/books/new/"><i class="fa fa-book" aria-hidden="true"></i> Create New Book</a></a>
                                <div class="row">
                                <?php

                                    $book = "SELECT * FROM book
                                            ORDER BY title ASC";
                                    $book_result = $mysqli->query($book);
        
                                    while($row = $book_result->fetch_assoc()) {
                                        echo '<div class="row is-table-row border-bottom"><a class="btn-block" href="/redirects/books/'. $row['id'] .'">'. $row['title'] .'</a></div>';
                                    }

                                    $book_result->close();
        
                                ?>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <h2>Sort by domain.</h2>
                                <p><a href="/redirects/domains/new/"><i class="fa fa-file-text" aria-hidden="true"></i> Create New Domain</a></p>
                                <p><a href="/redirects/domains/sub/new/"><i class="fa fa-file-text-o" aria-hidden="true"></i> Create New Sub Domain</a></p>
                                <div class="row">
                                <?php

                                    $dom = "SELECT * FROM root_domains
                                            ORDER BY domain ASC";
                                    $dom_result = $mysqli->query($dom);
        
                                    while($row = $dom_result->fetch_assoc()) {
                                        echo '<div class="row is-table-row border-bottom"><a class="btn-block" href="domains/'. $row['id'] .'">'. $row['domain'] .'</a></div>';
                                    }

                                    $dom_result->close();
        
                                ?>
                                </div>
                            </div>
                        </div>
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

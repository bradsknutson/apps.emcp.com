<?php

    require 'includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include 'includes/header.php';
        
?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>EMCP Redirects</h1>
                        <p>This tool is used to manage and generate new redirects.</p>
                        <p><a href="/redirects/search/" class="search-link"><i class="fa fa-search" aria-hidden="true"></i> Search for a redirect</a></p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <h2>Sort by book.</h2>
                                <?php

                                    $book = "SELECT * FROM book
                                            ORDER BY title ASC";
                                    $book_result = $mysqli->query($book);
        
                                    while($row = $book_result->fetch_assoc()) {
                                        echo '<a type="button" class="btn btn-default btn-lg btn-block sort-by-book" href="/redirects/books/'. $row['id'] .'">'. $row['title'] .'</a>';
                                    }

                                    $book_result->close();
        
                                ?>
                                <a type="button" class="btn btn-success btn-lg btn-block create-new-book" href="/redirects/books/new/">Create New Book</a>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <h2>Sort by domain.</h2>
                                <?php

                                    $dom = "SELECT * FROM root_domains
                                            ORDER BY domain ASC";
                                    $dom_result = $mysqli->query($dom);
        
                                    while($row = $dom_result->fetch_assoc()) {
                                        echo '<a type="button" class="btn btn-default btn-lg btn-block" href="domains/'. $row['id'] .'">'. $row['domain'] .'</a>';
                                    }

                                    $dom_result->close();
        
                                ?>
                                <a type="button" class="btn btn-success btn-lg btn-block create-new-domain" href="/redirects/domains/new/">Create New Domain</a>
                                <a type="button" class="btn btn-success btn-lg btn-block create-new-sub" href="/redirects/domains/sub/new/">Create New Subdomain</a>
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

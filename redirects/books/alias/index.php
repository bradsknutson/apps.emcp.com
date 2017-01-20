<?php

    $alias = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        if( $alias != '' ) {
            
            // ********** BOOK INFO ********** \\
            $book_title = "SELECT a.id as book_id, a.title, b.domain, c.sub
                    FROM book a, root_domains b, sub_domains c
                    WHERE a.id = '". $alias ."'
                    AND a.domain_id = b.id
                    AND a.sub_id = c.id";
            
            $book_title_result = $mysqli->query($book_title);
            $book_title_string = $book_title_result->fetch_assoc();
            $book_title_result->close();           
            
            if( $book_title_string['sub'] == '' ) {
                $book_nice_domain = $book_title_string['domain'];
            } else {
                $book_nice_domain = $book_title_string['sub'] .'.'. $book_title_string['domain'];
            }
            // ********** END BOOK INFO ********** \\
            
            // ********** ALIAS INFO ********** \\
            $alias = "SELECT a.id AS alias_id, b.id AS book_id, c.domain, d.sub
                        FROM book_alias a, book b, root_domains c, sub_domains d
                        WHERE a.book_id =  '". $alias ."'
                        AND a.book_id = b.id
                        AND a.domain_id = c.id
                        AND a.sub_id = d.id";
            
            $alias_result = $mysqli->query($alias);
            // ********** END ALIAS INFO ********** \\
            
            if( $alias_string['sub'] == '' ) {
                $nice_domain = $alias_string['domain'];
            } else {
                $nice_domain = $alias_string['sub'] .'.'. $alias_string['domain'];
            }
            

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Aliases for book:</h1>
                            <h2><?php echo $book_title_string['title']; ?></h2>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/alias/">Aliases</a></li>
                                <li class="breadcrumb-item active"><?php echo $book_title_string['title']; ?></li>
                            </ol>
                            <p><a href="/redirects/books/<?php echo $book_title_string['book_id']; ?>"><i class="fa fa-book" aria-hidden="true"></i> Back To Book</a> &nbsp;<a href="/redirects/books/alias/new/<?php echo $book_title_string['book_id']; ?>"><i class="fa fa-file" aria-hidden="true"></i> Create New Alias</a></p>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <div class="row is-table-row border-bottom">
                                <div class="col-md-10">
                                    Domain
                                </div>
                                <div class="col-md-2 text-center">
                                    Edit
                                </div>
                            </div>
                            <?php
              
            
                                while($row = $alias_result->fetch_assoc()) {
                                    
                                    if( $row['sub'] == '' ) {
                                        $dom_display = $row['domain'];
                                    } else {
                                        $dom_display = $row['sub'] .'.'. $row['domain'];
                                    }   
                                    
                                    echo '<div class="row is-table-row border-bottom">
                                            <div class="col-md-10">
                                                '. $dom_display .'
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <a href="/redirects/books/alias/manage/'. $row['alias_id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            </div>
                                        </div>';
                                }

                                $alias_result->close();            

                            ?>
                        </div>
                        <div class="col-md-1"></div>
                    </div>               
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    
                });
            </script>
            <div class="error-handling"></div>
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
                            <h1>Alias Listing</h1>
                            <p>Select an alias.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item active">Aliases</li>
                            </ol>
                            <p><a class="generate-alias" href="/redirects/books/alias/new/"><i class="fa fa-file" aria-hidden="true"></i>
 Create New Alias</a></p>
                        </div>                    
                        <div class="row">
                            <div class="col-sm-12 col-md-1"></div>
                            <div class="col-sm-12 col-md-10">
                                <div class="row is-table-row border-bottom">
                                    <div class="col-md-4">
                                        Alias Domain
                                    </div>
                                    <div class="col-md-6">
                                        For Book
                                    </div>
                                    <div class="col-md-2 text-center">
                                        Manage
                                    </div>
                                </div>
                                <?php
            
                                    // ********** ALIAS INFO ********** \\
                                    $alias_book = "SELECT a.id AS alias_id, b.title, b.id AS book_id, c.domain, d.sub
                                            FROM book_alias a, book b, root_domains c, sub_domains d
                                            WHERE a.book_id = b.id 
                                            AND a.domain_id = c.id
                                            AND a.sub_id = d.id
                                            ORDER BY b.title ASC";
                                    // ********** END ALIAS INFO ********** \\
            
                                    $alias_book_result = $mysqli->query($alias_book);

                                    while($row = $alias_book_result->fetch_assoc()) {
                                        
                                        if( $row['sub'] == '' ) {
                                            $dom_display = $row['domain'];
                                        } else {
                                            $dom_display = $row['sub'] .'.'. $row['domain'];
                                        }
                                        
                                        echo '<div class="row is-table-row border-bottom">
                                                <div class="col-md-4">
                                                    '. $dom_display .'
                                                </div>
                                                <div class="col-md-6">
                                                    <a href="/redirects/books/'. $row['book_id'] .'">'. $row['title'] .'</a>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <a href="/redirects/books/alias/manage/'. $row['alias_id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                </div>
                                            </div>';
                                    }

                                    $alias_book_result->close();

                                ?>
                            </div>
                            <div class="col-sm-12 col-md-1"></div>
                        </div> 
                    </div>
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    
                    $(document).on('click', '.generate-alias', function(e) {
                        e.preventDefault();
                        $('#generateAliasModal').modal('show');
                    });
                    
                    $(document).on('change', '.new-alias-select', function() {
                        $page = '/redirects/books/alias/new/' + $('.new-alias-select').val(); 
                    });
                    
                    $(document).on('click', '.alias-confirm', function() {
                        window.location = $page;
                    });
                    
                });
            </script>
            <div class="error-handling"></div>
            <div class="generate-alias-modal">
                <div class="modal fade" id="generateAliasModal" tabindex="-1" role="dialog" aria-labelledby="generateAliasModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="generateAliasModalLabel">For Which Book?</h2>
                            </div>
                            <div class="modal-body">
                                <p>In order to create a new alias, you first need to select a book.</p>
                                <select class="form-control new-alias-select">
                                <?php

                                    $book = "SELECT a.id, a.title, b.domain, c.sub
                                            FROM book a, root_domains b, sub_domains c
                                            WHERE a.domain_id = b.id
                                            AND a.sub_id = c.id
                                            ORDER BY a.title ASC";
                                    $book_result = $mysqli->query($book);

                                    while($row = $book_result->fetch_assoc()) {
                                        
                                        if( $row['sub'] == '' ) { 
                                            $friendly_dom = $row['domain'];
                                        } else {
                                            $friendly_dom = $row['sub'] .'.'. $row['domain'];
                                        }
                                        
                                        echo '<option value="'. $row['id'] .'">'. $row['title'] .' ('. $friendly_dom .')</option>';
                                    }

                                    $book_result->close();

                                ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-default alias-confirm" data-dismiss="modal">Next</button>
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

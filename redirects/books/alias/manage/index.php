<?php

    $alias = $_GET['id'];

    require '../../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../../includes/header.php';
        
        if( $alias != '' ) {
            
            // ********** BOOK INFO ********** \\
            $book_title = "SELECT a.id, b.domain, c.sub
                    FROM book a, root_domains b, sub_domains c, book_alias d
                    WHERE d.id = '". $alias ."'
                    AND d.book_id = a.id
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
            $title = "SELECT a.id AS alias_id, b.title, b.id AS book_id, c.domain, d.sub
                    FROM book_alias a, book b, root_domains c, sub_domains d
                    WHERE a.id = '". $alias ."' 
                    AND a.book_id = b.id 
                    AND a.domain_id = c.id
                    AND a.sub_id = d.id";
            
            $title_result = $mysqli->query($title);
            $title_string = $title_result->fetch_assoc();
            $title_result->close();            
            // ********** END ALIAS INFO ********** \\
            
            if( $title_string['sub'] == '' ) {
                $nice_domain = $title_string['domain'];
            } else {
                $nice_domain = $title_string['sub'] .'.'. $title_string['domain'];
            }
            

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Alias for book:</h1>
                            <h2><?php echo $title_string['title']; ?></h2>
                            <p>This alias uses the domain <strong><?php echo $nice_domain; ?></strong>.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/books/alias/">Aliases</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/alias/<?php echo $title_string['book_id']; ?>"><?php echo $title_string['title']; ?></a></li>
                                <li class="breadcrumb-item active">Manage</li>
                            </ol>
                            <p><a href="/redirects/books/alias/edit/<?php echo $title_string['alias_id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Alias</a> &nbsp;<a href="/redirects/books/<?php echo $title_string['book_id']; ?>"><i class="fa fa-book" aria-hidden="true"></i> Go To Book</a>
                            <p>All redirects for the book <strong><?php echo $title_string['title']; ?></strong> can use this domain alias.  Meaning <?php echo $nice_domain; ?>/example will redirect to the same place as <?php echo $book_nice_domain; ?>/example, and they are essentially the same redirect.</p>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-5">
                                <h3>Alias Domain</h3>
                            </div>
                            <div class="col-md-2">
                                <h3>&nbsp;</h3>
                            </div>
                            <div class="col-md-5">
                                <h3><?php echo $title_string['title']; ?></h3>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-5">
                                <h1><i class="fa fa-book" aria-hidden="true"></i></h1>
                            </div>
                            <div class="col-md-2">
                                <h1><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></h1>
                            </div>
                            <div class="col-md-5">
                                <h1><i class="fa fa-book" aria-hidden="true"></i></h1>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-5">
                                <h3><?php echo $nice_domain; ?></h3>
                            </div>
                            <div class="col-md-2">
                                <h3>&nbsp;</h3>
                            </div>
                            <div class="col-md-5">
                                <h3><?php echo $book_nice_domain; ?></h3>
                            </div>
                        </div>
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
            
            header('Location: /redirects/books/alias/');
            exit;
            
        }

    ?>
<?php

    } 

?>

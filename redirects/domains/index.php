<?php

    $domain_id = $_GET['id'];

    require '../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../includes/header.php';
        
        if( $domain_id != '' ) {
            
            $domain_info = "SELECT * FROM root_domains
                            WHERE id = '". $domain_id ."'";
            
            $domain_info_result = $mysqli->query($domain_info);
            $domain_info_string = $domain_info_result->fetch_assoc();
            $domain_info_result->close();

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1>Domain:</h1>
                            <h2><?php echo $domain_info_string['domain']; ?></h2>
                            <p>Links for this domain are listed below.</p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/domains/">Domains</a></li>
                                <li class="breadcrumb-item active"><?php echo $domain_info_string['domain']; ?></li>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="col-md-4 border-bottom">
                                    Redirect String
                                </div>
                                <div class="col-md-5 border-bottom">
                                    Book
                                </div>
                                <div class="col-md-1 border-bottom">
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

                                $domain_links = "SELECT a.id AS link_id, a.string, a.destination, b.title, a.book_id, c.domain, c.id AS domain_id, d.sub, d.id AS sub_id
                                FROM redirects a, book b, root_domains c, sub_domains d
                                WHERE b.id = a.book_id
                                AND c.id = b.domain_id
                                AND d.id = b.sub_id
                                AND b.domain_id = '". $domain_id ."'
                                AND a.deleted = '0'
                                ORDER BY string ASC";
            
                                $domain_links_result = $mysqli->query($domain_links);

                                while($row = $domain_links_result->fetch_assoc()) {

                                    $string = $row['string'];
                                    if( $string == '' ) {
                                        $string = 'Domain Root';
                                    }

                                    echo '<div class="row is-table-row">
                                        <div class="col-md-4 border-bottom">
                                            <a class="btn-block" href="/redirects/links/edit/'. $row['link_id'] .'">'. $string .'</a>
                                        </div>
                                        <div class="col-md-5 border-bottom">
                                            <a class="btn-block" href="/redirects/books/'. $row['book_id'] .'">'. $row['title'] .'</a>
                                        </div>
                                        <div class="col-md-1 border-bottom status-check status-check-'. $row['link_id'] .'" id="'. $row['destination'] .'">
                                            <div class="response-code">&nbsp;</div>
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            <a href="/redirects/links/edit/'. $row['link_id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-md-1 border-bottom">
                                            <a class="delete-link" id="'. $row['link_id'] .'" href="/redirects/links/delete/'. $row['link_id'] .'"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </div>
                                    </div>';
                                }

                                $domain_links_result->close();

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
                        <h1>Redirect Domains</h1>
                        <p>Below is a list of all domains managed under this redirect tool.</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item active">Domains</li>
                        </ol>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <h2>Choose a domain.</h2>
                                <?php

                                    $domain = "SELECT * FROM root_domains
                                            ORDER BY domain ASC";
                                    $domain_result = $mysqli->query($domain);
        
                                    while($row = $domain_result->fetch_assoc()) {
                                        echo '<a type="button" class="btn btn-default btn-lg btn-block sort-by-book" href="/redirects/domains/'. $row['id'] .'">'. $row['domain'] .'</a>';
                                    }

                                    $domain_result->close();
        
                                ?>
                                <a type="button" class="btn btn-success btn-lg btn-block create-new-domain" href="/redirects/domains/new/">Create New Domain</a>
                                <a type="button" class="btn btn-success btn-lg btn-block create-new-sub" href="/redirects/domains/sub/new/">Create New Subdomain</a>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3"></div>
                    </div>
                </div>                    
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                
            });
        </script>
    </body>
</html>
    <?php
        }

    ?>
<?php

    } 

?>
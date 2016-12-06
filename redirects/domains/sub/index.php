<?php

    $sub_id = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        if( $sub_id != '' ) {
            
            $sub_info = "SELECT * FROM sub_domains
                            WHERE id = '". $sub_id ."'";
            
            $sub_info_result = $mysqli->query($sub_info);
            $sub_info_string = $sub_info_result->fetch_assoc();
            $sub_info_result->close();

    ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Subdomain: <?php echo $sub_info_string['sub']; ?></h1>
                        <p>Below is a list of all links associated with this subdomain.</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/domains/">Domains</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/domains/sub/">Subdomains</a></li>
                            <li class="breadcrumb-item active"><?php echo $sub_info_string['sub']; ?></li>
                        </ol>
                        <p><a href="/redirects/domains/sub/edit/<?php echo $sub_info_string['id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Subdomain</a></p>
                    </div>
                    <div class="row">
                            <div class="row">
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
            
                                    $sub_domains = "SELECT c.id, c.string, d.title, d.id AS book_id, c.destination
                                                    FROM sub_domains a, root_domains b, redirects c, book d
                                                    WHERE c.book_id = d.id
                                                    AND d.sub_id = a.id
                                                    AND d.domain_id = b.id
                                                    AND a.id = '". $sub_id ."'
                                                    AND c.deleted = '0'
                                                    ORDER BY c.string ASC";

                                    $sub_domains_result = $mysqli->query($sub_domains);
        
                                    while($row = $sub_domains_result->fetch_assoc()) {
                                        
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
                                            <div class="col-md-1 border-bottom status-check status-check-'. $row['id'] .'" id="'. $row['destination'] .'">
                                                <div class="response-code">&nbsp;</div>
                                            </div>
                                            <div class="col-md-1 border-bottom">
                                                <a href="/redirects/links/edit/'. $row['id'] .'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            </div>
                                            <div class="col-md-1 border-bottom">
                                                <a class="delete-link" id="'. $row['link_id'] .'" href="/redirects/links/delete/'. $row['id'] .'"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </div>
                                        </div>';
                                    }

                                    $sub_domains_result->close();
        
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
                    
                    $('.status-start').on('click', function() {
                        console.log('click');
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
                        <h1>Redirect Subdomains</h1>
                        <p>Below is a list of all subdomains managed under this redirect tool.</p>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/">Home</a></li>
                            <li class="breadcrumb-item"><a href="http://apps.emcp.com/redirects/domains/">Domains</a></li>
                            <li class="breadcrumb-item active">Subomains</li>
                        </ol>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <h2>Choose a subdomain.</h2>
                                <?php

                                    $domain = "SELECT * FROM sub_domains
                                            ORDER BY sub ASC";
                                    $domain_result = $mysqli->query($domain);
        
                                    while($row = $domain_result->fetch_assoc()) {
                                        
                                        if( $row['sub'] == '' ) {
                                            $string = 'Empty - No Subdomain';
                                        } else {
                                            $string = $row['sub'];
                                        }
                                        
                                        echo '<a type="button" class="btn btn-default btn-lg btn-block sort-by-book" href="/redirects/domains/sub/'. $row['id'] .'">'. $string .'</a>';
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
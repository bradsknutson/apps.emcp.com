<?php

    $book = $_GET['id'];

    require '../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        include '../../includes/header.php';
        
        if( $book != '' ) {
            
            // ********** BOOK INFO ********** \\
            $book_title = "SELECT a.id as book_id, a.title, b.domain, c.sub, a.default_url
                    FROM book a, root_domains b, sub_domains c
                    WHERE a.id = '". $book ."'
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
            
            // ********** MISSING REDIRECTS ********** \\
            
            $missing = "SELECT count(a.string) AS count, a.string
                    FROM log_dne a, book b, root_domains c, sub_domains d
                    WHERE b.domain_id = c.id
                    AND b.sub_id = d.id
                    AND b.id = '". $book ."'
                    AND a.domain = c.domain
                    AND a.sub = d.sub
                    AND a.string NOT LIKE '%robots.txt%'
                    AND a.string NOT LIKE '%wp-login%'
                    AND a.string NOT LIKE '%favico%'
                    AND a.string NOT LIKE '%wp-admin%'
                    AND a.string NOT LIKE '%admin.php%'
                    AND a.string NOT LIKE '%xmlrpc%'
                    AND a.string NOT LIKE '%forum.php%'
                    AND a.string NOT LIKE '%readme.html%'
                    AND a.string NOT LIKE '%tiny_mce%'
                    AND a.string NOT LIKE '%wp-content%'
                    AND a.string NOT LIKE '%elfinder%'
                    AND a.string NOT LIKE '%config.php%'
                    AND a.string NOT LIKE '%cpanel%'
                    AND a.string NOT LIKE '%license.php%'
                    AND a.string NOT LIKE '%simplefileupload%'
                    AND a.string NOT LIKE '%wordpress%'
                    AND a.string NOT LIKE '%uploadify%'
                    AND a.string NOT IN (SELECT e.string FROM redirects e WHERE e.book_id = '". $book ."')
                    AND a.string NOT IN (SELECT f.string FROM dne_exempt f WHERE f.book_id = '". $book ."')
                    GROUP BY a.string";
            
            // ********** END MISSING REDIRECTS ********** \\
            
            

    ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-6">
                        <div class="jumbotron">
                            <h1 style="font-size:45px;">Potential Missing Redirects</h1>
                            <h2><?php echo $book_title_string['title']; ?></h2>
                            <p>The following redirects were logged, but are missing from our database of redirects. <i class="fa fa-info-circle help-book-name" aria-hidden="true" title data-content="Users attempting to reach these redirects were sent to the default URL for the book: <?php echo $book_title_string['default_url']; ?>" data-toggle="popover" data-placement="top" data-trigger="hover click" data-original-title="Help"></i></p>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/redirects/">Home</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/">Books</a></li>
                                <li class="breadcrumb-item"><a href="/redirects/books/<?php echo $book; ?>"><?php echo $book_title_string['title']; ?></a></li>
                                <li class="breadcrumb-item active">Missing Redirects</li>
                            </ol>
                            <p><a href="#toggle" class="redirect-toggle"><i class="fa fa-toggle-on" aria-hidden="true"></i> Toggle Display</a> &nbsp; <a href="/redirects/books/<?php echo $book_title_string['book_id']; ?>"><i class="fa fa-book" aria-hidden="true"></i> Back To Book</a></p>
                        </div>
                        
                        <div class="row is-table-row">
                            <div class="col-md-9 fade-container">
                                <div class="col-md-12 border-bottom shown-cols">
                                    Full Redirect
                                </div>
                                <div class="col-md-12 border-bottom hidden-cols">
                                    Redirect String
                                </div>
                            </div>
                            <div class="col-md-1 border-bottom text-center">
                                Hits
                            </div>
                            <div class="col-md-1 border-bottom text-center">
                                Add
                            </div>
                            <div class="col-md-1 border-bottom text-center">
                                Ignore
                            </div>
                        </div>
                        <?php

                            $missing_result = $mysqli->query($missing);

                            while($row = $missing_result->fetch_assoc()) {
                                
                                if( $row['string'] == '' ) {
                                    $string = 'Root Domain';
                                    $URLstring = '';
                                } else {
                                    $string = $row['string'];
                                    $URLstring = '/'. $row['string'];
                                }
                                
                                echo '<div class="row is-table-row">
                                        <div class="col-md-9 fade-container">
                                            <div class="col-md-12 border-bottom shown-cols">
                                                http://'. $book_nice_domain . $URLstring .'
                                            </div>
                                            <div class="col-md-12 border-bottom hidden-cols">
                                                '. $string .'
                                            </div>
                                        </div>
                                        <div class="col-md-1 border-bottom text-center">
                                                '. $row['count'] .'
                                        </div>
                                        <div class="col-md-1 border-bottom text-center">
                                            <a href="#add" class="missing-add" data-string="'. $row['string'] .'">
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                <form method="POST" action="/redirects/links/new/'. $book .'" style="display:none;">
                                                    <input type="text" name="string" value="'. $row['string'] .'" />
                                                </form>
                                            </a>
                                        </div>
                                        <div class="col-md-1 border-bottom text-center">
                                            <a href="#ignore" class="missing-ignore" data-string="'. $row['string'] .'"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                        </div>
                                    </div>';
                            }

                            $missing_result->close();


                        ?>
                    </div>               
                    <div class="col-sm-12 col-md-3"></div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    
                    $book_id = '<?php echo $book; ?>';
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
                    
                    $(document).on('click', '.missing-add', function(e) {
                        
                        e.preventDefault();
                        
                        $var = $(this).attr('data-string');
                        $(this).find('form').submit();
                        
                    });
                    
                    $(document).on('click', '.missing-ignore', function(e) {
                        
                        e.preventDefault();
                        
                        if( $cautionCheck == '1' ) {
                            
                            $ignoreString = $(this).attr('data-string');

                            ajaxCall($book_id,$ignoreString);    
                            
                        } else {
                            $('#cautionModal').modal('show');
                            $paddedString = $(this).attr('data-string');
                        }
                        

                        
                    });
                    
                    $(document).on('click', '.ignore-cancel', function(e) {
                        $cautionCheck = '0';
                    });
                    
                    $(document).on('click', '.ignore-confirm', function(e) {
                        $cautionCheck = '1';

                        ajaxCall($book_id,$paddedString);
                        
                    });
                    

                    
                    $cautionCheck = '0';
                    function ajaxCall($id,$string) {
                    
                        $.ajax({
                            method: "POST",
                            url: "http://apps.emcp.com/redirects/includes/ignore_missing_redirect.php",
                            async: true,
                            data: { 
                                book_id: $book_id,
                                string: $string
                            }
                        }).done(function(data) {
                            $('.error-handling').html(data);
                        }); 
                    
                    }
                });
            </script>
            <div class="error-handling"></div>
            <div class="success-modal">
                <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="successModalLabel">Thank you.</h2>
                            </div>
                            <div class="modal-body">
                                <h3>Your feedback is appreciated.</h3>
                                <p>The missing redirect you chose to ignore will not be displayed in this report again.</p>
                                <p>If you believe you have ignored this missing redirect in error, please contact <a href="mailto:webdev@emcp.com">webdev@emcp.com</a>.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="success-modal">
                <div class="modal fade" id="cautionModal" tabindex="-1" role="dialog" aria-labelledby="cautionModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h2 class="modal-title" id="cautionModalLabel">Confirm Missing Redirect Removal</h2>
                            </div>
                            <div class="modal-body">
                                <h3>You are attempting to ignore a missing redirect.</h3>
                                <p>This will permenantly remove this redirect from this report. If you do not want this redirect to appear in the missing redirects report going forward, please click confirm.</p>
                                <p>If you clicked the ignore icon by mistake, click cancel.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default ignore-cancel" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-default ignore-confirm" data-dismiss="modal">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php

        } else {
         
            header('Location: /redirects/books/');
            exit;
            
        }

    } 

?>

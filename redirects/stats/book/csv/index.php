<?php

    $id = $_GET['id'];

    header("Content-type: text/csv");
    header("Content-Transfer-Encoding: UTF-8");
    header("Content-Disposition: attachment; filename=stats-". $id .".csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    require '../../../includes/functions.php';

    if($_SERVER['REMOTE_ADDR'] != $ip1 && $_SERVER['REMOTE_ADDR'] != $ip2 && $_SERVER['REMOTE_ADDR'] != $ip3) {
        header("Location: http://paradigmeducation.com/");
    } else {
        
        
        /* Get Hit Tracking Data */
        
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
        
        /* Build Final Array */
            
        echo "Sub Domain,Domain,Redirect String,Destination URL,Hits\n";
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
            
            echo '"'. $sub .'","'. $domain .'","'. $string .'","'. $destination .'","'. $count .'"';
            echo "\n";

        }

    } 

?>

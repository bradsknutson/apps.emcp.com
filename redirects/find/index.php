<?php

    include '../includes/functions.php';

    $sub = $mysqli->real_escape_string( $_GET['sub'] );
    $domain = $mysqli->real_escape_string( $_GET['domain'] );
    $redirect = $mysqli->real_escape_string( $_GET['redirect'] );

    $redir_match = "SELECT a.id, a.destination
                    FROM redirects a, book b, root_domains c, sub_domains d
                    WHERE a.book_id = b.id
                    AND c.id = b.domain_id
                    AND d.id = b.sub_id
                    AND a.string = '". $redirect ."'
                    AND c.domain = '". $domain ."'
                    AND d.sub = '". $sub ."'
                    AND a.deleted = '0'";

    $redir_match_result = $mysqli->query($redir_match);
    $redir_match_count = $redir_match_result->num_rows;

    if( $redir_match_count > 0 ) {

        /***************************************************/
        /***************REDIRECT MATCH FOUND****************/
        /***************************************************/

        $redir_match_row = $redir_match_result->fetch_array();
        $redir_match_result->close();

        $destination = '/redirects/links/edit/'. $redir_match_row['id'];

        redirect($destination);

    } else {
        
        $location = '/redirects/search/'. $redirect;
        
        redirect($location);
        
    }

?>
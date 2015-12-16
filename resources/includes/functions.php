<?php

    include 'base.php';
    include 'vars.php';
    include 'con.php';

    include 'get_book_info.php';

    include 'get_unit_count.php';
    include 'get_covers.php';
    
    function getLevels($n) {
        $x = 0;
        while( $x < $n ) {
            $x++;
            echo '<div class="color anim level-'. $x .' size'. $n .'">Level '. $x .'</div>';
        }
        echo '<div class="clearfix"></div>';
    }

    function getUnits($n,$u) {        
        foreach( $n as $a ) {
            $x = 0;
            echo '<p class="units-level-'. $a['level'] .'">'. $u .' ';
            while( $x < $a['unitCount'] ) {
                $x++;
                echo '<a class="unit-link level-'. $a['level'] .'-unit-'. $x .'" href="#level-'. $a['level'] .'-unit-'. $x .'">'. $x .'</a>';
                if( $x != $a['unitCount'] ) {
                    echo ' | ';
                }
            }
            echo '</p>';
        }
    }

    function displaySlices($n,$u) {
        $z = '';
        foreach( $n as $a ) {
            $x = 0;
            while( $x < $a['unitCount'] ) {
                $x++;
                $z .= '<div class="slice slice-level-'. $a['level'] .' level-'. $a['level'] .'-unit-'. $x .'">';
                $z .= '     <div class="slice-level" id="'. $a['level'] .'"></div>';
                $z .= '     <div class="slice-unit" id="'. $x .'"></div>';
                $z .= '     <h2 class="unit-title">'. $u .' '. $x .'</h2>';
                $z .= '     <div class="resources"></div>';
                $z .= '</div>';
            }
        }
        echo $z;
    }

    // Check if Resource Page is Loaded in Passport (URL has '/passport/' following Program String)

    function isPassport() {
        global $passport;
        if( $passport == 'passport' ) {
            return true;
        } else {
            return false;
        }
    }
    // Check if Resource Page is Loaded with a User ID ($uid)

    function isUser() {
        global $uid;
        if( is_numeric($uid) ) { 
            return true;
        } else {
            return false;
        }
    }

?>
<?php

    ob_start("ob_gzhandler");

    include 'con.php';
    
    $lessonTitle = $_POST['lesson'];
    $id = $_POST['id'];
    $lev = $_POST['level'];
    $u = $_POST['unit'];

    $getIntType = 'SELECT b.interaction_long, REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(a.interaction_id, "0", ""),"1", ""),"2", ""),"3", ""),"4", ""),"5", ""),"6", ""),"7", ""),"8", ""),"9", ""),".", "") as interaction, COUNT(a.id) as count
                    FROM resource_meta_data a, interaction_master b
                    WHERE a.program_id = "'. $id .'"
                    AND a.level = "'. $lev .'"
                    AND a.unit = "'. $u .'"
                    AND b.interaction_short = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE (a.interaction_id, "0", ""),"1", ""),"2", ""),"3", ""),"4", ""),"5", ""),"6", ""),"7", ""),"8", ""),"9", ""),".", "")
                    AND b.interaction_short !=  \'\'
                    GROUP BY interaction';

    $getIntTypeResult = $mysqli->query($getIntType);
    
    while($getIntTypeRow = $getIntTypeResult->fetch_array()) {
        $getIntTypeRows[] = $getIntTypeRow;
    }
    $getIntTypeResult->close();

    $getLessons = 'SELECT lesson 
                    FROM resource_meta_data
                    WHERE program_id = "'. $id .'"
                    AND level = "'. $lev .'"
                    AND unit = "'. $u .'"
                    AND lesson != "0"
                    GROUP BY lesson';

    $getLessonsResult = $mysqli->query($getLessons);
    
    while($getLessonsRow = $getLessonsResult->fetch_array()) {
        $getLessonsRows[] = $getLessonsRow;
    }
    $getLessonsResult->close();
    
    $filters = array(
        'lesson',
        'interaction_type',
    );

    $end = '         
        </select>
    </div>';

    // Lessons filter 

    $l = '      <div class="select-style select-lesson">
            <select class="l'. $lev .'u'. $u .'-lesson">
                <option value="choose">'. $lessonTitle .' (All)</option>';

    foreach( $getLessonsRows as $les ) {
        $l .= '                 <option value="'. $les['lesson'] .'">'. $lessonTitle .' '. $les['lesson'] .'</option>';
    }
    $l .= $end;

    // Interaction Type filter

    $int_type = '      <div class="select-style select-interaction_type">
            <select class="l'. $lev .'u'. $u .'-interaction_type">
                <option value="choose">Interaction Type (All)</option>';


    foreach( $getIntTypeRows as $int ) {
        $int_type .= '                 <option value="'. $int['interaction'] .'">'. $int['interaction_long'] .'</option>';
    }
    $int_type .= $end;

    // Scripts

    $s .= '<script>$(document).ready(function() {
    ';
    foreach( $filters as $f ) {
        $s .= '$(\'.l'. $lev .'u'. $u .'-'. $f .'\').change(function() {
                $'. $f .' = $(this).val();';
        
        if( $f == 'lesson' ) {
            $s .= '
            if( $'. $f .' != \'choose\' ) {
                    $(this).parent().addClass(\'lessonFilterSelected\');
                    $(\'.l'. $lev .'u'. $u .'-interaction_type\').parent().show();
                } else {
                    $(this).parent().removeClass(\'lessonFilterSelected\');
                    $(\'.l'. $lev .'u'. $u .'-interaction_type\').val(\'choose\').change().parent().hide();
                }
            ';
        }
        
        $s .= '
                if( $'. $f .' != \'choose\' ) {
                    $(\'.l'. $lev .'u'. $u .'frame .'. $f .'\').parent().parent().parent().parent().addClass(\'hidden'. $f .'\');
                    $(\'.l'. $lev .'u'. $u .'frame .'. $f .'#\' + $'. $f .').parent().parent().parent().parent().removeClass(\'hidden'. $f .'\');
                } else {
                    $(\'.l'. $lev .'u'. $u .'frame .'. $f .'\').parent().parent().parent().parent().removeClass(\'hidden'. $f .'\');
                }
            });
        ';       
    }

    $s .= '$(\'.level-'. $lev .'-unit-'. $u .' select\').change(function() {
        $(\'.level-'. $lev .'-unit-'. $u .' .expanded,.level-'. $lev .'-unit-'. $u .' .collapsed\').each(function() { 
            $x =  $(this).attr(\'class\').split(\' \')[0];
 
            $length = $(\'.\' + $x + \'.resource_individuals\').not(\'.hiddenlesson,.hiddeninteraction_type\').length;
            
            if( $length == \'0\' ) {
                $(this).addClass(\'noneAvailable\');
            } else {
                $(this).removeClass(\'noneAvailable\');
            }
            
            $(\'.l'. $lev .'u'. $u .'frame\').sly(\'reload\');
            
        });
    });';

    
    $s .= '
        });</script>';

    echo $l . $int_type;

?>
<?php

    ob_start("ob_gzhandler");

    include 'con.php';
    include 'base.php';

    $id = $_POST['id'];
    $level = $_POST['level'];
    $unit = $_POST['unit'];
    $lesson = $_POST['lesson'];

    $getResources = 'SELECT a.id, b.activity_name, a.activity_label, a.lesson, a.url, a.book_id, a.page, a.activity_number, a.interaction_id, c.interaction_long, a.score_possible
                        FROM  resource_meta_data a, resource_master b, interaction_master c
                        WHERE a.resource_id = b.id 
                        AND program_id =  "'. $id .'"
                        AND level =  "'. $level .'"
                        AND unit =  "'. $unit .'"
                        AND c.interaction_short = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(a.interaction_id, "0", ""),"1", ""),"2", ""),"3", ""),"4", ""),"5", ""),"6", ""),"7", ""),"8", ""),"9", ""),".", "")
                        ORDER BY page * 1 ASC';

    $getResourcesResult = $mysqli->query($getResources);
    
    while($getResourcesRow = $getResourcesResult->fetch_array()) {
        $getResourcesRows[] = $getResourcesRow;
    }
    $getResourcesResult->close();


    $getActivityType = 'SELECT b.activity_name
                        FROM resource_meta_data a, resource_master b
                        WHERE a.program_id = "'. $id .'"
                        AND a.resource_id = b.id
                        GROUP BY a.resource_id
                        ORDER BY b.sort_order ASC';
    $getActivityTypeResult = $mysqli->query($getActivityType);
    
    while($getActivityTypeRow = $getActivityTypeResult->fetch_array()) {
        $getActivityTypeRows[] = $getActivityTypeRow;
    }
    $getActivityTypeResult->close();

    // Begin Building Resource Scroller

    foreach( $getActivityTypeRows as $act ) {
        $act_clean_array[] = $act['activity_name'];
        $activity = str_replace("'", "", explode(' ', $act['activity_name']) );
        $act_lower_array[] = strtolower($activity['0']);
    }
        
    $types = array_combine($act_clean_array, $act_lower_array);

    $c = '<div class="l'. $level .'u'. $unit .'frame frame">
            <ul class="slidee">';

    foreach( $types as $k => $v ) {
    
        $c .= '<li class="l'. $level .'u'. $unit . $v .' collapsed">
                <div class="resource-cover cover-'. $v .' cover-'. $v .'l'. $level .'">
                    <div class="resource_label">'. $k .'</div>
                </div>
              </li>';

        foreach( $getResourcesRows as $i ){
            if( $i['activity_name'] == $k ) {
                
                if( strlen($i['activity_label']) >= 24 ) {
                    $label = mb_substr($i['activity_label'],0,21, 'utf-8') . '...';
                } else {
                    $label = $i['activity_label'];   
                }
                
                $interaction_type = explode('.', $i['interaction_id']);
                $c .= '<li class="l'. $level .'u'. $unit . $v .' resource_individuals">
                            <a class="resource_item-link" href="'. $i['url'] .'" target="_blank">
                                <div class="resource_item '. $v .'">
                                    <div class="resource_label">'. $label .'</div>
                                    <div class="info_icon"></div>
                                    <div class="resource_modal_info">
                                        <div class="resource-meta-data label"><strong>Activity Name: '. $i['activity_label'] .'</strong></div>
                                        <div class="resource-meta-data level" id="'. $level .'">Level '. $level .'</div>
                                        <div class="resource-meta-data unit" id="'. $unit .'">Unit '. $unit .'</div>
                                        <div class="resource-meta-data lesson" id="'. $i['lesson'] .'">Lesson '. $i['lesson'] .'</div>
                                        <div class="resource-meta-data page" id="'. $i['page'] .'">Page '. $i['page'] .'</div>
                                        ';
                if( !empty( $i['activity_number'] ) ) {
                    $c .= '
                                        <div class="resource-meta-data activity_number" id="'. $i['activity_number'] .'">Activity Number '. $i['activity_number'] .'</div> 
                                        ';
                } 
                                        
                if( !empty( $i['score_possible'] ) ) {
                    $c .= '
                                        <div class="resource-meta-data score_possible" id="'. $i['score_possible'] .'">Score Possible: '. $i['score_possible'] .'</div>';   
                }
                                        
                $c .= '
                                        <div class="resource-meta-data interaction">Interaction Type: '. $i['interaction_long'] .'</div>
                                        <div class="resource-meta-data interaction_type" id="'. $interaction_type[0] .'"></div>
                                        <div class="resource-meta-data interaction_num" id="'. $interaction_type[1] .'"></div>
                                        <div class="resource-meta-data activity_id" id="'. $i['id'] .'"></div>
                                        <div class="resource-meta-data book_id" id="'. $i['book_id'] .'"></div>
                                        <div class="resource-meta-data url" id="'. $i['url'] .'"></div>
                                        <div class="modalClose anim"></div>
                                    </div>
                                </div>
                            </a>
                        </li>';
            }
        }    
        
    }



    $c .= '     </ul>
            </div>
            <div class="scrollbar">
                <div class="handle"></div>
            </div>
            <script>
            // Sly

            var $frame  = $(\'.l'. $level .'u'. $unit .'frame\');
            var $slidee = $frame.children(\'ul\').eq(0);
            var $wrap   = $frame.parent();

            // Call Sly on frame
            $frame.sly({
                horizontal: 1,
                itemNav: \'basic\',
                activateOn: \'click\',
                mouseDragging: 1,
                touchDragging: 1,
                startAt: 0,
                scrollBar: $wrap.find(\'.scrollbar\'),
                scrollBy: 1,
                speed: 300,
                elasticBounds: 0,
                dragHandle: 1,
                dynamicHandle: 1,
                clickBar: 1,
            });
            
            $(\'.l'. $level .'u'. $unit .'frame .collapsed\').each(function() {
                $coverSelector = $(this).attr(\'class\').split(\' \')[0];
                
                if( $(\'.\' + $coverSelector + \'.resource_individuals\').length == 0 ) {
                    $(\'.\' + $coverSelector).remove();
                }
                
            });
            
            var url = \''. $base .'includes/get_filters.ajax.php\';
            var $this = $(\'.level-'. $level .'-unit-'. $unit .'\');
            var lesson = \''. $lesson .'\';
            
            ajaxRequest( url, '. $id .', '. $level .', '. $unit .', $this, lesson );
            
            </script>';

    echo $c;

?>
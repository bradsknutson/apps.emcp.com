<?php

    include 'con.php';

    $id = $_POST['id'];
    $level = $_POST['level'];
    $unit = $_POST['unit'];

    $getResources = 'SELECT a.id, b.activity_name, a.label, a.lesson, a.link
                        FROM  resource_meta_data a, resource_master b 
                        WHERE a.resource_id = b.id 
                        AND program_id =  "'. $id .'"
                        AND level =  "'. $level .'"
                        AND unit =  "'. $unit .'"';
    $getResourcesResult = $mysqli->query($getResources);
    
    while($getResourcesRow = $getResourcesResult->fetch_array()) {
        $getResourcesRows[] = $getResourcesRow;
    }
    $getResourcesResult->close();


    $getActivityType = 'SELECT * FROM resource_master';
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

    $jsArray = '[';
    foreach( $act_lower_array as $jsVal ) { 
        $jsArray .= '\''. $jsVal .'\',';
    }
    $jsArray = rtrim($jsArray, ',') .']';
    
        
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
                $c .= '<li class="l'. $level .'u'. $unit . $v .' resource_individuals">
                            <a class="resource_item-link" href="'. $i['link'] .'" target="_blank">
                                <div class="resource_item '. $v .'">
                                    <div class="resource_label">'. $i['label'] .'</div>
                                    <div class="resource-meta-data lesson" id="'. $i['lesson'] .'"></div>
                                    <div class="resource-meta-data activity_id" id="'. $i['id'] .'"></div>
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
            
            $(document).on(\'click\', \'.collapsed\', function() {
            
                $(this).removeClass(\'collapsed\').addClass(\'expanded\');
        
                $resource = \'.\' + $(this).attr(\'class\').split(\' \')[0];
                $frame = \'.\' + $(this).parent().parent().attr(\'class\').split(\' \')[0];
                
                $($resource + \'.resource_individuals\').show();
                $($frame).sly(\'reload\');

            });
                        
            $(document).on(\'click\', \'.expanded\', function() {
            
                $(this).removeClass(\'expanded\').addClass(\'collapsed\');
        
                $resource = \'.\' + $(this).attr(\'class\').split(\' \')[0];
                $frame = \'.\' + $(this).parent().parent().attr(\'class\').split(\' \')[0];
                
                $($resource + \'.resource_individuals\').hide();
                $($frame).sly(\'reload\');

            });
            
            $(\'.l'. $level .'u'. $unit .'frame .collapsed\').each(function() {
                $coverSelector = $(this).attr(\'class\').split(\' \')[0];
                
                if( $(\'.\' + $coverSelector + \'.resource_individuals\').length == 0 ) {
                    $(\'.\' + $coverSelector).remove();
                    console.log($coverSelector + \' has been be removed\');
                }
                
            });
            
            </script>';

    echo $c;

?>
<?php

    $preload = 'SELECT a.activity_name, b.level, b.cover, COUNT(c.id) as count
                    FROM resource_master a, resource_asset_data b, resource_meta_data c
                    WHERE b.resource_id = a.id
                    AND c.resource_id = a.id
                    AND c.level = b.level
                    AND b.program_id = "'. $id .'"
                    GROUP BY b.cover';

    $preloadResult = $mysqli->query($preload);
    
    while($preloadRow = $preloadResult->fetch_array()) {
        $preloadRows[] = $preloadRow;
    }
    $preloadResult->close();

    $c = count($preloadRows);

    $js1 .= '	<script type="text/javascript">
			var images = new Array()
			function preload() {
				for (i = 0; i < preload.arguments.length; i++) {
					images[i] = new Image()
					images[i].src = preload.arguments[i]
				}
			}
			preload(
';

    $i = '0';
    while ( $i < $c ) {

        $js2 .= '					"'. $base .'img/covers/'. $preloadRows[$i]['cover'] .'",
        ';
        $i++;
        
    }

    $js3 .= '			);
	</script>';

    echo $js1 . rtrim( $js2, ",
    ") . $js3;

?>
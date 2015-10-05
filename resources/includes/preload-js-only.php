<?php

    $preload = 'SELECT cover
                    FROM resource_asset_data
                    WHERE program_id = "'. $id .'"
                    ORDER BY level';

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
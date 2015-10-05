<?php 

    ob_start("ob_gzhandler");
  
    include 'header.php';
    include 'includes/get_unit_count.php';
    include 'includes/functions.php';

?>
    <div class="program_id" id="<?php echo $id; ?>"></div>
        <div class="hero">
            <img src="<?php echo $base; ?>img/heros/<?php echo $hero; ?>" alt="" />
            <div class="shadow"></div>
            <div class="title"><?php echo $title; ?> Program Resources</div>
        </div>
        <div class="container">
            <div class="xmenu">
                <div class="levels">
                    <?php getLevels($levels); ?>
                </div>
                <div class="units">
                    <?php getUnits($getUnitCountRows,$unit_title); ?>
                </div>
                <div class="lesson" id="<?php echo $lesson_title; ?>"></div>
            </div>
            <?php displaySlices($getUnitCountRows,$unit_title); ?>
        </div>
<?php include 'footer.php'; ?>
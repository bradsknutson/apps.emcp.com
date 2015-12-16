<?php include 'header.php'; ?>
    <div class="program_id" id="<?php echo $id; ?>"></div>
        <?php if( !isPassport() ) { ?>
        <div class="hero">
            <img src="<?php echo $base ?>img/heros/<?php echo $hero ?>" alt="" />
            <div class="shadow"></div>
            <div class="title"><?php echo $title; ?> Program Resources</div>
        </div>
        <?php } ?>
        <div class="container">
            <div class="xmenu">
                <div class="levels">
                    <?php getLevels($levels); ?>
                </div>
                <div class="units">
                    <?php getUnits($getUnitCountRows,$unit_title); ?>
                </div>
                <?php if( isPassport() ) { ?>
                <div class="select-style select-assignable">
                    <select class="assignable-options">
                        <option value="choose">Assignable Activities</option>
                        <option value="1">Show Assignable Only</option>
                        <option value="0">Show All Activities</option>         
                    </select>
                </div>
                <?php } ?>
                <div class="lesson" id="<?php echo $lesson_title; ?>"></div>
            </div>
            <?php displaySlices($getUnitCountRows,$unit_title); ?>
        </div>
<?php include 'footer.php'; ?>
        <?php if( !isPassport() ) { ?>
        <div class="footer">
            <div class="emcp"></div>
            <div class="links">
                <a href="http://store.emcp.com/about-emcp/news-and-events/" class="xlink">News</a><span class="fespa">--</span>
                <a href="http://store.emcp.com/customer-service" class="xlink">Customer Service</a><span class="fespa">--</span>
                <a href="http://store.emcp.com/local-account-manager-locator/" class="xlink">Sales</a><span class="fespa">--</span>
                <a href="http://store.emcp.com/policies-and-order-information/" class="xlink">Terms and Conditions</a>
            </div> <!-- end links -->	
            <div class="linea"></div>
            <div class="social">
                <a href="https://twitter.com/EMCPublishing" target="_blank"><img src="<?php echo $base; ?>img/twitter.png"></a>
                <a href="https://www.facebook.com/EMCPublishing" target="_blank"><img src="<?php echo $base; ?>img/facebook.png"></a>
                <a href="http://vimeo.com/emcpublishing" target="_blank"><img src="<?php echo $base; ?>img/vimeo.png"></a>
            </div> <!-- end social -->	
            <div class="copy">&copy; 2014 EMC Publishing, LLC. All Rights Reserved.<br />EMC is a division of <a href="http://newmountainlearning.com/" target="_blank" class="nml"><img src="<?php echo $base; ?>img/NML_logo.png" height="22px"> New Mountain Learning, LLC</a><br />EMC Publishing, LLC, 875 Montreal Way, St. Paul, MN 55102 &bull; 800-328-1452 &bull; Fax: 800-328-4564
            </div>
        </div> <!-- end footer -->
        <?php } ?>
    </div> <!-- end xcenter -->
    <div id="totop" class="anim"></div>
    <div class="modalBackground">
        <div class="modalContainer anim"></div>
    </div>
    <div class="loading">
        <div class="loader">
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
        </div>
     </div>
    <?php if( isPassport() ) { include 'includes/footer-passport.php'; } ?>
    <?php if( isUser() ) { ?>
    <script type="text/javascript">var $uid = <?php echo $uid;  ?>;</script>
    <?php } ?>
    <script src="<?php echo $base; ?>js/custom.js"></script>
    <script src="<?php echo $base; ?>js/complete.js"></script>
</body>
</html>
<?php 

    $mysqli->close();

?>
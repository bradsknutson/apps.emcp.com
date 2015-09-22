        <div class="footer">
            <div class="emcp"></div>
            <div class="links">
                <a href="http://store.emcp.com/about-emcp/news-and-events/" class="xlink">News</a><span class="fespa">--</span>
                <a href="http://store.emcp.com/contact-us/customer-service" class="xlink">Customer Service</a><span class="fespa">--</span>
                <a href="http://store.emcp.com/contact-us/local-account-manager-locator/" class="xlink">Sales</a><span class="fespa">--</span>
                <a href="http://store.emcp.com/policies-and-order-information/" class="xlink">Terms and Conditions</a>
            </div> <!-- end links -->	
            <div class="linea"></div>
            <div class="copy">© 2014 EMC Publishing, LLC. All Rights Reserved.<br />EMC is a division of <a href="http://newmountainlearning.com/" target="_blank" class="nml"><img src="img/NML_logo.png" height="22px"> New Mountain Learning, LLC</a><br />EMC Publishing, LLC, 875 Montreal Way, St. Paul, MN 55102 &bull; 800-328-1452 &bull; Fax: 800-328-4564
            </div>
            <div class="social">
                <a href="https://twitter.com/EMCPublishing" target="_blank"><img src="img/twitter.png"></a>
                <a href="https://www.facebook.com/EMCPublishing" target="_blank"><img src="img/facebook.png"></a>
                <a href="http://vimeo.com/emcpublishing" target="_blank"><img src="img/vimeo.png"></a>
            </div> <!-- end social -->	

        </div> <!-- end footer -->
        <div id="xmses" style="display:none"></div>
        <div id="xese" style="display:none"></div>
        <div id="selinput" style="display:none"></div>
        <div id="totop">Go to Top</div>
    </div> <!-- end xcenter -->
    <script>
        $(document).scroll(function () {
            var yx = $(this).scrollTop();
            if (yx > 200) {
                $('#totop').fadeIn();
            } else {
                $('#totop').fadeOut();
            }

        });

        $('#totop').click(function(){$("html").stop().scrollTo( { top:10,left:0} , 1000 );});
    </script>
</body>
</html>
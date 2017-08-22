<!-- .site-footer -->
<footer class="site-footer" itemscope itemtype="https://schema.org/WPFooter">
<div class="ht-container">


	<?php if( ''!=(get_theme_mod( 'ht_setting__copyright' ))) : ?>
		<div class="site-footer__copyright" role="contentinfo"><?php echo get_theme_mod( 'ht_setting__copyright' ); ?></div>
	<?php endif; ?>
	<?php if ( has_nav_menu( 'nav-site-footer' ) ) { ?>
		<nav class="nav-footer">
			<?php wp_nav_menu( array('theme_location' => 'nav-site-footer', 'menu_id' => false, 'menu_class' => false, 'container_class' => false, 'depth' => 1 )); ?>
		</nav>
	<?php } ?>

</div>
</footer> 
<!-- /.site-footer -->

<div class="ht-global-overlay"></div>
<?php wp_footer(); ?>



</div>
<!-- /.ht-site-container -->
<!-- Updated Support Div Brian 7/20/17 -->
<div id="support-window" class="supportdiv"></div>
<script type="text/javascript">
var emcpSupportLinkId = 'support-window';

var emcpSupportParams = {
    type: '2',
    name: readCookie('wizName'),
    email: decodeURIComponent(readCookie('wizEmail')),
    role: readCookie('wizRole'),
    refurl: decodeURIComponent(readCookie('wizRefUrl')),
    ltiurl: decodeURIComponent(readCookie('wizLtiUrl')),
    courseid: readCookie('wizCourseId'),
    platform: 'SNAP 2016 Canvas',
}

var head = document.getElementsByTagName('head')[0];
var script = document.createElement('script');
script.id = 'emcpSupportJs';
script.type = 'text/javascript';
script.src = 'https://apps.emcp.com/support/lib/js/iframe.js';
head.appendChild(script);
</script>
</body>
</html>
<?php
add_action( 'wp_enqueue_scripts', 'magone_child_enqueue_styles' );
function magone_child_enqueue_styles() {
	wp_enqueue_style( 'magone-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array(
			'magone-style', 
			'magone-responsive',
			'magone-print',
		)
    );
}
add_action( 'after_setup_theme', 'magone_child_lang_setup' );
function magone_child_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'magone', $lang );
}
function google_analytics() { ?>
<script>

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-76194265-3', 'auto');
<?php if (is_singular('post') : ?>
  ga('set', 'dimension1', '<?php the_author(); ?>');
  <?php endif; ?>
ga('send', 'pageview');

</script> 

<?php }
add_action ('wp_head', 'google_analytics');


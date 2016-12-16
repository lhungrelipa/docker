<?php
function sneeit_utilities_scripts_styles_support_font_awesome_enqueue() {
	wp_register_style('sneeit-font-awesome', SNEEIT_PLUGIN_URL_FONT_AWESOME, array(), SNEEIT_PLUGIN_VERSION);
	if (!sneeit_is_gpsi()) {
		wp_enqueue_style('sneeit-font-awesome');
	}
}
function sneeit_utilities_scripts_styles_support_font_awesome_wp_footer() {
	wp_enqueue_style('sneeit-font-awesome');
}
function sneeit_utilities_scripts_styles_support_font_awesome() {
	add_action( 'wp_enqueue_scripts', 'sneeit_utilities_scripts_styles_support_font_awesome_enqueue');
	if (sneeit_is_gpsi()) {
		add_action( 'wp_footer', 'sneeit_utilities_scripts_styles_support_font_awesome_wp_footer');
	}
}
add_action('sneeit_support_font_awesome', 'sneeit_utilities_scripts_styles_support_font_awesome', 1);

function sneeit_utilities_scripts_styles_support_thread_comments_enqueue() {
	wp_enqueue_script('jquery');

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
function sneeit_utilities_scripts_styles_support_thread_comments() {
	add_action( 'wp_enqueue_scripts', 'sneeit_utilities_scripts_styles_support_thread_comments_enqueue');	
}
add_action('sneeit_support_thread_comments', 'sneeit_utilities_scripts_styles_support_thread_comments', 1);
<?php
global $Sneeit_Theme_Options;
include_once 'theme-options-ajax.php';

function sneeit_theme_options_admin_menu() {
	global $Sneeit_Theme_Options;
	
	if (!isset($Sneeit_Theme_Options['menu-title'])) {
		$Sneeit_Theme_Options['menu-title'] = esc_html__('Theme Options', 'sneeit');
	}
	
	if (!isset($Sneeit_Theme_Options['page-title'])) {
		$Sneeit_Theme_Options['page-title'] = esc_html__('Theme Options', 'sneeit');
	}
	
	
	add_theme_page( 
		$Sneeit_Theme_Options['page-title'],
		$Sneeit_Theme_Options['menu-title'], 
		'manage_options',
		'sneeit-theme-options', 
		'sneeit_theme_options_html'
	);
}
function sneeit_theme_options_html() {
	global $Sneeit_Theme_Options;
	if (!isset($Sneeit_Theme_Options['page-title'])) {
		$Sneeit_Theme_Options['page-title'] = esc_html__('Theme Options', 'sneeit');
	}
	include_once( sneeit_framework_plugin_path('/includes/controls/controls.php') );
	
	echo '<div class="wrap">'.
		'<h1>'.$Sneeit_Theme_Options['page-title'].'</h1>';
		if (isset($Sneeit_Theme_Options['html-before'])) {
			echo $Sneeit_Theme_Options['html-before'];
		}
		
		
		include_once 'theme-options-html.php';
		
		if (isset($Sneeit_Theme_Options['html-after'])) {
			echo $Sneeit_Theme_Options['html-after'];
		}
				
	echo '</div>';
		
	include_once 'theme-options-enqueue.php';
}

add_action('sneeit_theme_options', 'sneeit_theme_options_init',  10, 1); // end of filter
function sneeit_theme_options_init($args) {
	// validate args
	if (!isset($args['declarations']) || !is_admin()) {
		return;
	}

	// save it
	global $Sneeit_Theme_Options;	
	$Sneeit_Theme_Options = $args;
	
	add_action( 'admin_menu', 'sneeit_theme_options_admin_menu');
}

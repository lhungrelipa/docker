<?php

add_action( 'admin_enqueue_scripts', 'sneeit_lib_admin_enqueue_scripts');
function sneeit_lib_admin_enqueue_scripts () {
	// register global styles
	wp_register_style('sneeit-font-awesome', SNEEIT_PLUGIN_URL_FONT_AWESOME, array(), SNEEIT_PLUGIN_VERSION );    	
	wp_register_style('sneeit-plugin-chosen', SNEEIT_PLUGIN_URL_JS_PLUGINS . 'chosen/chosen.jquery.css', array(), SNEEIT_PLUGIN_VERSION);
	
	wp_enqueue_style('sneeit-admin', SNEEIT_PLUGIN_URL_CSS . 'admin.css', array(), SNEEIT_PLUGIN_VERSION);
	wp_enqueue_style('sneeit-font-awesome');
	
	// register global scripts
	wp_enqueue_script('iris');
	wp_enqueue_media();	
	
	wp_register_script('sneeit-lib', SNEEIT_PLUGIN_URL_JS . 'lib.js', array('jquery'), SNEEIT_PLUGIN_VERSION, false);
	wp_register_script('sneeit-plugin-chosen', SNEEIT_PLUGIN_URL_JS_PLUGINS .'chosen/chosen.jquery.min.js', array( 'jquery' ), SNEEIT_PLUGIN_VERSION, false);

	wp_register_script('sneeit-web-fonts', 'https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js', array(), SNEEIT_PLUGIN_VERSION, true);
	
//	wp_enqueue_script('sneeit-font-awesome');
	// localize script
	wp_localize_script('sneeit-lib', 'Sneeit_Lib_Options', array(
		'location' => array(
			'wp_includes_css' => includes_url('css'),
			'wp_includes_js' => includes_url('js')
		)
	));
}


add_action( 'customize_controls_enqueue_scripts', 'sneeit_lib_enqueue_customize_controls_enqueue_scripts');
function sneeit_lib_enqueue_customize_controls_enqueue_scripts () {
	wp_register_style('sneeit-font-awesome', SNEEIT_PLUGIN_URL_FONT_AWESOME, array(), SNEEIT_PLUGIN_VERSION );    
}


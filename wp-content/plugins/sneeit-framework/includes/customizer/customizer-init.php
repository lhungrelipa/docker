<?php
/* TODO
 * - Add validator for customizer declaration
 */
// local global variables
global $Sneeit_Customize_Declarations;$Sneeit_Customize_Declarations = array();

// local defines
define('SNEEIT_DEFAULT_CUSTOMIZER_PRIORITY', 50);

// local requirements
require_once 'customizer-lib.php';


add_action('sneeit_setup_customizer', 'sneeit_customizer_init_setup_customizer',1,1);
function sneeit_customizer_init_setup_customizer($declarations) {
	global $Sneeit_Customize_Declarations;
	$Sneeit_Customize_Declarations = $declarations;
	if (sneeit_customize_has_fonts($declarations)) {	
		sneeit_get_uploaded_fonts();
	}
	require_once 'customizer-default.php';
}


add_action( 'customize_register', 'sneeit_customizer_init_customize_register');
function sneeit_customizer_init_customize_register($wp_customize) {
	global $Sneeit_Customize_Declarations;
	
	if (is_array($Sneeit_Customize_Declarations)) {
		require_once 'customizer-control.php';
		require_once 'customizer-register.php';		
	}
}

require_once 'customizer-enqueue.php';
require_once 'customizer-out.php';

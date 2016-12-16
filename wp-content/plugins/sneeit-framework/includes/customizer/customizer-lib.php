<?php
/*apply: https://make.wordpress.org/core/2014/07/08/customizer-improvements-in-4-0/*/
/*customizer lib, only use in customizer extension*/
function sneeit_add_customize_setting($wp_customize, $section_id, $setting_id, $setting_declarations) {	
	// add setting to its section
	if (!class_exists( 'WP_Customize_Control' ) ) {
		return;
	}
	if (!isset($setting_declarations['type'])) {
		$setting_declarations['type'] = 'text';
	}
	$default_value = (isset($setting_declarations['default']) ? 
						$setting_declarations['default'] : 
						(($setting_declarations['type'] == 'number') ? 0 : ''));
	$wp_customize->add_setting($setting_id , array(
		'default' => $default_value
		)
	);
	$control_id = $setting_id . '_'.$setting_declarations['type'].'_control';
	$setting_declarations['attr'] = array(
		'data-customize-setting-link' => $setting_id
	);
	
	$control_options = array(				
		'label'			=> (/*you can input both title or label are also ok*/
			isset($setting_declarations['label']) ? 
				$setting_declarations['label'] : 
				(isset($setting_declarations['title']) ? 
					isset($setting_declarations['title']) : 
					sneeit_slug_to_title($setting_id)
				)
			),
		'priority'		=> (isset($setting_declarations['priority'])? $setting_declarations['priority'] : SNEEIT_DEFAULT_CUSTOMIZER_PRIORITY),
		'section'		=> $section_id,
		'settings'		=> $setting_id,
		'type'			=> $setting_declarations['type'],
		'setting_id'	=> $setting_id,
		'description'	=> (isset($setting_declarations['description'])? $setting_declarations['description'] : ''),
		'declarations'	=> $setting_declarations,
	);
	
	$input_attrs = array();
	if (isset($setting_declarations['min'])) {
		$input_attrs['min'] = $setting_declarations['min'];
	} else {
		$input_attrs['min'] = 0;
	}

	if (isset($setting_declarations['max'])) {
		$input_attrs['max'] = $setting_declarations['max'];
	} else {
		$input_attrs['max'] = 1000;
	}

	if (isset($setting_declarations['step'])) {
		$input_attrs['step'] = $setting_declarations['step'];
	} else {
		$input_attrs['step'] = 1;
	}

	if (isset($setting_declarations['class'])) {
		$input_attrs['class'] = $setting_declarations['class'];
	} else {
		$input_attrs['class'] = '';
	}

	if (isset($setting_declarations['style'])) {
		$input_attrs['style'] = $setting_declarations['style'];
	} else {
		$input_attrs['style'] = '';
	}
	
	if (count($input_attrs)) {
		$control_options['input_attrs'] = $input_attrs;
	}
	
	if (isset($setting_declarations['none'])) {
		$control_options['none'] = $setting_declarations['none'];
	}
	if (isset($setting_declarations['prefix'])) {
		$control_options['prefix'] = $setting_declarations['prefix'];
	}
	
	
	// modify the declaration
	if (isset($setting_declarations['choices'])) {
		$control_options['choices'] = $setting_declarations['choices'];			
		
		// check if choices contain special HTML tags, we will force it as visual picker
		$c_choice = current($control_options['choices']);
		if (strpos($c_choice, '<') !== false && strpos($c_choice, '>') != false	&& $control_options['type'] == 'select') {
			$control_options['type'] = 'visual';
		}
	}
	
//	$wp_customize->add_control(new WP_Customize_Sneeit_Control($wp_customize,$control_id,$control_options));
//	return;
	
	// add control via Customize API
	switch ($control_options['type']) :
		case 'color':
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,$control_id,$control_options));
			break;
		
		case 'media':
			$wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,$control_id,$control_options));
			break;
		
		case 'upload':
		case 'file':
			$control_options['type'] = 'upload';
			$wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize,$control_id,$control_options));
			break;

		case 'image':
			$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,$control_id,$control_options));
			break;
		
		
		default:
			$wp_customize->add_control(new WP_Customize_Sneeit_Control($wp_customize,$control_id,$control_options));
			break;
	endswitch;	
}

function sneeit_customize_has_fonts($declarations) {	
	if (!is_array($declarations)) {
		return false;
	}
	global $Sneeit_Customize_Declarations;
	foreach ($Sneeit_Customize_Declarations as $level_1_id => $level_1_value) :
		if (isset($level_1_value['type']) && ($level_1_value['type'] == 'font' || $level_1_value['type'] == 'font-family')) {
			return true;
		}
		
		$level_1_next = array();
		if (isset($level_1_value['sections'])) {		
			$level_1_next = $level_1_value['sections'];
		} else if (isset($level_1_value['settings'])) {
			$level_1_next = $level_1_value['settings'];
		}


		// next level 1
		foreach ($level_1_next as $level_2_id => $level_2_value) :
			if (isset($level_2_value['type']) && ($level_2_value['type'] == 'font' || $level_2_value['type'] == 'font-family')) {
				return true;
			}
			
			if (isset($level_2_value['settings'])) {			
				// scan for last level of declaration
				foreach ($level_2_value['settings'] as $level_3_id => $level_3_value) {
					if (isset($level_3_value['type']) && ($level_3_value['type'] == 'font' || $level_3_value['type'] == 'font-family')) {
						return true;
					}		
				}

			}
		endforeach;
	endforeach;

	return false;	
}
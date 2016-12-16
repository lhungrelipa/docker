<?php

class Sneeit_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
	var $field_wrapper_id = 0;
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		
        parent::start_el($output, $item, $depth, $args);
		$item_id = esc_attr( $item->ID );
		
        global $Sneeit_Menu_Fields_Declaration;
		if (!is_array($Sneeit_Menu_Fields_Declaration)) {
			return;
		}
		$sneeit_field_html = '';
		ob_start();
		foreach ($Sneeit_Menu_Fields_Declaration as $menu_field_id => $menu_field_declaration) :
			
			$item_value = get_post_meta($item_id, $menu_field_id);						
			if (is_array($item_value)) {
				if (empty($item_value)) {
					$item_value = $menu_field_declaration['default'];
				} else {
					$item_value = $item_value[0];
				}				
			} else {
				$item_value = $menu_field_declaration['default'];
			}
			
			if (isset($menu_field_declaration['depth'])) {
				echo '<style type="text/css">#sneeit-menu-editor-field-'.$this->field_wrapper_id.'{display:none}.menu-item-depth-'.$menu_field_declaration['depth'].' #sneeit-menu-editor-field-'.$this->field_wrapper_id.'{display:block}</style>';
			}
			$menu_field_declaration['id'] = 'edit-menu-item-'.$menu_field_id.'-'.$item_id;
			$menu_field_declaration['name'] = 'menu-item-'.$menu_field_id.'['.$item_id.']';
			if (in_array($menu_field_declaration['type'], array(
					'categories',
					'users',
					'tags',
					'selects',
					'sidebars'
				)
			)){
				$menu_field_declaration['name'] .= '[]';
			}
			
			
			?><div class="field-<?php echo $menu_field_id; ?> description description-wide" id="sneeit-menu-editor-field-<?php echo $this->field_wrapper_id; ?>"><?php
				
			new Sneeit_Controls($menu_field_id, $menu_field_declaration, $item_value);
			
			?></div><?php			
			$this->field_wrapper_id++;
		endforeach;
		$sneeit_field_html .= ob_get_clean();
		$insert_index = strrpos($output, SNEEIT_MENUS_OUTPUT_PREPEND_MARK);		
		if ($insert_index !== false) {
			$output = substr_replace($output, $sneeit_field_html, $insert_index, 0);			
		}
    }
}
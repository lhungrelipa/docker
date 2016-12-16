<?php
global $Sneeit_Setup_Compact_Menu_Args;

add_action('sneeit_setup_compact_menu', 'sneeit_setup_compact_menu_action', 2);
function sneeit_setup_compact_menu_action($args = array()) {
	global $Sneeit_Setup_Compact_Menu_Args;
	
	$Sneeit_Setup_Compact_Menu_Args = wp_parse_args($args, array(
		'responsive_width' => 320,
	));
	
	
	global $Sneeit_Menu_Fields_Declaration;
	$Sneeit_Menu_Fields_Declaration = sneeit_validate_menu_fields_declaration(array(
		'enable_mega' => array(
			'label' => esc_html__('Enable Mega Menu', 'sneeit'),
			'description' => esc_html__('If this menu item is a category, posts will show when you hover it and its sub menu items (if they are categories also). If this item is not a category, sub menu items will show as group links', 'sneeit'),
			'type' => 'checkbox',
			'default' => false,
			'depth' => 0 /*display specific for depth level 0 */
		),
		'show_hide_for_users' => array(
			'label' => esc_html__('Show / Hide Menu for Users', 'sneeit'),
			'description' => esc_html__('Usually use to create login / logout or register links', 'sneeit'),
			'type' => 'select',
			'default' => '',
			'choices' => array(
				'' => esc_html__('Show for All Users', 'sneeit'),
				'logged-in' => esc_html__('Show for Logged in Users only', 'sneeit'),
				'logged-out' => esc_html__('Show for Logged out Users only', 'sneeit'),
			),
		),
		'color' => array(
			'label' => esc_html__('Menu Item Text Color', 'sneeit'),
			'type' => 'color'
		),
		'bg_color' => array(
			'label' => esc_html__('Menu Item Background Color', 'sneeit'),
			'type' => 'color'
		),
		'icon_before' => array(
			'label' => esc_html__('Icon Code Before Text', 'sneeit'),
			'description' => wp_kses(
				sprintf(__('Example: fa-home. <a href="%s" target="_blank">Check Full List of Icon Codes Here</a>', 'sneeit'), esc_url('http://fortawesome.github.io/Font-Awesome/icons/')),
				array(
					'a' => array(
						'href' => array(),
						'target' => array()
					)
				)
			)
		),
		'icon_after' => array(
			'label' => esc_html__('Icon Code After Text', 'sneeit'),
			'description' => wp_kses(
				sprintf(__('Example: fa-angle-down. <a href="%s" target="_blank">Check Full List of Icon Codes Here</a>', 'sneeit'), esc_url('http://fortawesome.github.io/Font-Awesome/icons/')),
				array(
					'a' => array(
						'href' => array(),
						'target' => array()
					)
				)
			)
		),
		'badge_text' => array(
			'label' => esc_html__('Badge Text', 'sneeit'),			
		),
		'badge_color' => array(
			'label' => esc_html__('Badge Text Color', 'sneeit'),			
			'type' => 'color'
		),
		'badge_bg' => array(
			'label' => esc_html__('Badge Background Color', 'sneeit'),			
			'type' => 'color'
		),
	));
	
	
	add_action( 'wp_enqueue_scripts', 'sneeit_compact_menu_enqueue', 1 );
}

function sneeit_compact_menu_enqueue() {
	$rtl = '';
	if (is_rtl()) {
		$rtl = '-rtl';		
	}	
	wp_enqueue_style( 'sneeit-compact-menu', SNEEIT_PLUGIN_URL_CSS . 'menus-compact'.$rtl.'.css', array(), SNEEIT_PLUGIN_VERSION );
	wp_enqueue_script( 'sneeit-compact-menu', SNEEIT_PLUGIN_URL_JS . 'menus-compact'.$rtl.'.js', array( 'jquery'), SNEEIT_PLUGIN_VERSION, true );
	
	global $Sneeit_Setup_Compact_Menu_Args;	
		
	if ($Sneeit_Setup_Compact_Menu_Args['responsive_width']) {
		wp_enqueue_style( 'sneeit-compact-menu-responsive', SNEEIT_PLUGIN_URL_CSS . 'menus-compact-responsive'.$rtl.'.css', array(), SNEEIT_PLUGIN_VERSION, 'screen and (max-width: '.$Sneeit_Setup_Compact_Menu_Args['responsive_width'].'px)' );
				
		wp_enqueue_script( 'sneeit-compact-menu-responsive', SNEEIT_PLUGIN_URL_JS . 'menus-compact-responsive'.$rtl.'.js', array( 'jquery'), SNEEIT_PLUGIN_VERSION, true );
	}
}

class Sneeit_Compact_Menu_Walker extends Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Starts the element output.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of wp_nav_menu() arguments.
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		
		/* Sneeit Compact Menu Classes Start Here */
		if (get_post_meta($item->ID, 'enable_mega', true) && $depth == 0) {
			array_push($classes, 'menu-item-mega');
			if ($item->object == 'category') {				
				array_push($classes, 'menu-item-mega-category');
			} else {
				array_push($classes, 'menu-item-mega-link');
			}
		}
		$show_hide_for_users  = get_post_meta($item->ID, 'show_hide_for_users', true);
		if ('logged-in' == $show_hide_for_users) {
			array_push($classes, 'menu-item-show-when-logged-in');
		}
		if ('logged-out' == $show_hide_for_users) {
			array_push($classes, 'menu-item-show-when-logged-out');
		}
		/* Sneeit Compact Menu Classes End Here */


		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $args  An array of arguments.
		 * @param object $item  Menu item data object.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		/**
		 * Filters the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		
		/* Sneeit Compact Menu Attribute Start Here */
		if (get_post_meta($item->ID, 'enable_mega', true) && $item->object == 'category' && $depth == 0) {
			$atts['data-id'] = esc_attr($item->object_id);
		}
		
		if (get_post_meta($item->ID, 'color', true)) {
			if (!isset($atts['style'])) {
				$atts['style'] = '';
			}
			$atts['style'] .= esc_attr('color:'.get_post_meta($item->ID, 'color', true).';');
		}
		if (get_post_meta($item->ID, 'bg', true)) {
			if (!isset($atts['style'])) {
				$atts['style'] = '';
			}
			$atts['style'] .= esc_attr('background:'.get_post_meta($item->ID, 'bg', true).';');
		}
		/* Sneeit Compact Menu Attribute End Here */
		
		/* Sneeit Compact Menu Extra Texts Start Here */
		$sneeit_compact_link_before = '';
		$sneeit_compact_link_after = '';
		if (get_post_meta($item->ID, 'icon_before', true)) {
			$sneeit_compact_link_before .= '<span class="icon-before">'.sneeit_filter_get_font_awesome_tag(get_post_meta($item->ID, 'icon_before', true)) . '</span> ';
		}
		
		if (get_post_meta($item->ID, 'badge_text', true)) {
			$sneeit_compact_link_after .= ' <span class="badge"';
			$badge_style = '';
			if (get_post_meta($item->ID, 'badge_color', true)) {
				$badge_style .= 'color:'.get_post_meta($item->ID, 'badge_color', true).';';
			}
			if (get_post_meta($item->ID, 'badge_bg', true)) {
				$badge_style .= 'background:'.get_post_meta($item->ID, 'badge_bg', true).';';
			}
			if ($badge_style) {
				$sneeit_compact_link_after .= ' style="'.$badge_style.'"';
			}
			$sneeit_compact_link_after .= '>' .get_post_meta($item->ID, 'badge_text', true). '</span>';
		}

		if (get_post_meta($item->ID, 'icon_after', true)) {
			$sneeit_compact_link_after .= ' <span class="icon-after">'.sneeit_filter_get_font_awesome_tag(get_post_meta($item->ID, 'icon_after', true)) . '</span>';
		}
				
		
		/* Sneeit Compact Menu Extra Texts End Here */
		

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of wp_nav_menu() arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of wp_nav_menu() arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $sneeit_compact_link_before;
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= $sneeit_compact_link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$item_output .= '<div class="menu-item-inner">';
				
		
		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if (get_post_meta($item->ID, 'enable_mega', true) && $item->object == 'category' && $depth == 0) {
			$output .= '<div class="menu-mega-content"><div class="menu-mega-content-inner"><span class="mega-mega-content-loading"><i class="fa fa-spin fa-spinner"></i></span></div></div>';// .menu-mega-content
		}
		$output .= '<div class="clear"></div></div></li>'; // .menu-item-inner
	}
}

add_action('sneeit_display_compact_menu', 'sneeit_display_compact_menu_action');
function sneeit_display_compact_menu_action($args) {
	$args = wp_parse_args($args, array(
		'theme_location' => 'main-menu',
		'mega_item_display_callback' => '',
		'mega_item_number_per_page' => 4,
		'sticky_menu' => 'disable',
		'sticky_menu_logo' => '',
		'sticky_menu_logo_retina' => '',
		'container_class' => '',
		'container_id' => '',
		'sticky_menu_holder' => '',
		'sticky_menu_scroller' => '',
	));
	
	// validate args
	////////////////
	if (!$args['theme_location']) {
		return;
	}	
	if (!$args['container_id']) {
		$args['container_id'] = 'sneeit-compact-menu-'.$args['theme_location'];
	}
	if (!$args['sticky_menu_holder']) {
		$args['sticky_menu_holder'] = '#'.$args['container_id'];
	}
	if (!$args['sticky_menu_scroller']) {
		$args['sticky_menu_scroller'] = '#'.$args['container_id'];
	}
	
	// output menu
	
	echo '<nav id="'.$args['container_id'].'"';
	
	if ($args['sticky_menu'] && $args['sticky_menu'] != 'disable') {
		echo ' data-sticky_menu="'.$args['sticky_menu'].'" data-sticky_menu_holder="'.$args['sticky_menu_holder'].'" data-sticky_menu_scroller="'.$args['sticky_menu_scroller'].'"';
	}
	
	echo ' class="sneeit-compact-menu';
	if ($args['container_class']) {
		echo ' '.$args['container_class'];
	}
	echo '" data-mega_item_display_callback="'.esc_attr($args['mega_item_display_callback']).'" data-mega_item_number_per_page="'.esc_attr($args['mega_item_number_per_page']).'">';
	
	if ($args['sticky_menu'] != 'disable' && $args['sticky_menu_logo']) :
		?><a href="<?php echo esc_url(home_url());?>" class="sneeit-compact-menu-sticky-logo <?php echo $args['theme_location'] . '-sticky-menu-logo' ; ?>">
			<img alt="<?php echo esc_attr(bloginfo('name')); ?>" src="<?php echo esc_attr($args['sticky_menu_logo']); ?>" data-retina="<?php echo esc_attr($args['sticky_menu_logo_retina']); ?>"/>
		</a><?php
	endif;
	
	wp_nav_menu(array(
		'theme_location' => $args['theme_location'],
		'container' => '',
		'container_class' => 'sneeit-compact-menu',
		'container_id' => 'sneeit-compact-menu-'.$args['theme_location'],
		'walker' => new Sneeit_Compact_Menu_Walker()
	));
	echo '</nav>';
}

function sneeit_compact_mega_menu_content_callback() {	
	$cate_id = sneeit_get_server_request('id');
	$mega_item_display_callback = sneeit_get_server_request('mega_item_display_callback');
	$mega_item_number_per_page = sneeit_get_server_request('mega_item_number_per_page');
		
	if ($cate_id && is_numeric($cate_id)) {
		$entries = new WP_Query( array(
			'post_status' => 'publish',
			'cat' => (int) $cate_id,
			'post_type' => 'post',
			'posts_per_page'=> $mega_item_number_per_page,
			'ignore_sticky_posts' => 1
		));
		
		$html = '';
		if ($entries->have_posts()) :
				$index = 0;
				while ( $entries->have_posts() ) : $entries->the_post();				
					if (function_exists($mega_item_display_callback)) {				
						$html .= call_user_func($mega_item_display_callback, $index);
					}
					$index++;
				endwhile;
				wp_reset_postdata();
		endif;
		echo $html;
	}
	
	die();
}
if (is_admin()) :
	add_action( 'wp_ajax_nopriv_sneeit_compact_mega_menu_content', 'sneeit_compact_mega_menu_content_callback' );
	add_action( 'wp_ajax_sneeit_compact_mega_menu_content', 'sneeit_compact_mega_menu_content_callback' );
endif;// is_admin for ajax
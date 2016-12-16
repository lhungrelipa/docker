<?php

class Sneeit_Breadcrumbs {
	var $crumbs = array();
	var $depth =  3; // waiting next updates
	var $before_item =  '';
	var $after_item =  '';
	var $item_class =  array(); 
	var $before_text =  ''; 
	var $after_text =  '';
	var $text_class =  array();
	var $separator =  '&gt;';
	var $home_text =  '';
	var $show_current =  TRUE;
	var $before_current = '';
	var $after_current = '';
	var $before_current_text = '';
	var $after_current_text = '';
	var $current_class = '';
	var $custom_taxonomy = '';
	
	public function __construct($args = array()) {
		// premade data
		$this->home_text = __('Home', 'sneeit');
		
		// extract data
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}
		
		// validate data
		$this->item_class = $this->validate_class($this->item_class, 'breadcrumb-item');
		$this->text_class = $this->validate_class($this->text_class, 'breadcrumb-item-text');
		$this->current_class = $this->validate_class($this->current_class, 'breadcrumb-current');
		
		// colect crumbs
		if ($this->home_text) {
			$this->add_crumb(get_home_url(), $this->home_text);
		}
		
		if ( !is_front_page() ) {
			
			if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
				if ($this->show_current) {
					$this->add_crumb('', post_type_archive_title('', false));					
				}
			} else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
              
				// If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if($post_type != 'post') {

					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);
					$this->add_crumb($post_type_archive, $post_type_object->labels->name);
				}
				if ($this->show_current) {
					$this->add_crumb('', get_queried_object()->name);
				}
			} else if ( is_single() ) {
				// Check if post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link to the type
				if($post_type != 'post') {
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);
					$this->add_crumb($post_type_archive, $post_type_object->labels->name);
				}
				
				$categories = get_the_category();
				if ($categories && !empty($categories)) {
					$parent_categories = $categories;
					$min_url = '';
					$min_name = '';
					$min_count = 0;
					$parent_name = '';
					$parent_url = '';
					$child_name = '';
					$child_url = '';
					
					foreach ($categories as $cate) {
						// use this to find the category has minimum number of posts 
						if ($cate->count < $min_count || $min_count == 0) {
							$min_count = $cate->count;
							$min_url = get_category_link($cate->cat_ID);
							$min_name = $cate->name;
						}
						
						// if this category has parent
						if ($cate->parent) {
							// scane in post category list to find the parent (if have)
							foreach($parent_categories as $parent_cate) {
								// found the parent in the list
								if ($parent_cate->cat_ID == $cate->parent) {
									// save data
									$parent_name = $parent_cate->name;
									$parent_url = get_category_link($parent_cate->cat_ID);
									$child_name = $cate->name;
									$child_url = get_category_link($cate->cat_ID);
									
									// if this parent category still has parent,
									// then keep the loop to scan next until 
									// we found the cate without parent
									// if have no parent, break the loop and show out
									if (!$parent_cate->parent) {										
										break;
									}
								}
							}
						}
					}
					// if can not find any structured category
					if (!$parent_name && !$child_name) {
						$parent_name = $min_name;
						$parent_url = $min_url;
					}
					
					if ($parent_name) {
						$this->add_crumb($parent_url, $parent_name);
					}
					if ($child_name) {
						$this->add_crumb($child_url, $child_name);
					}
				} else if (!empty($this->custom_taxonomy) && $taxonomy_exists) {
					$taxonomy_terms = get_the_terms( $post->ID, $this->custom_taxonomy );
					$cat_link       = get_term_link($taxonomy_terms[0]->term_id, $this->custom_taxonomy);
					$cat_name       = $taxonomy_terms[0]->name;
					$this->add_crumb($cat_name, $cat_link);
				}

				// If it's a custom post type within a custom taxonomy
				$taxonomy_exists = taxonomy_exists($this->custom_taxonomy);
				$cat_id = '';
				$cat_link = '';
				$cat_name = '';
				
				if ($this->show_current) {
					$this->add_crumb('', get_the_title());
				}
			} else if ( is_category() ) {
				if ($this->show_current) {
					$this->add_crumb('', single_cat_title('', false));
				}				
			} else if ( is_page() ) {
				global $post;
				// Standard page
				if( $post->post_parent ){

					// If child page, get parents 
					$anc = get_post_ancestors( $post->ID );

					// Get parents in the right order
					$anc = array_reverse($anc);

					// Parent page loop
					foreach ( $anc as $ancestor ) {
						$this->add_crumb(get_permalink($ancestor), get_the_title($ancestor));						
					}

					// Display parent pages
					echo $parents;

					// Current page
					if ($this->show_current) {
						$this->add_crumb('', get_the_title());
					}
				} else {
					// Current page
					if ($this->show_current) {
						$this->add_crumb('', get_the_title());
					}					
				}

			} else if ( is_tag() ) {

				// Tag page

				// Get tag information
				$term_id        = get_query_var('tag_id');
				$taxonomy       = 'post_tag';
				$args           = 'include=' . $term_id;
				$terms          = get_terms( $taxonomy, $args );
				$get_term_id    = $terms[0]->term_id;
				$get_term_slug  = $terms[0]->slug;
				$get_term_name  = $terms[0]->name;

				// Display the tag name
				if ($this->show_current) {
					$this->add_crumb('', $get_term_name);
				}				
			} elseif ( is_day() ) {
				// Day archive

				// Year link
				$this->add_crumb(get_year_link( get_the_time('Y') ), get_the_time('Y'));
				
				// Month link
				$this->add_crumb(get_month_link( get_the_time('Y'), get_the_time('m') ), get_the_time('M'));
			
				// Day display
				if ($this->show_current) {
					$this->add_crumb('', get_the_time('jS') . ' ' . get_the_time('M'));
				}				

			} else if ( is_month() ) {

				// Month Archive

				// Year link
				$this->add_crumb(get_year_link( get_the_time('Y') ), get_the_time('Y'));				

				// Month display
				if ($this->show_current) {
					$this->add_crumb('', get_the_time('M'));
				}				
			} else if ( is_year() ) {

				// Display year archive
				if ($this->show_current) {
					$this->add_crumb('', get_the_time('Y'));
				}				
			} else if ( is_author() ) {
				// Display author name
				if ($this->show_current) {
					// Auhor archive

					// Get the author information
					global $author;
					$userdata = get_userdata( $author );
					
					$this->add_crumb('', $userdata->display_name);
				}				
			} else if ( get_query_var('paged') ) {
				// Paginated archives
				if ($this->show_current) {
					$this->add_crumb('', get_query_var('paged'));
				}
			} else if ( is_search() ) {
				if ($this->show_current) {
					$this->add_crumb('', get_search_query());
				}				
			} elseif ( is_404() ) {
				
			}
		}
	}
	public function add_crumb($crumb_href = '', $crumb_text = '') {
		$this->crumbs[] = array('href' => $crumb_href, 'text' => $crumb_text);
	}
	public function validate_class($class, $default = array()) {
		if (!empty($class)) {
			if (is_string($class)) {
				$class = explode(' ', trim($class));
			}
		} else {
			$class = array();
		}
		if (!is_array($class)) {
			$class = (array) $class;
		}
		
		if (!empty($default)) {
			if (is_string($default)) {
				$default = explode(' ', trim($default));
			}
		} else {
			$default = array();
		}
		if (!is_array($default)) {
			$default = (array) $default;
		}
		
		return array_merge($class, $default);
	}
	public function crumb_class($class) {
		$class = $this->validate_class($class);
		return ' class="'.  implode($class).'"';
	}
	public function crumb_item_class() {
		return $this->crumb_class($this->item_class);
	}
	public function crumb_text_class() {
		return $this->crumb_class($this->text_class);
	}
	public function crumb_current_class() {
		return $this->crumb_class($this->current_class);
	}
}

// https://www.thewebtaylor.com/articles/wordpress-creating-breadcrumbs-without-a-plugin
add_action('sneeit_breadcrumbs', 'sneeit_utilities_breadcrumbs', 1, 1);
function sneeit_utilities_breadcrumbs($args = array()) {
	
		$bc = new Sneeit_Breadcrumbs($args);
		$html = '';	
		
		foreach ($bc->crumbs as $index => $crumb) {
			if ($crumb['href']) {
				$html .= $bc->before_item.'<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">'.
					'<a href="'.$crumb['href'].'" itemprop="url"'.$bc->crumb_item_class().'>'.
						$bc->before_text.'<span itemprop="title"'.$bc->crumb_text_class().'>'.$crumb['text'].'</span>'.$bc->after_text.
					'</a>'.
				'</span>'.$bc->after_item;
			} else {
				$html .= $bc->before_current.'<span'.$bc->before_current_text.$bc->crumb_current_class().'>'.$crumb['text'].$bc->after_current_text.'</span>'.$bc->after_current;
			}
			if ($index < count($bc->crumbs) - 1) {
				$html .= $bc->separator;
			}
		}
		
		// ouput the breadcrumbs
		echo $html;	
}



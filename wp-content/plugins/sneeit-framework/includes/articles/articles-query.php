<?php

add_filter('sneeit_articles_query', 'sneeit_articles_query');

function sneeit_articles_query($args = array()) {	
	global $Sneeit_Articles_Loaded_Posts;
	
	// VALIDATE INFORMATION
	$args = wp_parse_args($args, array(		
		'localize_script_handle' => '',
		'localize_script_object_name' => '',
		'localize_script_data' => '',
		
		'count' => 5,		
		'view_meta_key' => SNEEIT_ARTICLE_META_KEY_VIEWS,
		'post_review_average_meta_key' => SNEEIT_ARTICLE_META_KEY_POST_REVIEW_AVERAGE,	
		'article_process_args_callback' => '',
		'article_display_callback' => '',
		
	));
	
	if (!is_numeric($args['count'])) {
		$args['count'] = 5;
	}


	// PREPARING QUERY ARGUMENTS TO LOAD	
	$query_args = array();
	
	// - basic things
	$query_args['post_type'] = 'post';
	$query_args['post_status'] = 'publish';
	$query_args['posts_per_page'] = (int) $args['count'];

	if (isset($args['paged'])) {
		$query_args['paged'] = (int) $args['paged'];
	}

	if ($args['ignore_sticky_posts']) {
		$query_args['ignore_sticky_posts'] = true;
	}

	if ($args['exclude_loaded_posts']) {
		if (isset($args['post__not_in'])) {
			$query_args['post__not_in'] = $args['post__not_in'];
		} else if (count($Sneeit_Articles_Loaded_Posts)) {
			$query_args['post__not_in'] = $Sneeit_Articles_Loaded_Posts;
		}
	}
	
	// - order for loading posts
	if ('popular' == $args['orderby']) {
		$query_args['meta_key'] = $args['view_meta_key'];
		$query_args['orderby'] = 'meta_value_num';
	} 
	elseif ('comment' == $args['orderby']) {
		$query_args['orderby'] = 'comment_count';
	} 
	elseif ('random' == $args['orderby']) {
		$query_args['orderby'] = 'rand';
	} 
	elseif ('latest-review' == $args['orderby']) {
		$query_args['meta_key'] = $args['post_review_average_meta_key'];
	} 
	elseif ('random-review' == $args['orderby']) {
		$query_args['meta_key'] = $args['post_review_average_meta_key'];
		$query_args['orderby'] = 'rand';
	} 
	elseif ('popular-review' == $args['orderby']) {
		$query_args['meta_key'] = $args['post_review_average_meta_key'];
		$query_args['orderby'] = 'meta_value_num';
	}
	
	
	// - categories	
	if (!empty($args['categories'])) {
		$query_args['cat'] = $args['categories'];		
		$args['categories'] = explode(',', $args['categories']);		
	}
	if (!empty($args['exclude_categories'])) {
		$args['exclude_categories'] = explode(',', $args['exclude_categories']);		
	}

	// tags
	if (!empty($args['tags'])) {					
		$query_args['tag__in'] = explode(',', $args['tags']);
	}
	if (!empty($args['exclude_tags'])) {			
		$query_args['tag__not_in'] = explode(',', $args['exclude_tags']);
	}

	// authors
	if ($args['authors']) {
		$query_args['author'] = $args['authors'];
	}
	if ($args['exclude_authors']) {		
		$query_args['author__not_in'] = explode(',', $args['exclude_authors']);
	}

	// duration to load posts from
	if (!empty($args['duration']) && 		
		in_array($args['duration'], array(
			'1 year ago',
			'1 month ago',
			'1 week ago'
		))
	) {
		$query_args['date_query'] = array(
			array(
				'column' => 'post_date_gmt',
				'after' => $args['duration'],
			)
		);	
	}

	// loading entries	
	$entries = new WP_Query( $query_args );
	
	// provide javascript data in case application need ajax pagination
	if (!empty($args['pagination']) && 
		!empty($args['localize_script_handle']) && 
		!empty($args['localize_script_object_name'])) {
		
		$localize_script_object = array(
			'query_args' => $query_args,
			'max_num_pages' => $entries->max_num_pages,
			'found_posts' => $entries->found_posts,
			'localize_script_data' => $args['localize_script_data']
		);
		
		wp_localize_script( $args['localize_script_handle'], $args['localize_script_object_name'], $localize_script_object);
	}

	// loop and fill content
	$index = 0;
	$html = '';
	if ($entries->have_posts()) :
		while ( $entries->have_posts() ) : $entries->the_post();			
			// update loaded post list
			global $Sneeit_Articles_Loaded_Posts;
			if ($Sneeit_Articles_Loaded_Posts == null) {
				$Sneeit_Articles_Loaded_Posts = array();
			}
			if (is_array($Sneeit_Articles_Loaded_Posts)) {
				array_push($Sneeit_Articles_Loaded_Posts, get_the_ID());
			}
			
			// process and display article
			$processed_args = $args;
			if (function_exists($args['article_process_args_callback'])) {
				$processed_args = call_user_func($args['article_process_args_callback'], $processed_args);				
			}
			if (function_exists($args['article_display_callback'])) {
				$entry = new Sneeit_Article($args);
				$html .= call_user_func($args['article_display_callback'], $entry, $index, $processed_args);
			}
			
			// update index
			$index++;			
		endwhile;	
	endif;
	wp_reset_postdata();
	return $html;
}

<?php
// use inside loop please
function magone_related_post($count = 3, $post_id = null, $style = 'related' /*or break*/) {
	$duration = 'year';

	// save post for backup later
	if ($post_id == null) {
		global $post;
		$post_id = $post->ID;
		if ($post_id == null) {
			return;
		}
	}
	

	/*QUERY*/
	// common query
	$args = array (
		'ignore_sticky_posts' => true,
		'posts_per_page' => $count,
		'post_status' => 'publish',
		'order' => 'DESC',
		'orderby' => 'rand',
		'post__not_in' => array($post_id),
	);

	// time query
	if ($duration == 'year') {//1 year ago
		$args = wp_parse_args($args, array(
			'date_query' => array(
				array(
					'column' => 'post_date_gmt',
					'after' => '1 year ago',
				)
			),
		));
	} else if ($duration == 'month') {//1 month ago
		$args = wp_parse_args($args, array(
			'date_query' => array(
				array(
					'column' => 'post_date_gmt',
					'after' => '1 month ago',
				)
			),
		));
	} else if ($duration == 'week') {//1 week ago
		$args = wp_parse_args($args, array(
			'date_query' => array(
				array(
					'column' => 'post_date_gmt',
					'after' => '1 week ago',
				)
			),
		));
	}

	$args_tags = $args;
	$args_cate = $args;
	$tags = wp_get_post_tags($post_id);  
	$cat_ids = wp_get_post_categories($post_id);
	if ($tags) {  /*Get post from tags if post had at least 1 tag*/
		$tag_ids = array();  
		foreach($tags as $tag) {
			$tag_ids[] = $tag->term_id;  
		}
		$args_tags = wp_parse_args($args_tags, array('tag__in' => $tag_ids));
	} else if ($cat_ids) { /*in case empty tags, get from cate*/
		$args_cate = wp_parse_args($args_cate, array('category__in' => $cat_ids));
	}
	
	$my_query = new WP_Query( $args_tags );
	if (empty($tags) || ($my_query->have_posts() == false) || (count($my_query->posts) < $count)) {
		wp_reset_postdata();
		$my_query = new WP_Query( $args_cate );
	}
	if ($my_query->have_posts() == false || (count($my_query->posts) < $count)) { // just random
		wp_reset_postdata();
		$my_query = new WP_Query( $args );
	}

	
	// Show HTML
	if ($my_query->have_posts()) : 
		$post_id = get_the_ID();
		if ($style == 'break') {
			echo '<div class="post-break-links">';
		} else {
			
		}
		/*
		 * '<div class="item-content gradident">'
						.				$e->cates()
						.				$e->title()
						.			'</div>'
		 */
		$counter = 0;
		while ( $my_query->have_posts() ) : $my_query->the_post();			
			if ($style == 'break') {
				echo '<div class="post-break-link"><i class="fa fa-angle-right"></i> <a title="'.esc_attr(get_the_title()).'" href="'.get_permalink().'">'.get_the_title().'</a></div>';
			} else {
				echo '<div class="post-related-item post-related-item-'.$counter.' '.($counter % 2 == 0 ? 'item-two':'').'">
						<a href="'.get_permalink().'" title="'.esc_html__('Click to read', 'magone').'" class="thumbnail item-thumbnail">
							'.magone_get_post_image($post_id, 'full', array(
								'alt' => esc_attr(get_the_title()), 
								'title' => esc_attr(get_the_title())
							)).'
						</a>
						<h3 class="item-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3></div>';
			}
			$counter++;
		endwhile;
		if ($style == 'break') {
			echo '</div>';
		} else {
			
		}
	else:
		if ($style == 'break') {
			
		} else {
			esc_html_e('Not found any posts', 'magone');
		}
	endif;
	wp_reset_postdata();	
}
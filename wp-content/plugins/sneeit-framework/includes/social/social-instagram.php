<?php

// link format: https://www.instagram.com/username
add_filter('sneeit_number_instagram_followers', 'sneeit_get_social_count_number_instagram_followers', 1, 1);
function sneeit_get_social_count_number_instagram_followers($args) {
	if (is_string($args)) {		
		$args = array(
			'url' => $args
		);
	}
	if (!isset($args['name'])) {
		$args['name'] = 'instagram';
	}
	if (!isset($args['filter'])) {
		$args['filter'] = array(
			'start_1' => '"followed_by": {"count": ',
			'end_2' => '}, "'
		);
	}
	// Behance require empty user agent at beginning to response right HTML
	// if not, it still response but with a wasted HTML	
	return sneeit_get_one_number_from_url($args);
}


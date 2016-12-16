<?php

// link format: https://plus.google.com/u/0/+TienNguyenPlus
add_filter('sneeit_number_google_plus_followers', 'sneeit_get_social_count_number_google_plus_followers', 1, 1);
function sneeit_get_social_count_number_google_plus_followers($args) {
	if (is_string($args)) {
		$args = array(
			'url' => $args
		);
	}
	if (!isset($args['name'])) {
		$args['name'] = 'google';
	}
	if (!isset($args['filter'])) {
		$args['filter'] = array(
			'start_1' => 'id="contentPane',
			'start_2' => 'BOfSxb',
			'end_3' => '</span>'
		);
	}
	return sneeit_get_one_number_from_url($args);
}

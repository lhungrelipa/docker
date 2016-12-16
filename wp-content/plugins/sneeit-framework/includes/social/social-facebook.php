<?php


////
// PRE DEFINES FOR GETTING VALUE OF LIKES
////
// link format: https://www.facebook.com/Sneeit-622691404530609/
add_filter('sneeit_number_facebook_likes', 'sneeit_get_social_count_number_facebook_likes', 1, 1);
function sneeit_get_social_count_number_facebook_likes ($args) {
	if ( is_string( $args ) ) {	
		$args = array(
			'url' => $args
		);
	}
	
	// process URL:
	$args['url-1'] = $args['url'];
	if ( strpos( $args['url'], '/likes') === false ) {
		if (strrpos( $args['url'], '/' ) < strlen( $args['url'] ) - 1 ) {
			$args['url'] .= '/';
		}
		$args['url'] .= 'likes';
	}
	
	// addition data for facebook	
	$args['name'] = 'facebook';
	$args['filter'] = array(
		'start_1' => '["PagesLikesTab","renderLikesData",["',
		'start_2' => '},',
		'start_3' => '],',
		'end_4' => '],[]],["PagesLikesTab"',
	);
	
	$args['filter-1'] = array(
		'start_1' => 'id="PagesLikesCountDOMID"',
		'start_2' => '<span',
		'start_3' => '>',
		'end_4' => '<span',
	);
	
	return sneeit_get_one_number_from_url( $args );
}


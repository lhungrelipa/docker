<?php
define('SNEEIT_YOUTUBE_API_KEY', 'sneeit-youtube-api-key');
define('SNEEIT_MAX_YOUTUBE_VIDEO_ID_LENGTH', 11);

function sneeit_get_youtube_channel_id_in_url( $url ) {
	$channel_id = '';
	$url_elements = explode('/channel/', $url);
	
	// found
	if (count($url_elements) > 1) {
		// clean up
		$channel_id = explode('?', $url_elements[1]);
		$channel_id = $channel_id[0];
		$channel_id = explode('#', $channel_id);
		$channel_id = $channel_id[0];
	}
	
	return $channel_id;
}

// link format: https://www.youtube.com/channel/UCMwiaL6nKXKnSrgwqzlbkaw
add_filter('sneeit_number_youtube_subscribers', 'sneeit_get_social_count_number_youtube_subscribers', 1, 1);
function sneeit_get_social_count_number_youtube_subscribers($args) {
	if (is_string($args)) {		
		$args = array(
			'url' => $args
		);
	}
	if (!isset($args['name'])) {
		$args['name'] = 'youtube';
	}
	if (!isset($args['filter'])) {
		$args['filter'] = array(
			'start_1' => 'subscriber-count',
			'start_2' => '>',
			'end_3' => '<'
		);
	}
	
	if (get_option(SNEEIT_YOUTUBE_API_KEY)) {
		$channel_id = sneeit_get_youtube_channel_id_in_url($args['url']);
		
		if ($channel_id) {
			$count = sneeit_get_data_from_json(array(
				'url' => 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id='.$channel_id.'&key='.get_option(SNEEIT_YOUTUBE_API_KEY),
				'filter' => array('items', 0, 'statistics', 'subscriberCount'),
				'name'  => 'youtube',
			));			
		}
	}
	
	if ($count) {
		if (is_numeric($count)) {
			$count = number_format($count);
		}		
		return $count;
	}
	
	
	
	return sneeit_get_one_number_from_url($args);
}


// parse FIRST youtube video ID in any $content
add_filter('sneeit_get_youtube_id', 'sneeit_get_youtube_id', 1, 1);
function sneeit_get_youtube_id( $content = '' ) {
	$youtube_id = '';

	if ( strlen( $content ) ) {
		// search and get vimeo ID
		$key = '//www.youtube.com/embed/';
		$start = strpos( $content, $key );
		
		if ( false === $start ) {
			$key = 'youtube.com/watch?v=';
			$start = strpos( $content, $key );
		}
		
		if ( false === $start ) {
			$key = 'youtu.be/';
			$start = strpos( $content, $key );
		}
		if ( false === $start ) {
			$key = 'youtube.com/v/';
			$start = strpos( $content, $key );
		}
		
		if ( false === $start ) {
			$key = 'youtube-nocookie.com/embed/';
			$start = strpos( $content, $key );
		}
		
		if ( false !== $start ) {
			for ( $i = $start + strlen($key); $i < strlen($content); $i++ ) {
				if ( ! sneeit_is_slug_name_character( $content[$i] ) ) {
					break;
				}
			}
			if ( $i <= strlen( $content ) ) {
				$youtube_id = substr( $content, $start + strlen( $key ), $i - ( $start + strlen( $key ) ) );
				if ( strlen( $youtube_id ) > SNEEIT_MAX_YOUTUBE_VIDEO_ID_LENGTH ) {
					$youtube_id = '';
				}
			}
		}
	}
	
	return $youtube_id;
}


// $code can be anything: full embed player, url, id, ...
add_filter('sneeit_get_youtube_player', 'sneeit_get_get_youtube_player', 1, 1);
function sneeit_get_get_youtube_player($code, $with = 560, $height = 315) {
	$youtube_id = sneeit_get_youtube_id($code);
	$youtube_player = '';
	
	if ($youtube_id) {
		$youtube_player = '<iframe width="'.$with.'" height="'.$height.'" src="https://www.youtube.com/embed/'.$youtube_id.'?showinfo=0" frameborder="0" allowfullscreen></iframe>';
	}
		
	return $youtube_player;	
}

/*we will save this id in site option with name: sneeit_youtube_api_key*/
add_action( 'sneeit_youtube_api_key_collector', 'sneeit_youtube_api_key_collector_action' );
function sneeit_youtube_api_key_collector_action( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'label' => esc_html__( 'Youtube API Key', 'sneeit' ),
		'description' => wp_kses( __( 'To get Youtube API Key: <ol><li>Access <a href="https://console.developers.google.com/project" target="_blank">https://console.developers.google.com/project</a>, click <b>Create project</b> button, input <b>Project name</b>, click <b>Create</b></li><li>In API Manager overview window, search <b>Youtube</b>, then click and Enable <b>YouTube Data API v3</b> and <b>YouTube Analytics API</b></li><li>Choose <b>Credentials</b> tab, click <b>Create credentials</b>, click <b>API key</b> and choose <b>Browser key</b>, input a name and click <b>Create</b></li></ol>', 'sneeit' ), array(
			'a' => array('href' => array(), 'target' => array(),) , 
			'img' => array('src' => array(), 'alt' => array(),) ,
			'ol' => array() ,
			'li' => array() ,
			'b' => array() ,
		) ),
		'before' => '<tr>',
		'after' => '</tr>',
		'before_label' => '<th scope="row"><label for="sneeit-youtube-api-key">',
		'after_label' => '</label></th>',
		'before_description' => '<p class="description" id="sneeit-youtube-api-key">',
		'after_description' => '</p>',
		'before_input' => '<td>',
		'after_input' => '</td>',
		'nonce' => true, /* false, collector will not check submit value*/
	) );
	
	// process save
	if ( $args['nonce']) {
		if ( ! empty( $_GET[ SNEEIT_YOUTUBE_API_KEY ] ) ) {
			update_option( SNEEIT_YOUTUBE_API_KEY, esc_attr( $_GET[ SNEEIT_YOUTUBE_API_KEY ] ) );
		}
		elseif ( ! empty( $_POST[ SNEEIT_YOUTUBE_API_KEY ] ) ) {
			update_option( SNEEIT_YOUTUBE_API_KEY, esc_attr( $_POST[ SNEEIT_YOUTUBE_API_KEY ] ) );
		}
	}
	
	$sneeit_youtube_api_key = get_option( SNEEIT_YOUTUBE_API_KEY );
	
	echo 
	$args['before'] .
		$args['before_label'] . $args['label'] . $args['after_label'] .
		$args['before_input'] .
			'<input name="' .
					esc_attr( SNEEIT_YOUTUBE_API_KEY ) . 
					'" type="text" id="' . 
					esc_attr( SNEEIT_YOUTUBE_API_KEY ) . 
					'" value="' . 
					esc_attr( $sneeit_youtube_api_key ) .
					'" class="regular-text"/>'.
			$args['before_description'] . $args['description'] . $args['after_description'] .
		$args['after_input'] .
	$args['after'] ;
}

function sneeit_get_youtube_image($content) {
	if (strpos( $content, 'youtube' ) === false && strpos( $content, 'youtu.be' ) === false) {
		return '';
	}
	$youtube_id = sneeit_get_youtube_id($content);
	if (!$youtube_id) {
		return '';
	}

	return 'http://img.youtube.com/vi/'.$youtube_id.'/hqdefault.jpg';
}
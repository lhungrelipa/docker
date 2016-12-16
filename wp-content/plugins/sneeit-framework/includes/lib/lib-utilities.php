<?php

function sneeit_get_server_request($key) {
	$value = '';
	if ($key) {
		if (isset($_GET[$key])) {
			$value = $_GET[$key];
		} else if (isset($_POST[$key])) {
			$value = $_POST[$key];
		}
	}
	return $value;
}

function sneeit_are_you_admin() {
	return current_user_can( 'manage_options' );
}

function sneeit_get_client_ip() {
	$ip_keys = array(
		'HTTP_CLIENT_IP', 
		'HTTP_X_FORWARDED_FOR', 
		'HTTP_X_FORWARDED', 
		'HTTP_FORWARDED_FOR', 
		'HTTP_FORWARDED', 
		'REMOTE_ADDR'
	);	
	foreach ($ip_keys as $key) {
		$ip = getenv($key);
		if( $ip && !strcasecmp( $ip, 'unknown')) {
			return $ip;
		}
		if (isset($_SERVER[$key])) {
			$ip = $_SERVER[$key];
			if( $ip && !strcasecmp( $ip, 'unknown')) {
				return $ip;
			}
		}
	}
	return '';
}

function sneeit_is_gpsi() {
	return (isset($_SERVER['HTTP_USER_AGENT']) && 
			strpos($_SERVER['HTTP_USER_AGENT'], 'Google Page Speed Insights') !== false);
}

function sneeit_var_dump($expression) {
	if (current_user_can( 'manage_options') ) {
		echo '<pre>';
		var_dump($expression);
		echo '</pre><br/><br/>';
	}
}

function sneeit_get_one_number_from_url_with_filter( $url, $filter ) {
	$count = -1;
	$remote_args = array(
		'timeout'    => SNEEIT_REMOTE_TIMEOUT,
		'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) ' . 
		                'AppleWebKit/537.36 (KHTML, like Gecko) ' . 
		                'Chrome/49.0.2623.87 '.
		                'Safari/537.36',
		'sslverify'  => false
	);
	
	
	$response = wp_remote_get( $url, array(	'timeout' => SNEEIT_REMOTE_TIMEOUT	) );
	
	if ( ! is_wp_error( $response ) ) {
		$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );		
	}
	
	if ( -1 == $count ) {
		$response = wp_remote_get( $url, $remote_args );	
		if ( ! is_wp_error( $response ) ) {			
			$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );
		}
	}
	
	if ( -1 == $count ) {
		$remote_args['sslverify'] = true;
		$response = wp_remote_get( $url, $remote_args );		
		if ( ! is_wp_error( $response ) ) {
			$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );
		}
	}
	
	if ( -1 == $count ) {
		$remote_args['sslverify'] = false;
		$remote_args['user-agent'] = '';
		$response = wp_remote_get( $url, $remote_args );		
		if ( ! is_wp_error( $response ) ) {
			$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );
		}
	}
	
	if ( -1 == $count ) {
		$remote_args['sslverify'] = true;
		$remote_args['user-agent'] = '';
		$response = wp_remote_get( $url, $remote_args );		
		if ( ! is_wp_error( $response ) ) {
			$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );
		}
	}
	
	if ( -1 == $count ) {
		$remote_args['sslverify'] = false;
		unset($remote_args['user-agent']);
		$response = wp_remote_get( $url, $remote_args );		
		if ( ! is_wp_error( $response ) ) {
			$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );
		}
	}
	
	if ( -1 == $count ) {
		$remote_args['sslverify'] = true;
		unset($remote_args['user-agent']);
		$response = wp_remote_get( $url, $remote_args );		
		if ( ! is_wp_error( $response ) ) {
			$count = sneeit_get_one_number_in_string_with_filter( $response['body'], $filter );
		}
	}
	
	return $count;
}

/*
 * @param $args array()
 *		name			string		will use to check url
 *		url				string		social url
 *		cache_time		int			only re-fetch after end of cache time (seconds)
 *		remote_timeout	int			number of seconds to wait when fetch a site
 *		filter			array		the filter to cut the response html until find the count
 *							key			key will begin with prefix 'start' or 'end', 'open' or 'close', true or false, '' or ' ', 0 or 1
 *										will cut off the 'head' or the 'tail', example: 'start_1', or 'end_2'
 *										only use _ (underscore) to split key parts
 *										key must be specifi
 *							value		the string will be searched
 *		user-agent		header user-agent request
 * 
 *	@return -1 for error and an int for result
 */
function sneeit_get_one_number_from_url( $args = array() ) {
	
	if (!isset($args['url']) || 
		!isset($args['filter']) || 
		!is_array($args['filter']) ||
		(isset($args['name']) && strpos($args['url'], $args['name']) === false)) {		
		return -1;
	}
		
	$count = false;
	$response_html = false;
	
	// generate key just to use in case using cache
	$count_key = sneeit_url_to_slug($args['url'], true, SNEEIT_PLUGIN_VERSION);
		
	$count = get_transient($count_key);	
	
	// ************* ONLY ENABLE THIS FOR TEST
//	if ( 'youtube' == $args['name'] && current_user_can( 'manage_options')) {
//		$count = false; 
//	}
	/// *******************************
	
	if ( $count == false || $count == '' || ( sneeit_are_you_admin() && get_transient( SNEEIT_ADMIN_CACHE_REFRESH_TIME_KEY ) == false ) ) {
		
		$count = sneeit_get_one_number_from_url_with_filter( $args['url'], $args['filter'] );
		
		if ( -1 == $count && isset( $args['url-1'] ) && isset( $args['filter-1'] ) ) {
			$count = sneeit_get_one_number_from_url_with_filter( $args['url-1'], $args['filter-1'] );
		}
		
		if ( -1 == $count ) {
			$count = get_option( $count_key );
			
			if ( false == $count ) {
				return -1;
			}
		}
		
		set_transient($count_key, $count, SNEEIT_CACHE_TIME);
		update_option($count_key, $count);
		set_transient(SNEEIT_ADMIN_CACHE_REFRESH_TIME_KEY, 'cached', SNEEIT_ADMIN_CACHE_REFRESH_TIME);
	}
	return $count;
}

/*filter is the list of index (key) chain in json, don't include "body"
example: $args['filter'] = array('items', 0, 'statistics', 'subscriberCount')
 *  */
function sneeit_get_data_from_json( $args = array() ) {
	$data = '';
	if (!isset($args['url']) || 
		!isset($args['filter']) || 
		!is_array($args['filter']) ||
		(isset($args['name']) && strpos($args['url'], $args['name']) === false)) {		
		return $data;
	}
			
	$count = false;
	$response_html = false;
	
	$data_key = sneeit_url_to_slug($args['url'], true, SNEEIT_PLUGIN_VERSION);
	
	$data = get_transient($data_key);	
		// ************* ONLY ENABLE THIS FOR TEST
//	if ( 'youtube' == $args['name'] && current_user_can( 'manage_options')) {
//		$data = false; 
//	}
	/// *******************************
		
	if ( $data == false || $data == '' || ( sneeit_are_you_admin() && get_transient( SNEEIT_ADMIN_CACHE_REFRESH_TIME_KEY ) == false ) ) {
		
		$response = wp_remote_get( $args['url'], array(	'timeout' => SNEEIT_REMOTE_TIMEOUT	) );
	
		if ( is_wp_error( $response ) ) {			
			return $data;
		}
		
		$data = json_decode($response['body'], true);		
		
		foreach ($args['filter'] as $json_key) {
			if ( !isset($data[$json_key])) {
				$data = get_option( $data_key );
				if (false == $data || '' == $data) {
					$data = '';
				}
				return $data;
			}
			$data = $data[$json_key];
		}
		
		set_transient($data_key, $data, SNEEIT_CACHE_TIME);
		update_option($data_key, $data);
		set_transient(SNEEIT_ADMIN_CACHE_REFRESH_TIME_KEY, 'cached', SNEEIT_ADMIN_CACHE_REFRESH_TIME);
	}
	return $data;
}

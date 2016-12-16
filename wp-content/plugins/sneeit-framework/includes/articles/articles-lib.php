<?php
/* return
 * - id (int) if found an attachment
 * - src of first image or youtube or video
 */
function sneeit_article_get_image_src($content = '') {	
	// DEFINES
	$src = '';
	
	// START SCANNING IMAGE
	// check if ocntent has image or not
	if (strpos($content, '<img ') !== false) {
		$wp_image_class = (strpos($content, 'wp-image-') === false ? '' : 'wp-image-');	

		// search images in content
		$content = explode('<img ', $content);	
		for ($i = 1; $i < count($content) - 1; $i += 2) {
			$wp_image_id = 0;

			// parse image atributes
			$attrs = explode('/>', $content[$i]);
			$attrs = explode('=', $attrs[0]);

			// santinize image attributes
			for ($j = 0; $j < count($attrs); $j++) {
				$attrs[$j] = trim($attrs[$j], ' "\'\t\n\r\0\x0B');
			}

			// - get image id first, if already in library, return match size src, 
			// - else, return src only		
			for ($j = 0; $j < count($attrs) - 1; $j+=2) {
				if ($attrs[$j] === 'src' && !$src) {
					$src = $attrs[$j+1];
				}
				if ($attrs[$j] === 'class' && strpos($attrs[$j+1], $wp_image_class) !== false) {
					$wp_image_id = explode($attrs[$j+1], $wp_image_class);
					$wp_image_id = sneeit_get_one_number_in_string($wp_image_id[1]);
				}
			}

			// in case have no wp images, but found first image, just return
			if (!$wp_image_class && $src) {
				return $src;
			} 
			
			// or return id of found attachment if it's an image
			if ($wp_image_id && is_numeric($wp_image_id) && wp_attachment_is_image((int) $wp_image_id)) {
				return ((int) $wp_image_id);								
			}
		} // end for i
	} 	
	// if have no image, but youtube
	else if ( strpos( $content, 'youtube' ) !== false || strpos( $content, 'youtu.be' ) !== false ) {	
		$src = sneeit_get_youtube_image($content);
	}
	// or vimeo
	else if ( strpos( $content, 'vimeo' ) !== false ) {
		$src = sneeit_get_vimeo_image($content);		
	} // end of check content
	
	return $src;
}


/* we don't care about size,
 * If an image from media lib, we have srcset and js will handle
 * If an image from some where, we output the src directly
 */
function sneeit_article_get_post_image($post_id = 0, $priority_content = '', $post_content = '', $size = 'post-thumbnail', $attr = array()) {
	// DEFINE
	$html = '';
	$src = '';	
	static $cache = array();
	
	// CHECK IN CACHE FIRST
	if (isset($cache[$post_id.$size])) {
		return $cache[$post_id.$size];
	}
	
	// IF HAVE THUMBNAIL
	if (has_post_thumbnail( $post_id ) ) {
		$cache[$post_id.$size] = get_the_post_thumbnail( $post_id, $size, $attr );
		return $cache[$post_id.$size];
	}
		
	// CHECK IF HAVE FEATURE MEDIA FIELD	
	if ($priority_content) {
		$src = sneeit_get_youtube_image($priority_content);
		if (!$src) {
			$src = sneeit_get_vimeo_image($priority_content);
		}		
	}
	
	if (!$src) {
		// NOW, WE MUST SCAN THE FIRST IMAGE
		if (!isset($ret[$post_id])) {
			return $ret[$post_id];
		}	
		$src = sneeit_article_get_image_src(get_the_content());
		
		// found an attachment id
		if (is_numeric($src)) {
			$cache[$post_id] = wp_get_attachment_image($src, $size, false, $attr);
			return $cache[$post_id];
		}
	}
	
	if ( $src ) {
		// maybe external image or not in library
		$html = '<img src="' . esc_url( $src ) . '"';
		foreach ( $attr as $key => $value ) {
			$html .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		}
		$html .= '/>';
		
		$cache[$post_id.$size] = $html;
	}
	
	return $html;
}

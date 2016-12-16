<?php
global $Sneeit_Rating_Declaration;
add_action($Sneeit_Rating_Declaration['display']['hook'], 'sneeit_review_system_display');

function sneeit_review_system_display($content = '') {
	global $Sneeit_Rating_Declaration;
	if (!is_singular($Sneeit_Rating_Declaration['post_type'])) {
		return $content;
	}
	$post_id = get_the_ID();
	if (!$post_id) {
		return $content;
	}
	
	
	$id = $Sneeit_Rating_Declaration['id'];
	
	$post_review = get_post_meta($post_id, $id, true);	
	
	if (!is_array($post_review) || empty($post_review['type']) || !is_array($post_review[$post_review['type']])) {
		delete_post_meta($post_id, $id.'-average');
		delete_post_meta($post_id, $id.'-type');	
		return $content;
	}
	
	// validate translation text
	$display = $Sneeit_Rating_Declaration['display'];
	
	if (!isset($display['text_no_user_vote'])) {
		$display['text_no_user_vote'] = esc_html__('Have no any user vote', 'sneeit');
	}
	if (!isset($display['text_n_user_votes'])) {
		$display['text_n_user_votes'] = esc_html__('%1$s user %2$s x %3$s', 'sneeit');
	}
	if (!isset($display['text_vote'])) {
		$display['text_vote'] = esc_html__('vote', 'sneeit');
	}
	if (!isset($display['text_votes'])) {
		$display['text_votes'] = esc_html__('votes', 'sneeit');
	}	
	
	/*
	 * ************
	 * PROCESS DATA
	 * ************
	 */
	$post_review_type = $post_review['type'];
	$post_review_items = $post_review[$post_review_type];
	$post_review_author_total_score = 0;
	$post_review_author_total_item = 0;
	$post_review_allow_visitor = (!empty($post_review['support-visitor-review']) && 
									in_array('visitor', $Sneeit_Rating_Declaration['support']));

	// calculate average score of author rating
	foreach ($post_review_items as $item) {		
		if (isset($item['value']) && is_numeric($item['value'])) {
			$post_review_author_total_item++;
			$post_review_author_total_score += (int) $item['value'];
		}
	}
	$post_review_author_average_score = 0;
	if ($post_review_author_total_item) {
		$post_review_author_average_score = $post_review_author_total_score / $post_review_author_total_item;
	}


	// calculate total average score
	
	$post_review_user = get_post_meta($post_id, $id.'-user-'.$post_review_type, true);
	if (!$post_review_user || !$post_review_allow_visitor) {
		// will will calculate user review if not allow or did not have
		$post_review_user = array('count' => 0, 'total' => 0);
	}

	$post_review_total_item = $post_review_user['count'] + 1; // total user and one for author
	$post_review_total_score = $post_review_user['total'] + $post_review_author_average_score;
	$post_review_average_score = $post_review_total_score / $post_review_total_item;
	$post_review_average_scale_score = $post_review_average_score;
	$post_review_user_average_score = 0;
	if ($post_review_user['count']) {
		$post_review_user_average_score = $post_review_user['total'] / $post_review_user['count'];
	}
	
	// change to 0-100 ratio
	$post_review_author_average_scale_score = $post_review_author_average_score;
	if ('star' == $post_review_type ) {
		$post_review_author_average_scale_score = $post_review_author_average_score * 100 / 5;	
		$post_review_average_scale_score = $post_review_average_score * 100 / 5;
	} elseif ('point' == $post_review_type) {
		$post_review_author_average_scale_score = $post_review_author_average_score * 100 / 10;		
		$post_review_average_scale_score = $post_review_average_score * 100 / 10;		
	}

	// update review average field for index query post	
	update_post_meta($post_id, $id.'-average', $post_review_average_scale_score);	
	update_post_meta($post_id, $id.'-type', $post_review_type);

	// output rating box: http://circle.firchow.net/	
	if ($post_review_allow_visitor)  {
		// add one more items for reviews list if have user review enabled
		array_push($post_review_items, array(
			'name' => -1
		));
	}

	/*
	 * ************
	 * PREPARE DATA
	 * ************
	 */
	$review = array(
		'average' => '',		
		'items' => '',		
		'conclusion' => '',
		'summary' => '',
	);
	
	if (!isset($display['star_icon'])) {
		$display['star_icon'] = '&#9733;';
	}
	$display['star_icon'] = '<b>'.$display['star_icon'].'</b>';
	
	/*class remake*/
	// class_star_bar
	if(!isset($display['class_star_bar'])) {
		$display['class_star_bar'] = '';
	}
	$display['class_star_bar'] = 
		'post-review-star-bar' . 
		(('post-review' != $id) ? ' '.$id.'-star-bar' : '') .
		' '.$display['class_star_bar'];
	
	// class_star_bar_top
	if(!isset($display['class_star_bar_top'])) {
		$display['class_star_bar_top'] = '';
	}
	$display['class_star_bar_top'] = 
		'post-review-star-bar-top' . 
		(('post-review' != $id) ? ' '.$id.'-star-bar-top' : '') .
		' '.$display['class_star_bar_top'];
	
	// class_star_bar_bottom
	if(!isset($display['class_star_bar_bottom'])) {
		$display['class_star_bar_bottom'] = '';
	}
	$display['class_star_bar_bottom'] = 
		'post-review-star-bar-bottom' . 
		(('post-review' != $id) ? ' '.$id.'-star-bar-bottom' : '') .
		' '.$display['class_star_bar_bottom'];
	
	
	
	// class_line_bar
	if(!isset($display['class_line_bar'])) {
		$display['class_line_bar'] = '';
	}
	$display['class_line_bar'] = 
		'post-review-line-bar' . 
		(('post-review' != $id) ? ' '.$id.'-line-bar' : '') .
		' '.$display['class_line_bar'];
	
	// class_line_bar_top
	if(!isset($display['class_line_bar_top'])) {
		$display['class_line_bar_top'] = '';
	}
	$display['class_line_bar_top'] = 
		'post-review-line-bar-top' . 
		(('post-review' != $id) ? ' '.$id.'-line-bar-top' : '') .
		' '.$display['class_line_bar_top'];
	
	
	
	// class_average_value
	if(!isset($display['class_average_value'])) {
		$display['class_average_value'] = '';
	}
	$display['class_average_value'] = 
		'post-review-average-value' . 
		(('post-review' != $id) ? ' '.$id.'-average-value' : '') .
		' '.$display['class_average_value'];
	
	// class_average_value_text
	if(!isset($display['class_average_value_text'])) {
		$display['class_average_value_text'] = '';
	}
	$display['class_average_value_text'] = 
		'post-review-average-value-text' . 
		(('post-review' != $id) ? ' '.$id.'-average-value-text' : '') .
		' '.$display['class_average_value_text'];
	
	// class_average_value_canvas
	if(!isset($display['class_average_value_canvas'])) {
		$display['class_average_value_canvas'] = '';
	}
	$display['class_average_value_canvas'] = 
		'post-review-average-value-canvas' . 
		(('post-review' != $id) ? ' '.$id.'-average-value-canvas' : '') .
		' '.$display['class_average_value_canvas'];
	
	// class_average_value_star_bar
	if(!isset($display['class_average_value_star_bar'])) {
		$display['class_average_value_star_bar'] = '';
	}
	$display['class_average_value_star_bar'] = 
		'post-review-average-value-star-bar' . 
		(('post-review' != $id) ? ' '.$id.'-average-value-star-bar' : '') .
		' '.$display['class_average_value_star_bar'].' '.$display['class_star_bar'];
	
	// class_average_value_star_bar_top
	if(!isset($display['class_average_value_star_bar_top'])) {
		$display['class_average_value_star_bar_top'] = '';
	}
	$display['class_average_value_star_bar_top'] = 
		'post-review-average-value-star-bar-top' . 
		(('post-review' != $id) ? ' '.$id.'-average-value-star-bar-top' : '') .
		' '.$display['class_average_value_star_bar_top'].' '.$display['class_star_bar_top'];
	
	// class_average_value_star_bar_bottom
	if(!isset($display['class_average_value_star_bar_bottom'])) {
		$display['class_average_value_star_bar_bottom'] = '';
	}
	$display['class_average_value_star_bar_bottom'] = 
		'post-review-average-value-star-bar-bottom' . 
		(('post-review' != $id) ? ' '.$id.'-average-value-star-bar-bottom' : '') .
		' '.$display['class_average_value_star_bar_bottom'].' '.$display['class_star_bar_bottom'];
	
	//
	
	// class_item
	if(!isset($display['class_item'])) {
		$display['class_item'] = '';
	}
	$display['class_item'] = 
		'post-review-item' . 
		(('post-review' != $id) ? ' '.$id.'-item' : '') .
		' '.$display['class_item'];
	
	// class_item_author
	if(!isset($display['class_item_author'])) {
		$display['class_item_author'] = '';
	}
	$display['class_item_author'] = 
		'post-review-item-author' . 
		(('post-review' != $id) ? ' '.$id.'-item-author' : '') .
		' '.$display['class_item_author'];
	
	// class_item_user
	if(!isset($display['class_item_user'])) {
		$display['class_item_user'] = '';
	}
	$display['class_item_user'] = 
		'post-review-item-user' . 
		(('post-review' != $id) ? ' '.$id.'-item-user' : '') .
		' '.$display['class_item_user'];
	
	// class_item_user_note
	if(!isset($display['class_item_user_note'])) {
		$display['class_item_user_note'] = '';
	}
	$display['class_item_user_note'] = 
		'post-review-item-user-note' . 
		(('post-review' != $id) ? ' '.$id.'-item-user-note' : '') .
		' '.$display['class_item_user_note'];
	
	// class_item_name
	if(!isset($display['class_item_name'])) {
		$display['class_item_name'] = '';
	}
	$display['class_item_name'] = 
		'post-review-item-name' . 
		(('post-review' != $id) ? ' '.$id.'-item-name' : '') .
		' '.$display['class_item_name'];

	/***************************************************************************************/
	// get average value template
	ob_start();
	?><div class="<?php echo esc_attr($display['class_average_value']); ?>"<?php
	?> data-type="<?php echo esc_attr($post_review_type); ?>"<?php
	?> data-value="<?php echo esc_attr((int) $post_review_average_scale_score); ?>"<?php		
	?>><?php

		?><div class="<?php echo esc_attr($display['class_average_value_text']); ?>"><?php

			echo number_format($post_review_average_score, 100 == $post_review_average_score ? 0 : 1);		

			if ('star' != $post_review_type) :
				?><canvas class="<?php echo esc_attr($display['class_average_value_canvas']); ?>"></canvas><?php
			endif; 

		?></div><?php 

		if ('star' == $post_review_type) :
			?><div class="<?php echo esc_attr($display['class_average_value_star_bar']); ?>"><?php
				?><div class="<?php echo esc_attr($display['class_average_value_star_bar_top']); ?>"<?php
				?> data-value="<?php echo esc_attr((int) $post_review_average_scale_score); ?>"<?php
				?>><?php
					?><span><?php
						echo 
						$display['star_icon'].
						$display['star_icon'].
						$display['star_icon'].
						$display['star_icon'].
						$display['star_icon'];
					?></span><?php
				?></div><?php
				?><div class="<?php echo esc_attr($display['class_average_value_star_bar_bottom']); ?>"><?php
					?><span><?php
						echo 
						$display['star_icon'].
						$display['star_icon'].
						$display['star_icon'].
						$display['star_icon'].
						$display['star_icon'];
					?></span><?php
				?></div><?php
			?></div><?php
		endif; // if post review type is star
	?></div><?php
	$review['average'] = ob_get_clean();
	/***************************************************************************************/

	/***************************************************************************************/
	// review items
	ob_start();
	$index = 0;
	foreach ($post_review_items as $item) : 
		// process if user review
		$is_user_review = false;
		if (isset($item['name']) && -1 == $item['name'] && ! isset($item['value']) ) {
			$is_user_review = true;
			if (0 == $post_review_user['count']) {
				$item['name'] = $display['text_no_user_vote'];
			} else {
				$item['name'] = sprintf(
					$display['text_n_user_votes']
					, $post_review_user['count']
					, $post_review_user['count'] > 1 ? $display['text_votes']:$display['text_vote']
					, number_format($post_review_user_average_score,1)
				);
			}
			$item['value'] = $post_review_user_average_score;
		}
		if (! isset($item['value']) || !is_numeric($item['value'])) {
			continue;
		}
		$item_value_scale = $item['value'];
		if ('star' == $post_review_type ) {
			$item_value_scale = $item_value_scale * 100 / 5;						
		} elseif ('point' == $post_review_type) {
			$item_value_scale = $item_value_scale * 100 / 10;
		}

		?><div class="<?php echo esc_attr($display['class_item']); 
		if ($is_user_review) {
			echo ' '.esc_attr($display['class_item_user']);
		} else {
			echo ' '.esc_attr($display['class_item_author']);
		}
		echo ' '.esc_attr('post-review-item-'.$index);
		if ('post-review' != $id) {
			echo ' '.esc_attr($id.'-item-'.$index);
		}
		?>"<?php
		if ($is_user_review) {
			echo ' data-value="'.esc_attr((int) $item_value_scale).'."'.
				 ' data-type="'.esc_attr($post_review_type).'"'.
				 ' data-id="'.esc_attr($post_id).'"';
		}
		?>><?php
			if ('star' == $post_review_type) : 							
				?><div class="<?php echo esc_attr($display['class_star_bar']); ?>"><?php
					?><div class="<?php echo esc_attr($display['class_star_bar_top']); ?>"<?php
					?> data-value="<?php echo esc_attr((int) $item_value_scale); ?>"<?php
					?>><?php
						?><span><?php
							echo 
							$display['star_icon'].
							$display['star_icon'].
							$display['star_icon'].
							$display['star_icon'].
							$display['star_icon'];
						?></span><?php
					?></div><?php
					?><div class="<?php echo esc_attr($display['class_star_bar_bottom']); ?>"><?php
						?><span><?php
							echo 
							$display['star_icon'].
							$display['star_icon'].
							$display['star_icon'].
							$display['star_icon'].
							$display['star_icon'];
						?></span><?php
					?></div><?php
				?></div><?php
			endif; 

			?><div class="<?php echo esc_attr($display['class_item_name']); ?>"><?php 
				echo $item['name'];
				if ('star' != $post_review_type && !$is_user_review) {
					echo ': '.number_format($item['value'], 
						100 == ( (int) $item['value'] ) ? 0 : 1
					);
				}					
			?></div><?php 

			if ('star' != $post_review_type) : 
				?><div class="<?php echo esc_attr($display['class_line_bar']); ?>"><?php
					?><div class="<?php echo esc_attr($display['class_line_bar_top']); ?>"<?php
					?> data-value="<?php echo esc_attr((int) $item_value_scale); ?>"<?php
					?>></div><?php
				?></div><?php
			endif;

			if ($is_user_review) : 
				?><div class="<?php echo esc_attr($display['class_item_user_note']); ?>"></div><?php
			endif; 
		?></div><?php /* post-review-item */

		$index++;
	endforeach; 

	$review['items'] = ob_get_clean();


	/***************************************************************************************/
	// summary & conclusion
	if ($post_review['summary']) :
		$review['summary'] = $post_review['summary'];
	endif;
	if ($post_review['conclusion']) :
		$review['conclusion'] = $post_review['conclusion'];
	endif;

	/***************************************************************************************/
	
	
	if ('the_content' == $display['hook']) {
		ob_start();
	}

	if (function_exists($display['callback'])) {
		call_user_func($display['callback'], $review);
	}
	
	
	?><span itemprop="itemReviewed" itemscope="" itemtype="https://schema.org/Thing"><?php
		?><meta itemprop="name" content="<?php echo esc_attr(get_the_title()); ?>"><?php
	?></span><?php

	?><span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><?php
	
		?><meta itemprop="worstRating" content="0"><?php
		?><meta itemprop="bestRating" content="<?php
			if ('star' == $post_review_type) {
				echo '5';
			} else {
				echo '10';
			}
		?>"><?php
		?><meta itemprop="ratingValue" content="<?php
			echo number_format($post_review_average_score, 1);
		?>"><?php
		
	?></span><?php
	
	if ('the_content' == $display['hook']) {
		$content .= ob_get_clean();
		return $content;
	}
}

add_action( 'wp_enqueue_scripts', 'sneeit_rating_enqueue_scripts_styles' );
function sneeit_rating_enqueue_scripts_styles() {
	global $Sneeit_Rating_Declaration;
	if (!is_singular($Sneeit_Rating_Declaration['post_type'])) {
		return;
	}
	$post_id = get_the_ID();
	if (!$post_id) {
		return;
	}
		
	$id = $Sneeit_Rating_Declaration['id'];
		
	$post_review = get_post_meta($post_id, $id, true);
	if (!is_array($post_review) || empty($post_review['type']) || !is_array($post_review[$post_review['type']])) {
		return;
	}
	
	
	$display = $Sneeit_Rating_Declaration['display'];
	if (!isset($display['text_click_line_rate'])) {
		$display['text_click_line_rate'] = esc_html__('Hover and click above bar to rate', 'sneeit');
	}
	if (!isset($display['text_click_star_rate'])) {
		$display['text_click_star_rate'] = esc_html__('Hover and click above stars to rate', 'sneeit');
	}
	if (!isset($display['text_rated'])) {
		$display['text_rated'] = esc_html__('You rated %s', 'sneeit');
	}
	if (!isset($display['text_will_rate'])) {
		$display['text_will_rate'] = esc_html__('You will rate %s', 'sneeit');
	}
	if (!isset($display['text_submitting'])) {
		$display['text_submitting'] = esc_html__('Submitting ...', 'sneeit');
	}
	if (!isset($display['text_browser_not_support'])) {
		$display['text_browser_not_support'] = esc_html__('Your browser not support user rating', 'sneeit');
	}
	if (!isset($display['text_server_not_response'])) {
		$display['text_server_not_response'] = esc_html__('Server not response your rating', 'sneeit');
	}
	if (!isset($display['text_server_not_accept'])) {
		$display['text_server_not_accept'] = esc_html__('Server not accept your rating', 'sneeit');
	}
	
		
	// enqueue
	wp_enqueue_style( 'sneeit-rating-front', SNEEIT_PLUGIN_URL_CSS . 'rating-front.css', array(), SNEEIT_PLUGIN_VERSION );
	wp_enqueue_script( 'sneeit-rating-front', SNEEIT_PLUGIN_URL_JS .'rating-front.js', array( 'jquery' ), SNEEIT_PLUGIN_VERSION, true );
	wp_localize_script( 'sneeit-rating-front', 'Sneeit_Rating', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'id' => $Sneeit_Rating_Declaration['id'],
		'text' => array(
			'click_line_rate' => $display['text_click_line_rate'],
			'click_star_rate' => $display['text_click_star_rate'],
			'rated' => $display['text_rated'],
			'will_rate' => $display['text_will_rate'],
			'submitting' => $display['text_submitting'],
			'browser_not_support' => $display['text_browser_not_support'],
			'server_not_response' => $display['text_server_not_response'],
			'server_not_accept' => $display['text_server_not_accept'],
		)
	));
	
}
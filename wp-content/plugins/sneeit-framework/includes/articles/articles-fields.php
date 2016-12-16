<?php
add_filter('sneeit_articles_display_fields', 'sneeit_articles_display_fields');
function sneeit_articles_display_fields($args = array()) {
	$args = wp_parse_args($args, array(
		'meta_order_choices' => array(
			'cat' => esc_html__('Categories', 'sneeit'),
			'ico' => esc_html__('Icon', 'sneeit'),
			'review' => esc_html__('Review', 'sneeit'),
			'view' => esc_html__('Views', 'sneeit'),
			'author' => esc_html__('Authors', 'sneeit'),
			'comment' => esc_html__('Comments', 'sneeit'),
			'date' => esc_html__('Date', 'sneeit'),
			'readmore' => esc_html__('Readmore', 'sneeit'),
		)
	));
	return array(
		'meta_order' => array(
			'label' => esc_html__('Meta Elements Order', 'sneeit'), 
			'description' => esc_html__('Pick meta elements to display as order as you want. The display will depend on show / hide option of individual element.', 'sneeit'),
			'type' => 'selects', 
			'choices' => $args['meta_order_choices']
		),
		'thumbnail_height' => array(
			'label' => esc_html__('Thumbnail Height', 'sneeit'), 
			'description' => esc_html__('Thumbnail image height. Set to 0 hide. Set to 1999 for natural height', 'sneeit'),
			'type' => 'range', 
			'max' => 1999, 
			'default' => 150
		),		
		'number_cates' => array(
			'label' => esc_html__('Show Category Names', 'sneeit'), 
			'description' => esc_html__('Number category names will be displayed in each item. Set to 0 to hide', 'sneeit'),
			'type' => 'range', 
			'max' => 4,
			'default' => 1
		),
		'show_format_icon' => array(
			'label' => esc_html__('Show Format Icon', 'sneeit'), 
			'description' => esc_html__('Show post format icon', 'sneeit'),
			'type' => 'checkbox',
			'default' => false
		),
		'show_review_score' => array(
			'label' => esc_html__('Show Review Score', 'sneeit'), 
			'description' => esc_html__('Show review score if have', 'sneeit'),
			'type' => 'checkbox', 
			'default' => false
		),
		'show_view_count' => array(
			'label' => esc_html__('Show View Count', 'sneeit'), 
			'description' => esc_html__('Show number of page views', 'sneeit'),
			'type' => 'checkbox', 
			'default' => false
		),
		'show_author' => array(
			'label' => esc_html__('Show Author', 'sneeit'), 
			'description' => esc_html__('Show / hide author name in item detail', 'sneeit'),
			'type' => 'select', 
			'default' => 'icon',
			'choices' => array(
				'' => esc_html__('Not show', 'sneeit'), 
				'name' => esc_html__('Show name only', 'sneeit'), 
				'icon' => esc_html__('Show icon and name', 'sneeit'), 
				'avatar' => esc_html__('Show avatar and name', 'sneeit')
			),
		),
		'show_date' => array(
			'label' => esc_html__('Show Date', 'sneeit'), 
			'description' => esc_html__('Show / hide date / time in item detail. The format will follow the site date time format', 'sneeit'),
			'type' => 'select', 
			'default' => 'date',
			'choices' => array(
				'' => esc_html__('Not Show', 'sneeit'), 
				'full' => esc_html__('Date and Time', 'sneeit'), 
				'date' => esc_html__('Only Date', 'sneeit'), 
				'time' => esc_html__('Only Time', 'sneeit'), 
				'short' => esc_html__('Short Date Time', 'sneeit'), 
				'pretty' => esc_html__('Pretty Date Time', 'sneeit')
			)
		),
		'show_comment' => array(
			'label' => esc_html__('Show Comment', 'sneeit'), 
			'description' => esc_html__('Show comment number. Uncheck to hide', 'sneeit'),
			'type' => 'checkbox', 
			'default' => true
		),		
		'snippet_length' => array(
			'label' => esc_html__('Snippet Length', 'sneeit'), 
			'description' => esc_html__('Snippet / excerpt length. Set to 0 to hide. Set to 1999 for full post', 'sneeit'),
			'type' => 'range', 
			'max' => 1999,
			'default' => 150
		),
		'show_readmore' => array(
			'label' => esc_html__('Show Read More', 'sneeit'), 
			'description' => esc_html__('Show readmore link. Uncheck to hide', 'sneeit'),
			'type' => 'checkbox', 
			'default' => true
		),
	);
}

add_filter('sneeit_articles_query_fields', 'sneeit_articles_query_fields');
function sneeit_articles_query_fields($args = array()) {
	return array(
		'count' => array(
			'label' => esc_html__('Post Count', 'sneeit'), 
			'description' => esc_html__('Number of posts will be loaded', 'sneeit'),
			'type' => 'number', 
			'default' => 5,
			'heading' => esc_html__('Post Queries', 'sneeit'), 
		),
		'orderby' => array(
			'label' => esc_html__('Post Orderby', 'sneeit'), 		
			'type' => 'select', 
			'choices' => array(
				'latest' => esc_html__('Latest', 'sneeit'), 
				'random' => esc_html__('Random', 'sneeit'),			
				'comment' => esc_html__('Most Commented', 'sneeit'),
				'popular' => esc_html__('Popular (Most Viewed)', 'sneeit'),	
				'latest-review' => esc_html__('Latest Reviews', 'sneeit'),	
				'random-review' => esc_html__('Random Reviews', 'sneeit'),	
				'popular-review' => esc_html__('Popular (Highest) Reviews', 'sneeit'),			
			),
			'default' => 'latest'
		),
		'duration' => array(
			'label' => esc_html__('Date Range', 'sneeit'), 		
			'description' => esc_html__('Date range limit to load post from, base on publish date', 'sneeit'),
			'type' => 'select', 
			'choices' => array(
				'' => esc_html__('All Time', 'sneeit'),
				'1 year ago' => esc_html__('Last 365 days', 'sneeit'), 
				'1 month ago' => esc_html__('Last 30 days', 'sneeit'),			
				'1 week ago' => esc_html__('Last 7 days', 'sneeit'),					
			),
			'default' => ''
		),
		'categories' => array(
			'label' => esc_html__('Load from Categories', 'sneeit'),
			'description' => esc_html__('The categories that will be loaded posts from. Leave blank to load from all (recent possts)', 'sneeit'),
			'type' => 'categories'
		),
		'exclude_categories' => array(
			'label' => esc_html__('Exclude from Categories', 'sneeit'),
			'description' => esc_html__('The categories that will be loaded posts from. Leave blank to load from all (recent possts)', 'sneeit'),
			'type' => 'categories'
		),
		'authors' => array(
			'label' => esc_html__('Load from Authors', 'sneeit'),
			'description' => esc_html__('The authors that will be loaded posts from. Leave blank to load from all', 'sneeit'),
			'type' => 'users'
		),
		'exclude_authors' => array(
			'label' => esc_html__('Exclude from Authors', 'sneeit'),
			'description' => esc_html__('The authors that will not be loaded posts from. Leave blank to load from all', 'sneeit'),
			'type' => 'users'
		),
		'tags' => array(
			'label' => esc_html__('Load from Tags', 'sneeit'),
			'description' => esc_html__('The tags that will be loaded posts from. Leave blank to load from all', 'sneeit'),
			'type' => 'tags'
		),
		'exclude_tags' => array(
			'label' => esc_html__('Exclude from Tags', 'sneeit'),
			'description' => esc_html__('The tags that will be loaded posts from. Leave blank to load from all', 'sneeit'),
			'type' => 'tags'
		),
		'ignore_sticky_posts' => array(
			'label' => esc_html__('Ignore Sticky Posts', 'sneeit'), 
			'description' => esc_html__('Do not move sticky posts to the start of the set', 'sneeit'),
			'type' => 'checkbox', 
			'default' => true
		),
		'exclude_loaded_posts' => array(
			'label' => esc_html__('Exclude Loaded Posts', 'sneeit'), 
			'description' => esc_html__('Do not get the loaded posts from previous blocks', 'sneeit'),
			'type' => 'checkbox', 
			'default' => false
		),
	);
}

add_filter('sneeit_articles_block_header_link', 'sneeit_articles_block_header_link');
function sneeit_articles_block_header_link($args = array()) {
	$args = wp_parse_args($args, array(
		'categories' => '',
		'category_scenario' => 'combination',
		'authors' => '',
		'tags' => '',
	));
	
	// generate link
	$link = '';
	// base on categories
	if (!empty($args['categories'])) {
		if ($link) {
			$link .= '&';
		} else {
			$link .= '?';
		}
		$link .= 'cat='.$args['categories'];		
	}	
	// base on authors
	if (!empty($args['authors'])) {
		if ($link) {
			$link .= '&';
		} else {
			$link .= '?';
		}
		$link .= 'author='.$args['authors'];
	}
	// base on tags
	if (!empty($args['tags'])) {				
		$tag_ids = explode(',', $args['tags']);
		$tag_link = '';
		foreach ($tag_ids as $tag_id) {
			$tag = get_tag($tag_id);
			if (!is_wp_error($tag)) {
				if ($tag_link) {
					$tag_link .= ',';
				}
				$tag_link .= $tag->slug;
			}			
		}
		if ($tag_link) {
			if ($link) {
				$link .= '&';
			} else {
				$link .= '?';
			}
			$link .= 'tags=' .$tag_link;
		}
	}
	
	// in case not found anything
	// just link to recent post
	if ($link) {
		$link = get_home_url() . $link;
	} else {
		$link = get_home_url() . '?s=';
	}
	return $link;
}
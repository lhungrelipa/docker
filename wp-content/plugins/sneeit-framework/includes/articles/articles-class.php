<?php

class Sneeit_Article {
	var $ID = 0;
	var $permalink = '';
	var $title = '';
	var $title_esc_attr = '';
	var $content = '';
	var $args = array();
	
	/* THIS FUNCTION RUN WHEN CREATE CLASS VARIABLE
	 * we will use this to get some basic thing that will be use in every methods
	 */
	function __construct($args = array()) {
		$this->ID = get_the_ID();
		$this->permalink = get_permalink();
		$this->title = get_the_title();
		$this->title_esc_attr = esc_attr($this->title);		
		$this->args = $args;
		
		wp_parse_args($this->args, array(
			// META KEYS
			'feature_media_meta_key' => 'feature-media',
			'view_count_meta_key' => 'views',
			'post_review_average_meta_key' => 'post-review-average',
			
			// THUMBNAIL
			'default_thumbnail_image' => '',
			'thumbnail_height' => 150,
			'before_thumbnail_image' => '',
			'after_thumbnail_image' => '',
			'thumbnail_image_attr' => array(),
			'thubnail_link_attr' => array(),
		));
	}
	
	/* return full content of post
	 * call this when you need only to save performances
	 */
	function content() {
		if ($this->content) {
			return $this->content;
		}
		$this->content = get_the_content();
		return $this->content;
	}
	
	/* Images not from library will have src
	 * Images from library will have no src, but data-s,
	 * and javascript will measure the thumbnail size
	 * and get the right image for src attribute
	 */
	function thumbnail($size = 'post-thumbnail', $args = array()) {
		/*
		if (!$this->args['thumbnail_height']) {
			return '';
		}
		
		// SANITIZE
		$args = wp_parse_args($args, array(
			'before_image' => $this->args['before_thumbnail_image'],
			'after_image' => $this->args['after_thumbnail_image'],
			'image_attr' => $this->args['thumbnail_image_attr'],	
			'link_attr' => $this->args['thumbnail_link_attr'],
		));
		
		// VALIDATE
		$args['image_attr'] = wp_parse_args($args['image_attr'], array(
			'alt' => $this->title_esc_attr
		));		
		$args['link_attr'] = wp_parse_args($args['image_attr'], array(
			'title' => $this->title_esc_attr
		));		
		if (!isset($args['link_attr']['class'])) {
			$args['link_attr']['class'] = '';
		}
		
		
		// FEATURE MEDIA CONTENT
		$feature_media_meta_value = '';
		if ($this->args['feature_media_meta_key']) {
			$feature_media_meta_value = get_post_meta($this->ID, $this->args['feature_media_meta_key'], true);
		}
		
		// GET IMAGE
		$image = sneeit_article_get_post_image($this->ID, $feature_media_meta_value, $this->content(), $size, $args['image_attr']);
		if (!$image && $this->args['default_thumbnail_image']) {
			$image = '<img src="'.esc_url($this->args['default_thumbnail_image']).'"';
			foreach ($args['image_attr'] as $attr_name => $attr_value) {
				$image .= ' '.$attr_name.'="'.esc_attr($attr_value).'"';
			}
			$image .= '/>';
		}		
		if (!$image) {
			return '';
		}
		
		// ADD LINK	
		 * 
		 */
	}
}

<?php
define('SNEEIT_ARTICLE_META_KEY_VIEWS', 'views');
define('SNEEIT_ARTICLE_META_KEY_POST_REVIEW_AVERAGE', 'post-review-average');

global $Sneeit_Articles_Loaded_Posts;
$Sneeit_Articles_Loaded_Posts = array();

include_once 'articles-lib.php';
include_once 'articles-fields.php';
include_once 'articles-class.php';
include_once 'articles-query.php';

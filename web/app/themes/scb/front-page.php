<?php
/**
 * Template Name: Homepage
 */

$num_projects = \Firebelly\PostTypes\Project\get_num_projects();
$load_more_category = '';

$grid_stat = \Firebelly\PostTypes\Stat\get_stat();
$grid_projects = \Firebelly\PostTypes\Project\get_projects();
$grid_news_posts = get_posts(['numberposts' => 3, 'suppress_filters' => false]);
$grid_description = $post->post_content;

include(locate_template('templates/project-grid-top.php'));
?>

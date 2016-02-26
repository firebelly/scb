<?php
/**
 * Project taxonomy page 
 */

$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$num_projects = \Firebelly\Utils\get_num_posts_in_category('project', 'project_category', $term->term_id);
$load_more_category = $term->slug;

$grid_stat = \Firebelly\PostTypes\Stat\get_stat(['related_category' => $term->term_id]);
$grid_projects = \Firebelly\PostTypes\Project\get_projects();
$grid_news_posts = get_posts(['numberposts' => 3, 'suppress_filters' => false, 'category' => $term->term_id]);
$grid_description = $post->post_content;

include(locate_template('templates/project-grid-top.php'));
?>

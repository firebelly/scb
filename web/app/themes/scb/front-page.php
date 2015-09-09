<?php
/**
 * Template Name: Homepage
 */
?>

<?= $post->post_content ?>

<ul>
<?php 
wp_list_categories([ 
  'taxonomy' => 'project_category', 
  'hide_empty' => 0,
  'title_li' => '',
]);
?>
</ul>

<?php 
$projects = \Firebelly\PostTypes\Project\get_projects();
foreach ($projects as $project_post) {
  include(locate_template('templates/article-project.php'));
}
?>

<section class="news">
<?php 
  // Recent Blog & News posts
  $news_posts = get_posts(['numberposts' => 3]);
  if ($news_posts):
    foreach ($news_posts as $news_post) {
      include(locate_template('templates/article-news.php'));
    }
  endif;
?>
</section>

<?php
/**
 * Template Name: Homepage
 */
$collection = \Firebelly\Collections\get_active_collection();
$num_projects = \Firebelly\PostTypes\Project\get_num_projects();
?>

<div class="grid wrap -top">
  <div class="page-intro grid-item one-half -left">
    <?= $post->post_content ?>
  </div>

  <div class="project-categories grid-item one-half -right">
    <ul class="categories-parent">
    <?php 
    wp_list_categories([ 
      'taxonomy' => 'project_category', 
      'hide_empty' => 0,
      'title_li' => '',
    ]);
    ?>
    </ul>
    <button class="plus-button categories-toggle expanded"><div class="plus"></div></button>
  </div>
</div>

<section class="projects">
<?php 
$projects = \Firebelly\PostTypes\Project\get_projects();
foreach ($projects as $project_post) {
  include(locate_template('templates/article-project.php'));
}
?>
</section>

<section class="people">
<?php 
$people = \Firebelly\PostTypes\person\get_people();
foreach ($people as $person_post) {
  include(locate_template('templates/article-person.php'));
}
?>
</section>

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

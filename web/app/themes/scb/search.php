<?php get_template_part('templates/page', 'header'); ?>

<?php get_search_form(); ?>

<div class="search-container">

  <?php 
  $search_arr = [
    'architecture' => 'Architecture',
    'planning' => 'Planning',
    'interior-design' => 'Interior Design',
  ];

  foreach ($search_arr as $cat_slug => $cat_title) {

    $project_posts = \Firebelly\PostTypes\Project\get_projects([
      'category' => $cat_slug,
      'search' => get_search_query(),
    ]);

    if ($project_posts) {
      echo '<div class="search-column"><h2>'.$cat_title.'</h2>';
      foreach ($project_posts as $project_post) {
        include(locate_template('templates/article-project.php'));
      }
      echo '</div>';
    }
  }
  ?>
  <div class="search-column">

    <?php 
    $count = 0;
    if (have_posts()) { echo '<h2>News</h2>';}
    while (have_posts()) : the_post();
      if ($post->post_type=='post') {
        $count++;
        get_template_part('templates/content', 'search');
      }
    endwhile;
    ?>
  </div>

</div><!-- .search-container -->
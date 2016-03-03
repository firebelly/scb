<?php get_search_form(); ?>

<div class="search-container">

  <!-- <?php get_template_part('templates/page', 'header'); ?> -->

  <?php if (!have_posts()) : ?>
    <div class="alert alert-warning">
      <?php _e('Sorry, we couldn\'t find anything that matched your search. Try refining your search terms.', 'sage'); ?>
    </div>
  <?php endif; ?>

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
      echo '<div class="search-column"><h2 class="cat-title">'.$cat_title.'</h2>';
      foreach ($project_posts as $project_post) {
        include(locate_template('templates/article-project-excerpt.php'));
      }
      echo '</div>';
    }
  }
  ?>
  <div class="search-column">

    <?php 
    $count = 0;
    if (have_posts()) { echo '<h2 class="cat-title">News</h2>';}
    while (have_posts()) : the_post();
      if ($post->post_type=='post') {
        $count++;
        echo '
        <article class="article show-post-modal" data-modal-type="news-modal" data-id="'.$post->ID.'">
          <h2 class="entry-title"><a href="'.get_permalink($post).'">'.wp_trim_words($post->post_title, 10).'</a></h2>
        </article>';
      }
    endwhile;
    ?>
  </div>

</div><!-- .search-container -->
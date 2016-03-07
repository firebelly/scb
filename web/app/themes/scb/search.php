<?php
// Build arrays of matching post types from main search query
$news_posts = $other_posts = $project_posts = [];
while (have_posts()) : the_post();
  if ($post->post_type=='post') {
    $news_posts[] = $post;
  } elseif ($post->post_type=='project') {
    $project_posts[] = $post;
  } else {
    // Not currently using this anywhere
    $other_posts[] = $post;
  }
endwhile;
?>

<?php get_search_form(); ?>

<div class="search-container">

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

  // Loop through matching projects and search for category
  foreach ($search_arr as $cat_slug => $cat_title) {
    $matching_posts = [];
    foreach($project_posts as $project_post) {
      if (has_term($cat_slug, 'project_category', $project_post)) {
        $matching_posts[] = $project_post;
      }
    }

    // Did any projects match this category column?
    if (!empty($matching_posts)) {
      echo '<div class="search-column"><h2 class="cat-title">'.$cat_title.'</h2>';
      foreach ($matching_posts as $project_post) {
        include(locate_template('templates/article-project-excerpt.php'));
      }
      echo '</div>';
    }
  }
  ?>
  <div class="search-column">
    <?php 
    // Any news posts match?
    if (!empty($news_posts)) { 
      echo '<h2 class="cat-title">News</h2>';
      foreach ($news_posts as $news_post) {
        echo '<article class="article show-post-modal" data-modal-type="news-modal" data-id="'.$news_post->ID.'">
          <h2 class="entry-title"><a href="'.get_permalink($news_post).'">'.wp_trim_words($news_post->post_title, 10).'</a></h2>
        </article>';
      }
    }
    ?>
  </div>

</div><!-- .search-container -->
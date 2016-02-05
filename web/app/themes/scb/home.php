<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<div class="article-list">
<?php 
  // Recent Blog & News posts
  $i = 0;
  $news_posts = get_posts(['posts_per_page' => 10, 'suppress_filters' => false]);
  if ($news_posts):
    foreach ($news_posts as $news_post) {
      $i++;
      if ($i===7) {
        echo '<article class="resource-list">
          <div class="background-image-wrap">
            <div class="article-inner">
              <h3>White Pages</h3>
              <ul>
                <li><a href="">Article Title</a></li>
                <li><a href="">Article Title</a></li>
                <li><a href="">Article Title</a></li>
              </ul>
            </div>
          </div>
        </article>';
      } elseif ($i===8) {
        echo '<article class="resource-list">
          <div class="background-image-wrap">
            <div class="article-inner">
              <h3>Project Brochures</h3>
              <ul>
                <li><a href="">Article Title</a></li>
                <li><a href="">Article Title</a></li>
                <li><a href="">Article Title</a></li>
              </ul>
            </div>
          </div>
        </article>';
      }
      include(locate_template('templates/article-news.php'));
    }
  endif;
?>
</div>

<?php the_posts_navigation(); ?>

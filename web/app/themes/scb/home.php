<?php
/**
 * News landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_posts = $GLOBALS['wp_query']->found_posts;
$total_pages = ($total_posts > 0) ? ceil($total_posts / $per_page) : 1;
$page = get_page_by_path('/news'); // may use this down the line to pull editable metadata from page for og tags/etc
?>
<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php else: ?>

  <div class="article-list"><div class="first-grid">
  <?php
    $i = 0;
    while (have_posts()) : the_post();
      $news_post = $post;
      $i++;
      include(locate_template('templates/article-news-excerpt.php'));
      if ($total_posts>=6 && $i===6) {
        echo '<article class="resource-list">
          <div class="background-image-wrap">
            <div class="article-inner">
              <h3>White Pages</h3>';

                $page = get_page_by_path('/news/white-pages/');
                $lis = \Firebelly\Utils\get_li_excerpt($page);
                echo '<ul>'.$lis.'</ul>
                <a href="'.get_permalink($page).'">View all</a>

            </div>
          </div>
        </article>';
      } elseif ($total_posts>=6 && $i===7) {
        echo '<article class="resource-list">
          <div class="background-image-wrap">
            <div class="article-inner">
              <h3>Project Brochures</h3>';

                $page = get_page_by_path('/news/project-brochures/');
                $lis = \Firebelly\Utils\get_li_excerpt($page);
                echo '<ul>'.$lis.'</ul>
                <a href="'.get_permalink($page).'">View all</a>

            </div>
          </div>
        </article>';
      }
    endwhile;
  ?>
  </div><!-- END .first-grid -->

  <div class="masonry-grid"></div>
  <?php if ($total_pages>1): ?>
    <div class="load-more" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a href="#"><span>Load More News</span> <span><button class="plus-button"><div class="plus"></div></button></span></a></div>
  <?php endif ?>

  </div><!-- END .article-list -->
<?php endif; ?>

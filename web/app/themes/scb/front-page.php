<?php
/**
 * Template Name: Homepage
 */
$num_projects = \Firebelly\PostTypes\Project\get_num_projects();
?>

<div class="grid wrap -top">
  <div class="page-intro grid-item one-half -left">
    <?= $post->post_content ?>
  </div>

  <div class="project-categories grid-item one-half -right">
    <div class="-inner">
      <ul class="categories-parent">
      <?php 
      wp_list_categories([ 
        'taxonomy' => 'project_category', 
        'hide_empty' => 0,
        'title_li' => '',
      ]);
      ?>
      </ul>
    </div>
  </div>
</div>

<section class="projects main-project-grid">
  <div class="initial-section">
  <?php
    $i = 0;
    $projects = \Firebelly\PostTypes\Project\get_projects();
    foreach ($projects as $project_post) {
      $i++;
      include(locate_template('templates/article-project.php'));
      $news_posts = get_posts(['numberposts' => 3, 'suppress_filters' => false]);
      if (count($projects)>=3 && $i===3) {
        echo '<article class="grid-item stat">
                <div class="wrap">
                  <p class="stat-number">85</p>
                  <p class="stat-label">Year Design Legacy</p>
                  <p class="stat-link"><a href="about">About SCB</a></p>
                </div>
              </article>
        ';

        // Recent Blog & News posts
        if ($news_posts):
          echo '<article class="grid-item news"><ul>';
          foreach($news_posts as $news_post) {
            $category = \Firebelly\Utils\get_category($news_post);
            if (!empty($news_post->post_content)) {
              echo '<li>';
                      if ($category) { echo '<div class="article-category"><a href="'.get_term_link($category).'">'.$category->name.'</a></div>';
                      }
                      echo '<h3><a href="'.get_permalink($news_post).'" data-id="'.$news_post->ID.'" data-modal-type="news-modal" class="show-post-modal">'.$news_post->post_title.'</a></h3>
                            <a href="'.get_permalink($news_post).'" class="show-post-modal read-more-link" data-modal-type="news-modal" data-id="'.$news_post->ID.'"><button class="plus-button"><div class="plus"></div></button> <span class="sr-only">Continued</span></a>
                    </li>';
            }
            else
              echo '<li>'.$news_post->post_title.'</li>';
          }
          echo '</ul></article>';
        endif;
      }

      if (count($projects)>=5 && $i===5) {
        echo '<article class="grid-item stat stat-secondary">
                <div class="wrap">
                  <div class="stat-content">
                    <p class="stat-number">150</p>
                    <p class="stat-label">Active Projects</p>
                    <p class="stat-link"><a href="map">View on map</a></p>
                  </div>
                </div>
              </article>
        ';
      }
    }
  ?>    
  </div>

<div class="load-more-container"></div>

<div class="load-more" data-post-type="project" data-page-at="1" data-past-events="0" data-per-page="9" data-total-pages="<?= ceil(($num_projects - 6)/9) ?>" data-category=""><a href="#"><span>Load More Projects</span> <span><button class="plus-button"><div class="plus"></div></button></span></a></div>
</section>

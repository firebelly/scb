<?php 
/**
 * Crazy ass project grid
 */

$map_id = \Firebelly\Utils\get_id_by_slug('map');
$is_homepage = false;

if (!empty($term)) {
  // Taxonomy filtered
  $num_projects = \Firebelly\Utils\get_num_posts_in_category('project', 'project_category', $term->term_id);
  $num_active_projects = $num_projects;
  $load_more_category = $term->slug;

  $grid_stat = \Firebelly\PostTypes\Stat\get_stat(['related_category' => $term->term_id]);
  $grid_projects = \Firebelly\PostTypes\Project\get_projects(['category' => $term->slug, 'num_posts' => 6]);
  $grid_news_posts = get_posts(['numberposts' => 3, 'suppress_filters' => false, 'project_category' => $term->slug]);
  if (!$grid_news_posts) {
    $grid_news_posts = get_posts(['numberposts' => 3, 'suppress_filters' => false]);
  }
  // Does this term have a description set?
  if (!empty($term->description)) {
    $grid_description = '<h2>'.$term->description.'</h2>';
  } else {
    global $wpdb;
    if ($parent = $wpdb->get_var("SELECT parent FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = ".$term->term_id)) {
      $parent_term = get_term($parent, 'project_category');
      if (!empty($parent_term->description)) {
        // Parent_description?
        $grid_description = '<h2>'.$parent_term->description.'</h2>';
      } else if ($grandparent = $wpdb->get_var("SELECT parent FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = ".$parent)) {
        // grandparent description?
        $grandparent_term = get_term($grandparent, 'project_category');
        if (!empty($grandparent_term->description))
          $grid_description = '<h2>'.$grandparent_term->description.'</h2>';
      }
    }
  }

} else {

  // Homepage, no filter
  $num_projects = \Firebelly\PostTypes\Project\get_num_projects();
  $num_active_projects = \Firebelly\SiteOptions\get_option('num_active_projects');
  $load_more_category = '';
  $is_homepage = true;

  $grid_stat = \Firebelly\PostTypes\Stat\get_stat();
  $grid_projects = \Firebelly\PostTypes\Project\get_projects(['num_posts' => 6]);
  $grid_news_posts = get_posts(['numberposts' => 3, 'suppress_filters' => false]);

}

// Fallback description from homepage if none is set above
if (empty($grid_description)) {
  $homepage = get_post(get_option('page_on_front'));
  $grid_description = $homepage->post_content;
}

?>

<div class="grid wrap -top">
  <div class="page-intro grid-item one-half -left">
    <?= $grid_description ?>
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
  <div class="initial-section masonry-grid">
  <div class="grid-sizer"></div>
  <?php
    $i = 0;
    foreach ($grid_projects as $project_post) {
      $i++;
      include(locate_template('templates/article-project.php'));
      
      if (count($grid_projects)>=3 && $i===3) {
        $stat_length_class = strlen($grid_stat['_cmb2_stat_number'][0]) > 2 ? (strlen($grid_stat['_cmb2_stat_number'][0]) > 4 ? ' long-stat extra-long-stat' : ' long-stat') : '';
        echo '<article class="grid-item stat '.$stat_length_class.'">
                <div class="wrap">
                  <div class="stat-content">
                    <p class="stat-number">' . $grid_stat['_cmb2_stat_number'][0] . '</p>
                    <div class="stat-meta">
                      <p class="stat-label">' . $grid_stat['_cmb2_stat_label'][0] . '</p>
                      ' . (!empty($grid_stat['_cmb2_stat_url'][0]) ? '<p class="stat-link"><a href="' . $grid_stat['_cmb2_stat_url'][0] . '">' . $grid_stat['_cmb2_stat_callout'][0] . '</a></p>' : '') . '
                    </div>
                  </div>
                </div>
              </article>
        ';

        // Recent Blog & News posts
        if ($grid_news_posts):
          echo '<article class="grid-item news"><ul>';
          foreach($grid_news_posts as $news_post) {
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

      // Homepage gets num_active_projects (via site options), filtered views get active projects per category
      if (count($grid_projects)>=5 && $i===5) {
        $stat_length_class = strlen($num_active_projects) > 2 ? (strlen($num_active_projects) > 4 ? ' long-stat extra-long-stat' : ' long-stat') : '';
        echo '<article class="grid-item stat'.$stat_length_class.'">
                <div class="wrap">
                  <div class="stat-content">
                    <p class="stat-number">'.$num_active_projects.'</p>
                    <div class="stat-meta">
                      <p class="stat-label">' . ($is_homepage ? 'Active' : 'Featured') .' Projects</p>
                      ' . ($is_homepage ? '<p class="stat-link"><a href="map" class="show-map" data-id="'.$map_id.'">View on map</a></p>' : '') . '
                    </div>
                  </div>
                </div>
              </article>
        ';
      }
    }
  ?>    
  </div>

<div class="load-more" data-post-type="project" data-page-at="1" data-per-page="9" data-total-pages="<?= ceil(($num_projects - 6)/9)+1 ?>" data-category="<?= $load_more_category ?>"><a href="#"><span>Load More Projects</span> <span><button class="plus-button"><div class="plus"></div></button></span></a></div>
</section>

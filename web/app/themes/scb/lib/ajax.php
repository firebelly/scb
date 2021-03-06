<?php
namespace Firebelly\Ajax;

/**
 * Add wp_ajax_url variable to global js scope
 */
function wp_ajax_url() {
  wp_localize_script('sage_js', 'wp_ajax_url', [ admin_url( 'admin-ajax.php') ]);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\wp_ajax_url', 100);

/**
 * Silly ajax helper, returns true if xmlhttprequest
 */
function is_ajax() {
  return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * AJAX load more posts
 */
function load_more_posts() {
  global $collection;
  if (!isset($collection)) {
    $collection = \Firebelly\Collections\get_active_collection();
  }

  // News or Projects?
  $post_type = (!empty($_REQUEST['post_type']) && $_REQUEST['post_type']=='project') ? 'project' : 'news';

  // Get page offsets
  $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
  $per_page = !empty($_REQUEST['per_page']) ? $_REQUEST['per_page'] : get_option('posts_per_page');
  $offset = ($page-1) * $per_page;
  $args = [
    'offset' => $offset,
    'posts_per_page' => $per_page,
    'no_found_rows' => true,
  ];

  // Show private posts if logged in
  if (is_user_logged_in()) {
    $args['post_status'] = ['publish', 'private'];
  }

  // Set post type
  if ($post_type == 'project') {
    $args['post_type'] = 'project';
  }

  // Filter by Category?
  if (!empty($_REQUEST['project_category'])) {
    if (strpos($_REQUEST['project_category'], ',') !== false) {
      $cats = explode(',', $_REQUEST['project_category']);
      $args['tax_query'] = array();
      foreach($cats as $cat) {
        array_push($args['tax_query'], array(
          'taxonomy' => 'project_category',
          'field'    => 'slug',
          'terms'    => sanitize_title($cat),
        ));
      }
    } else {
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'project_category',
          'field'    => 'slug',
          'terms'    => sanitize_title($_REQUEST['project_category']),
        )
      );
    }
  }

  // $posts = wp_cache_get( 'my_result' );
  // if ( false === $result ) {
    $posts = get_posts($args);

  if ($posts):
    foreach ($posts as $post) {
      // Set local var for post type — avoiding using $post in global namespace
      if ($post_type == 'project') {
        $project_post = $post;
        include(locate_template('templates/article-'.$post_type.'.php'));
      } else {
        $news_post = $post;
        include(locate_template('templates/article-news-excerpt.php'));
      }
    }
  endif;

  if (is_ajax()) die();
}
add_action( 'FB_AJAX_load_more_posts', __NAMESPACE__ . '\\load_more_posts' );
add_action( 'FB_AJAX_nopriv_load_more_posts', __NAMESPACE__ . '\\load_more_posts' );


/**
 * AJAX load more projects in grid
 */
function load_more_projects() {
  global $collection;
  if (!isset($collection)) {
    $collection = \Firebelly\Collections\get_active_collection();
  }

  // Get page offsets
  $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
  $per_page = !empty($_REQUEST['per_page']) ? $_REQUEST['per_page'] : get_option('posts_per_page');
  $offset = ($page-1) * $per_page;

  $args = [
    'offset' => $offset,
    'posts_per_page' => $per_page,
    'no_found_rows' => true,
  ];
  $args['post_type'] = 'project';

  // Filter by Category?
  if (!empty($_REQUEST['project_category'])) {
    $term = get_term_by('slug', $_REQUEST['project_category'], 'project_category');
    $args['tax_query'] = [
      'taxonomy' => 'project_category',
      'field'    => 'slug',
      'terms'    => $term->slug,
    ];
  }

  $grid_projects = get_posts($args);

  if ($grid_projects):
    if ($page==1) {
      include(locate_template('templates/project-grid-top.php'));
    } else {
      foreach ($grid_projects as $project_post) {
        include(locate_template('templates/article-project.php'));
      }
    }
  endif;

  if (is_ajax()) die();
}
add_action( 'FB_AJAX_load_more_projects', __NAMESPACE__ . '\\load_more_projects' );
add_action( 'FB_AJAX_nopriv_load_more_projects', __NAMESPACE__ . '\\load_more_projects' );


/**
 * Load post in modal
 */
function load_post_modal() {

  global $collection,$post;
  if (!isset($collection)) {
    $collection = \Firebelly\Collections\get_active_collection();
  }

  if(!empty($_REQUEST['post_url'])) {
    $post_id = url_to_postid($_REQUEST['post_url']);
    if ($post_id) {
      $post = get_post($post_id);
      $post_type = get_post_type($post);
      $page_name = $post->post_name;

      if ($post_type == 'post') {
        $news_post = $post;
        include(locate_template('templates/article-news.php'));
      } elseif ($post_type == 'page') {
        include(locate_template('page-'.$page_name.'.php'));
      } else {
        include(locate_template('single-'.get_post_type($post).'.php'));
      }
    } else {
      echo 'Post not found.';
    }
  } else {
    echo 'Post not found.';
  }

  if (is_ajax()) die();
}
add_action( 'FB_AJAX_load_post_modal', __NAMESPACE__ . '\\load_post_modal' );
add_action( 'FB_AJAX_nopriv_load_post_modal', __NAMESPACE__ . '\\load_post_modal' );

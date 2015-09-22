<?php
namespace Firebelly\Ajax;

/**
 * Add wp_ajax_url variable to global js scope
 */
function wp_ajax_url() {
  wp_localize_script('sage_js', 'wp_ajax_url', admin_url( 'admin-ajax.php'));
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\wp_ajax_url', 100);

/**
 * Silly ajax helper, returns true if xmlhttprequest
 */
function is_ajax() {
  return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * AJAX load more posts (news or events)
 */
function load_more_posts() {
  // news or projects?
  $post_type = (!empty($_REQUEST['post_type']) && $_REQUEST['post_type']=='project') ? 'project' : 'news';
  // get page offsets
  $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
  $per_page = !empty($_REQUEST['per_page']) ? $_REQUEST['per_page'] : get_option('posts_per_page');
  $offset = ($page-1) * $per_page;
  $args = [
    'offset' => $offset,
    'posts_per_page' => $per_page,
  ];
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

  $posts = get_posts($args);

  if ($posts): 
    foreach ($posts as $post) {
      // set local var for post type — avoiding using $post in global namespace
      if ($post_type == 'project')
        $project_post = $post;
      else
        $news_post = $post;
      include(locate_template('templates/article-'.$post_type.'.php'));
    }
  endif;

  // we use this call outside AJAX calls; WP likes die() after an AJAX call
  if (is_ajax()) die();
}
add_action( 'wp_ajax_load_more_posts', __NAMESPACE__ . '\\load_more_posts' );
add_action( 'wp_ajax_nopriv_load_more_posts', __NAMESPACE__ . '\\load_more_posts' );

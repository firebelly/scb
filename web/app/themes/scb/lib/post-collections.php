<?php
/**
 * Post Collections
 *
 * Simple library for storing a collection of WP posts in db
 */

namespace Firebelly\Collections;

if (!session_id()) {
  session_start();
}

/**
 * Create a new collection
 */
function new_collection() {
  global $wpdb;

  session_regenerate_id(TRUE);

  // check for existing
  $collection_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}collections WHERE session_id=%s", session_id()));
  if ($collection_id) return $collection_id;

  $wpdb->insert( 
    $wpdb->prefix.'collections', 
    [ 
      'created_at' => current_time('mysql'),
      'session_id' => session_id(),
      'user_id' => get_current_user_id(),
    ] 
  );
  return $wpdb->insert_id;
}

/**
 * Add post to collection
 */
function add_post_to_collection($collection_id, $post_id) {
  global $wpdb;
  $res = false;
  $post_in_collection = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}collection_posts WHERE collection_id=%d AND post_id=%d", $collection_id, $post_id));
  if ($post_in_collection) return;
  $post = get_post($post_id);
  if ($post) {
    $max_pos = $wpdb->get_var($wpdb->prepare("SELECT MAX(position) FROM {$wpdb->prefix}collection_posts WHERE collection_id=%d", $collection_id));
    $res = $wpdb->insert(
      $wpdb->prefix.'collection_posts', 
      [
        'collection_id' => $collection_id,
        'post_type' => $post->post_type,
        'post_id' => $post->ID,
        'position' => $max_pos + 1,
        'created_at' => current_time('mysql'),
      ]
    );
  }
  return $res;
}

/**
 * Remove a post from a collection
 */
function remove_post_from_collection($collection_id, $post_id) {
  global $wpdb;
  $res = $wpdb->delete( $wpdb->prefix.'collection_posts', [ 'collection_id' => $collection_id, 'post_id' => $post_id ] );
  return $res;
}

/**
 * Get user's active collection
 * @return object $collection
 */
function get_active_collection() {
  global $wpdb;

  $active_collection = $wpdb->get_row( 
    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}collections WHERE session_id = %s", session_id())
  );
  if ($active_collection) {
    return get_collection($active_collection->ID);
  } else {
    $id = new_collection();
    return get_collection($id);
  }
 } 

/**
 * Get a collection object given id
 */
function get_collection($collection_id) {
  global $wpdb;
  $collection = $wpdb->get_row( 
    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}collections WHERE ID = %d", $collection_id)
  );

  if ($collection) {
    $collection->posts = $wpdb->get_results(
      $wpdb->prepare(
        "
        SELECT * FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->prefix}collection_posts cp ON cp.post_id = p.ID
        WHERE cp.collection_id = %d
        AND post_status = 'publish'
        ORDER BY cp.post_type, cp.position ASC
        ",
        $collection_id
      )
    );
  }

  return $collection;
}

/**
 * Is post in collection?
 */
function post_in_collection($collection, $post_id) {
  $in_collection = false;
  foreach ($collection->posts as $post) {
    if ($post->ID==$post_id)
      $in_collection = true;
  }

  return $in_collection;
}

/**
 * AJAX collection events
 */
function collection_action() {
  $collection = empty($_REQUEST['collection_id']) ? get_active_collection() : get_collection((int)$_REQUEST['collection_id']);
  if ($collection && !empty($_REQUEST['do'])) {
    $do = $_REQUEST['do'];
    if ($do=='add')
      add_post_to_collection($collection->ID, $_REQUEST['post_id']);
    else if ($do=='remove')
      remove_post_from_collection($collection->ID, $_REQUEST['post_id']);
  }
  
  // todo: return html of collection template
  echo json_encode([
    'status' => 1
  ]);

  // we use this call outside AJAX calls; WP likes die() after an AJAX call
  if (is_ajax()) die();
}
add_action( 'wp_ajax_collection_action', __NAMESPACE__ . '\\collection_action' );
add_action( 'wp_ajax_nopriv_collection_action', __NAMESPACE__ . '\\collection_action' );

/**
 * Collection page
 */
function collection_rewrites() {
  add_rewrite_rule(
    'collection/(\d+)/?$',
    'index.php?pagename=collection&collection_id=$matches[1]',
    'top' 
  );
}
add_action('init', __NAMESPACE__.'\collection_rewrites');

function collection_query_vars($query_vars) {
  $query_vars[] = 'collection_id';
  return $query_vars;
}
add_filter('query_vars', __NAMESPACE__.'\collection_query_vars');

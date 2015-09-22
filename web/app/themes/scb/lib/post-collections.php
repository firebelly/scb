<?php
/**
 * Post Collections
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
  $post_in_collection = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}collection_posts WHERE collection_id=%d AND post_id=%d", $collection_id, $post_id));
  if ($post_in_collection) return;
  $post = get_post($post_id);
  if ($post) {
    $max_pos = $wpdb->get_var($wpdb->prepare("SELECT MAX(position) FROM {$wpdb->prefix}collection_posts WHERE collection_id=%d", $collection_id));
    $wpdb->insert(
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
}

/**
 * Remove a post from a collection
 */
function remove_post_from_collection($collection_id, $post_id) {
  global $wpdb;
  $res = $wpdb->delete( $wpdb->prefix.'collection_posts', [ 'collection_id' => $collection_id, 'post_id' => $post_id ] );
  return $res;
}

function get_active_collection() {
  global $wpdb;
  $active_collection = $wpdb->get_row( 
    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}collections WHERE session_id = %s", session_id())
  );
  if ($active_collection) {
    return get_collection($active_collection->ID);
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
        GROUP BY cp.post_type
        ORDER BY cp.position ASC
        ",
        $collection_id
      )
    );
  }

  return $collection;
}

/**
 * Show posts in collection
 */
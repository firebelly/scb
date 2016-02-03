<?php
/**
 * Extra fields, admin changes, and filters for various post types
 */

namespace Firebelly\PostTypes\Posts;

// Custom CMB2 fields for post type
function metaboxes(array $meta_boxes) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['related_office'] = array(
    'id'            => 'related_office',
    'title'         => __( 'Related Office', 'cmb2' ),
    'object_types'  => array( 'person', 'position', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => true,
    'fields'        => array(
      array(
          'id'       => $prefix . 'related_office',
          'type'     => 'select',
          'show_option_none' => true,
          'options'  => \Firebelly\CMB2\get_post_options(['post_type' => 'office', 'numberposts' => -1]),
      ),
    ),
  );

  $meta_boxes['featured'] = array(
    'id'            => 'featured',
    'title'         => __( 'Featured Post', 'cmb2' ),
    'object_types'  => array( 'post', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => false,
    'fields'        => array(
      array(
          'id'       => '_featured',
          'type'     => 'checkbox',
          'desc'     => 'Show at top of homepage',
          'name'     => 'Yes',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

add_filter('manage_edit-post_columns', __NAMESPACE__.'\my_columns');
function my_columns($columns) {
    $columns['featured'] = 'Featured';
    return $columns;
}
add_action('manage_posts_custom_column',  __NAMESPACE__.'\my_show_columns');
function my_show_columns($name) {
    global $post;
    switch ($name) {
        case 'featured':
            $featured = get_post_meta($post->ID, '_featured', true);
            echo $featured ? '✓' : '';
    }
}

/**
 * Move Featured posts to top of query
 */
function featured_orderby($orderby) {
  global $wpdb;

  $sticky_posts = array();
  $results = $wpdb->get_results("SELECT post_id FROM wp_postmeta WHERE meta_key='_featured' AND meta_value='on'");
  if ($results) {
    $i = count($results);
    $sql = ' CASE';
    foreach($results as $result) {
      $sql .= " WHEN $wpdb->posts.ID = {$result->post_id} THEN {$i}";
      $i--;
    }
    $sql .= ' ELSE 0 END DESC, ';
    $orderby = $sql . $orderby;
  }
  return $orderby;
}
add_action('pre_get_posts', __NAMESPACE__.'\init_featured_orderby');
function init_featured_orderby($query) {
  if($query->is_main_query() && !$query->is_feed()) {
    add_filter('posts_orderby', __NAMESPACE__.'\featured_orderby');
  }
}

/**
 * Hide unused/redundant UI in admin
 */
function remove_sub_menus() {
  // remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
  // remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
  remove_submenu_page('edit.php?post_type=person', 'edit-tags.php?taxonomy=project_category&amp;post_type=person');
  remove_submenu_page('edit.php?post_type=position', 'edit-tags.php?taxonomy=project_category&amp;post_type=position');
}
add_action('admin_init', __NAMESPACE__ . '\remove_sub_menus');

// function remove_post_metaboxes() {
//   remove_meta_box( 'focus_areadiv','event','normal' ); // hide default Focus Area UI
//   remove_meta_box( 'focus_areadiv','program','normal' );
//   remove_meta_box( 'focus_areadiv','post','normal' );
//   remove_meta_box( 'tagsdiv-post_tag','post','normal' ); // hide Tags UI
//   remove_meta_box( 'categorydiv','post','normal' ); // hide Category UI
// }
// add_action('admin_menu', __NAMESPACE__ . '\remove_post_metaboxes');

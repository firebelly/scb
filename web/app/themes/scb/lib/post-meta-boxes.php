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

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

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

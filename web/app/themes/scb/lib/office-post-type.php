<?php
/**
 * Office post type
 */

namespace Firebelly\PostTypes\Office;

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Offices',
    'singular_name'       => 'Office',
    'menu_name'           => 'Offices',
    'parent_item_colon'   => '',
    'all_items'           => 'All Offices',
    'view_item'           => 'View Office',
    'add_new_item'        => 'Add New Office',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Office',
    'update_item'         => 'Update Office',
    'search_items'        => 'Search Offices',
    'not_found'           => 'Not found',
    'not_found_in_trash'  => 'Not found in Trash',
  );
  $rewrite = array(
    'slug'                => '',
    'with_front'          => false,
    'pages'               => false,
    'feeds'               => false,
  );
  $args = array(
    'label'               => 'office',
    'description'         => 'Offices',
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', 'thumbnail', ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'office',
    'map_meta_cap'        => true
  );
  register_post_type( 'office', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_office');
  $role_admin->add_cap('read_office');
  $role_admin->add_cap('delete_office');
  $role_admin->add_cap('edit_offices');
  $role_admin->add_cap('edit_others_offices');
  $role_admin->add_cap('publish_offices');
  $role_admin->add_cap('read_private_offices');
  $role_admin->add_cap('delete_offices');
  $role_admin->add_cap('delete_private_offices');
  $role_admin->add_cap('delete_published_offices');
  $role_admin->add_cap('delete_others_offices');
  $role_admin->add_cap('edit_private_offices');
  $role_admin->add_cap('edit_published_offices');
  $role_admin->add_cap('create_offices');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    // 'featured_image' => 'Image',
  );
  return $columns;
}
add_filter('manage_office_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'office' ) {
    if ( $column == 'featured_image' )
      echo the_post_thumbnail('thumbnail');
    else {
      $custom = get_post_custom();
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  };
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['office_metabox'] = array(
    'id'            => 'office_metabox',
    'title'         => __( 'Office Details', 'cmb2' ),
    'object_types'  => array( 'office', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Intro',
        'desc' => 'Brief description',
        'id'   => $prefix . 'intro',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 8,
        ),
      ),
      array(
        'name' => 'Submit Portfolio Call',
        'desc' => 'e.g. We are always in search of bright, talented, dynamic professionals.',
        'id'   => $prefix . 'submit_portfolio_call',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 4,
        ),
      ),
      array(
          'name'    => 'Address',
          'id'      => $prefix . 'address',
          'type'    => 'address',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Geocode address and save in custom fields
 */
add_action('save_post_office', '\Firebelly\Map\geocode_address', 20, 2);

/**
 * Get num active Offices
 */
function get_num_offices() {
  global $wpdb;
  return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'office' AND post_status = 'publish'" );
}

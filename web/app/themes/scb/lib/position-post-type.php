<?php
/**
 * Position post type
 */

namespace Firebelly\PostTypes\Position;

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Positions',
    'singular_name'       => 'Position',
    'menu_name'           => 'Positions',
    'parent_item_colon'   => '',
    'all_items'           => 'All Positions',
    'view_item'           => 'View Position',
    'add_new_item'        => 'Add New Position',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Position',
    'update_item'         => 'Update Position',
    'search_items'        => 'Search Positions',
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
    'label'               => 'position',
    'description'         => 'Positions',
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
    'capability_type'     => 'position',
    'map_meta_cap'        => true
  );
  register_post_type( 'position', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_position');
  $role_admin->add_cap('read_position');
  $role_admin->add_cap('delete_position');
  $role_admin->add_cap('edit_positions');
  $role_admin->add_cap('edit_others_positions');
  $role_admin->add_cap('publish_positions');
  $role_admin->add_cap('read_private_positions');
  $role_admin->add_cap('delete_positions');
  $role_admin->add_cap('delete_private_positions');
  $role_admin->add_cap('delete_published_positions');
  $role_admin->add_cap('delete_others_positions');
  $role_admin->add_cap('edit_private_positions');
  $role_admin->add_cap('edit_published_positions');
  $role_admin->add_cap('create_positions');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    '_cmb2_open_for_applications' => 'Open for Applications',
    'related_office' => 'Office',
    'date' => 'Date',
    // 'featured_image' => 'Image',
  );
  return $columns;
}
add_filter('manage_position_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'position' ) {
    if ( $column == 'featured_image' ) {
      echo the_post_thumbnail('thumbnail');
    } elseif ( $column == '_cmb2_open_for_applications' ) {
      $custom = get_post_custom();
      echo array_key_exists($column, $custom) ? '&#9989;' : ''; 
    } elseif ( $column == 'related_office' ) {
      if ($office = \Firebelly\Utils\get_office($post)) {
        echo $office->post_title;
      }
    } else {
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

  $meta_boxes['position_metabox'] = array(
    'id'            => 'position_metabox',
    'title'         => __( 'Position Details', 'cmb2' ),
    'object_types'  => array( 'position', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Open for Applications',
        'desc' => 'If checked, allows people to submit applications',
        'id'   => $prefix . 'open_for_applications',
        'type' => 'checkbox',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );


/**
 * Get Positions matching office
 */
function get_positions($filters=[]) {
  $output = '';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'position',
    'orderby' => ['date' => 'ASC'],
    );
  if (!empty($filters['office'])) {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'office',
        'field' => 'slug',
        'terms' => $filters['office']
      )
    );
  }

  $positions = get_posts($args);
  return $positions;
}
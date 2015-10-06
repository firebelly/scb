<?php
/**
 * Person post type
 */

namespace Firebelly\PostTypes\Person;

// Custom image size for post type?
// add_image_size( 'people-thumb', 300, 300, true );

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'People',
    'singular_name'       => 'Person',
    'menu_name'           => 'People',
    'parent_item_colon'   => '',
    'all_items'           => 'All People',
    'view_item'           => 'View Person',
    'add_new_item'        => 'Add New Person',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Person',
    'update_item'         => 'Update Person',
    'search_items'        => 'Search People',
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
    'label'               => 'person',
    'description'         => 'People',
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
    'capability_type'     => 'person',
    'map_meta_cap'        => true
  );
  register_post_type( 'person', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_person');
  $role_admin->add_cap('read_person');
  $role_admin->add_cap('delete_person');
  $role_admin->add_cap('edit_persons');
  $role_admin->add_cap('edit_others_persons');
  $role_admin->add_cap('publish_persons');
  $role_admin->add_cap('read_private_persons');
  $role_admin->add_cap('delete_persons');
  $role_admin->add_cap('delete_private_persons');
  $role_admin->add_cap('delete_published_persons');
  $role_admin->add_cap('delete_others_persons');
  $role_admin->add_cap('edit_private_persons');
  $role_admin->add_cap('edit_published_persons');
  $role_admin->add_cap('create_persons');
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
add_filter('manage_person_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'person' ) {
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

  $meta_boxes['person_metabox'] = array(
    'id'            => 'person_metabox',
    'title'         => __( 'Person Details', 'cmb2' ),
    'object_types'  => array( 'person', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Subtitle',
        'desc' => 'e.g. Drew Ranieri, AIA',
        'id'   => $prefix . 'subtitle',
        'type' => 'text',
      ),
      array(
        'name' => 'Position',
        // 'desc' => '',
        'id'   => $prefix . 'position',
        'type' => 'text',
      ),
      array(
        'name' => 'Studied At',
        'desc' => '',
        'id'   => $prefix . 'studied_at',
        'type' => 'text',
      ),
    ),
  );

  $meta_boxes['related_office'] = array(
    'id'            => 'related_office',
    'title'         => __( 'Related Office', 'cmb2' ),
    'object_types'  => array( 'person', 'project', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => true,
    'fields'        => array(
      array(
          // 'name'     => 'If set, will trump finding a related program by Focus Area',
          // 'desc'     => 'Select Program(s)...',
          'id'       => $prefix . 'related_office',
          'type'     => 'select',
          'show_option_none' => true,
          // 'type'     => 'pw_multiselect', // currently multiple=true is causing issues with pw_multiselect -nate 4/30/15 
          // 'multiple' => true, 
          'options'  => \Firebelly\CMB2\get_post_options(['post_type' => 'office', 'numberposts' => -1]),
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get People matching category
 */
function get_people($filters=[]) {
  $output = '';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'person',
    'orderby' => ['title' => 'ASC'],
    );
  if (!empty($filters['category'])) {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'project_category',
        'field' => 'slug',
        'terms' => $category
      )
    );
  }

  $person_posts = get_posts($args);
  return $person_posts;
}
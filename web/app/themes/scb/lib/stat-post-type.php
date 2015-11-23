<?php
/**
 * Stat post type
 */

namespace Firebelly\PostTypes\Stat;

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Stats',
    'singular_name'       => 'Stat',
    'menu_name'           => 'Stats',
    'parent_item_colon'   => '',
    'all_items'           => 'All Stats',
    'view_item'           => 'View Stat',
    'add_new_item'        => 'Add New Stat',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Stat',
    'update_item'         => 'Update Stat',
    'search_items'        => 'Search Stats',
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
    'label'               => 'stat',
    'description'         => 'Stats',
    'labels'              => $labels,
    'supports'            => array( 'title' ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_stat'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'stat',
    'map_meta_cap'        => true
  );
  register_post_type( 'stat', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_stat');
  $role_admin->add_cap('read_stat');
  $role_admin->add_cap('delete_stat');
  $role_admin->add_cap('edit_stats');
  $role_admin->add_cap('edit_others_stats');
  $role_admin->add_cap('publish_stats');
  $role_admin->add_cap('read_private_stats');
  $role_admin->add_cap('delete_stats');
  $role_admin->add_cap('delete_private_stats');
  $role_admin->add_cap('delete_published_stats');
  $role_admin->add_cap('delete_others_stats');
  $role_admin->add_cap('edit_private_stats');
  $role_admin->add_cap('edit_published_stats');
  $role_admin->add_cap('create_stats');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'project_category' => 'Category',
    '_cmb2_stat_number' => 'Number',
    '_cmb2_stat_label' => 'Label',
    '_cmb2_stat_callout' => 'Callout',
    '_cmb2_stat_url' => 'URL',
    'date' => 'Date',
  );
  return $columns;
}
add_filter('manage_stat_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ($post->post_type == 'stat') {
    if ($column == 'project_category') {
      if ($terms = get_the_terms($post->ID, $column)) {
        $out = [];
        foreach ( $terms as $term ) {
          $out[] = sprintf( '<a href="%s">%s</a>',
            esc_url( add_query_arg( array( 'action' => 'edit', 'taxonomy' => $column, 'tag_ID' => $term->term_id ), 'edit-tags.php' ) ),
            esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'project_category', 'display' ) )
          );
        }
        echo join( ', ', $out );
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

  $meta_boxes['stat_details'] = array(
    'id'            => 'stat_details',
    'title'         => __( 'Stat Details', 'cmb2' ),
    'object_types'  => array( 'stat', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name'     => 'Related Category',
        'id'       => $prefix . 'related_category',
        'taxonomy' => 'project_category',
        'type'     => 'taxonomy_select',
        'options'  => [
          'hierarchical' => true // doesn't do anything, maybe need to do custom function here?
        ]
      ),
      array(
        'name'     => 'Number',
        'id'       => $prefix . 'stat_number',
        'desc'     => 'Stat number, e.g. 5 or 1,250',
        'type'     => 'text',
      ),
      array(
        'name'     => 'Label',
        'id'       => $prefix . 'stat_label',
        'desc'     => 'Stat label, e.g. Stories or Units',
        'type'     => 'text',
      ),
      array(
        'name'     => 'Callout',
        'id'       => $prefix . 'stat_callout',
        'desc'     => 'e.g. About SCB',
        'type'     => 'text',
      ),
      array(
        'name'     => 'URL',
        'id'       => $prefix . 'stat_url',
        'desc'     => 'Where the callout links to, e.g. /about',
        'type'     => 'text',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

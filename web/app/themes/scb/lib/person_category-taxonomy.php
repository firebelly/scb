<?php
/**
 * Person Category taxonomy
 */

namespace Firebelly\PostTypes\PersonCategory;

/**
 * Add capabilities to control permissions of Taxonomy via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('manage_person_category');
  $role_admin->add_cap('edit_person_category');
  $role_admin->add_cap('delete_person_category');
  $role_admin->add_cap('assign_person_category');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom taxonomy People Categories
register_taxonomy( 'person_category', 
  array('person', ),
  array('hierarchical' => true, // if this is true, it acts like categories
    'labels' => array(
      'name' => 'People Categories',
      'singular_name' => 'Person Category',
      'search_items' =>  'Search People Categories',
      'all_items' => 'All People Categories',
      'parent_item' => 'Parent Person Category',
      'parent_item_colon' => 'Parent Person Category:',
      'edit_item' => 'Edit Person Category',
      'update_item' => 'Update Person Category',
      'add_new_item' => 'Add New Person Category',
      'new_item_name' => 'New Person Category',
    ),
    'show_admin_column' => true,
    'show_ui' => true,
    'query_var' => true,
    'capabilities' => array(
        'manage_terms' => 'manage_person_category',
        'edit_terms' => 'edit_person_category',
        'delete_terms' => 'delete_person_category',
        'assign_terms' => 'assign_person_category'
    ),
    'rewrite' => array( 
      'slug' => 'persons',
      'with_front' => false 
    ),
  )
);

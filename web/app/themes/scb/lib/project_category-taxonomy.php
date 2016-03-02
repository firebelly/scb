<?php
/**
 * Project Category taxonomy
 */

namespace Firebelly\PostTypes\ProjectCategory;

/**
 * Add capabilities to control permissions of Taxonomy via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('manage_project_category');
  $role_admin->add_cap('edit_project_category');
  $role_admin->add_cap('delete_project_category');
  $role_admin->add_cap('assign_project_category');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom taxonomy Project Categories
register_taxonomy( 'project_category', 
  array('project', 'person', 'position', 'post', ),
  array('hierarchical' => true, // if this is true, it acts like categories
    'labels' => array(
      'name' => 'Project Categories',
      'singular_name' => 'Project Category',
      'search_items' =>  'Search Project Categories',
      'all_items' => 'All Project Categories',
      'parent_item' => 'Parent Project Category',
      'parent_item_colon' => 'Parent Project Category:',
      'edit_item' => 'Edit Project Category',
      'update_item' => 'Update Project Category',
      'add_new_item' => 'Add New Project Category',
      'new_item_name' => 'New Project Category',
    ),
    'show_admin_column' => true,
    'show_ui' => true,
    'query_var' => true,
    'capabilities' => array(
        'manage_terms' => 'manage_project_category',
        'edit_terms' => 'edit_project_category',
        'delete_terms' => 'delete_project_category',
        'assign_terms' => 'assign_project_category'
    ),
    'rewrite' => array( 
      'slug' => 'projects',
      'with_front' => false 
    ),
  )
);

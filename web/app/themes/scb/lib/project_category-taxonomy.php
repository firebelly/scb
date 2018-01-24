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

function get_description($term) {
  global $wpdb;
  $description = '';
  if (!is_object($term)) {
    return '';
  }
  // Does this term have a description set?
  if (!empty($term->description)) {
    $description = '<h2>'.$term->description.'</h2>';
  } else {
    // Try to find description from parent/grandparent
    if ($parent = $wpdb->get_var("SELECT parent FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = ".$term->term_id)) {
      $parent_term = get_term($parent, 'project_category');
      if (!empty($parent_term->description)) {
        // Parent description?
        $description = '<h2>'.$parent_term->description.'</h2>';
      } else if ($grandparent = $wpdb->get_var("SELECT parent FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = ".$parent)) {
        // Grandparent description?
        $grandparent_term = get_term($grandparent, 'project_category');
        if (!empty($grandparent_term->description))
          $description = '<h2>'.$grandparent_term->description.'</h2>';
      }
    }
  }
  return $description;
}

/**
 * Hook into FB metatag for this taxonomy type
 */
function metatag_description($string) {
  global $term;
  if (is_tax('project_category') && !empty($term)) {
    $description = \Firebelly\PostTypes\ProjectCategory\get_description($term);
    if (!empty($description)) {
      $string = strip_tags($description);
    }
  }
  return $string;
}
add_filter('fb_metatag_description', __NAMESPACE__ . '\metatag_description');
function metatag_title($string) {
  global $term;
  if (is_tax('project_category') && !empty($term)) {
    $string = $term->name;
  }
  return $string;
}
add_filter('fb_metatag_title', __NAMESPACE__ . '\metatag_title');

<?php
/**
 * Extra fields for Pages
 */

namespace Firebelly\PostTypes\Pages;

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  // $meta_boxes['page_metabox'] = array(
  //   'id'            => 'page_metabox',
  //   'title'         => __( 'Extra Fields', 'cmb2' ),
  //   'object_types'  => array( 'page', ), // Post type
  //   'context'       => 'normal',
  //   'priority'      => 'high',
  //   'show_names'    => true, // Show field names on the left
  //   'fields'        => array(
      
  //     // General page fields
  //     array(
  //       'name' => 'Secondary Content',
  //       'id'   => $prefix . 'secondary_content',
  //       'type' => 'wysiwyg',
  //     ),
  //   ),
  // );

  $meta_boxes['careers_content'] = array(
    'id'            => 'careers_content',
    'title'         => __( 'Secondary content areas', 'cmb2' ),
    'object_types'  => array( 'page', ), // Post type
    'show_on'       => array( 'key' => 'id', 'value' => 11 ), // Only show on 'Who We Are' page
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      
      // General page fields
      array(
        'name' => 'Secondary Content',
        'id'   => $prefix . 'secondary_content',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Career page images',
        'id'   => $prefix . 'careers_images',
        'type' => 'file_list',
        'description' => 'The images displayed on the page according to the following layout (in order): <img style="vertical-align: top" src="/app/themes/scb/dist/images/careers-image-layout.png">',
      ),
      array(
        'name' => 'Middle Column 1',
        'id'   => $prefix . 'middle_column_1',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Middle Column 2',
        'id'   => $prefix . 'middle_column_2',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Middle Column 3',
        'id'   => $prefix . 'middle_column_3',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Terms Left',
        'id'   => $prefix . 'terms_left',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Terms Right',
        'id'   => $prefix . 'terms_right',
        'type' => 'wysiwyg',
      ),
    ),
  );

  $meta_boxes['internships_content'] = array(
    'id'            => 'internships_content',
    'title'         => __( 'Secondary content areas', 'cmb2' ),
    'object_types'  => array( 'page', ), // Post type
    'show_on'       => array( 'key' => 'id', 'value' => 1783 ), // Only show on 'Internships' page
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      
      // General page fields
      array(
        'name' => 'Intro Content',
        'id'   => $prefix . 'intro_content',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Number of internships',
        'id'   => $prefix . 'num_internships',
        'type' => 'text_small',
      )
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );
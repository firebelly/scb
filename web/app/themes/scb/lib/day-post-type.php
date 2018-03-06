<?php
/**
 * Day post type
 */

namespace Firebelly\PostTypes\Day;
use PostTypes\PostType; // see https://github.com/jjgrainger/PostTypes

$cpt = new PostType('day', [
  'supports'   => ['title', 'editor', 'thumbnail'],
  'rewrite'    => ['with_front' => false],
]);
$cpt->register();

/**
 * CMB2 custom fields
 */
function metaboxes() {
  $prefix = '_cmb2_';

  $day_info = new_cmb2_box([
    'id'            => $prefix . 'day_info',
    'title'         => __( 'Day Info', 'cmb2' ),
    'object_types'  => ['day'],
    'context'       => 'normal',
    'priority'      => 'high',
  ]);
  $day_info->add_field([
    'name'      => 'Archived',
    'id'        => $prefix . 'archived',
    'type'      => 'checkbox',
    'desc'      => 'If checked, will not show in listing on Careers',
    'column'    => [
      'position' => 3,
      'name'     => 'Archived',
    ],
  ]);
}
add_filter( 'cmb2_admin_init', __NAMESPACE__ . '\metaboxes' );


/**
 * Get Days with active
 */
function get_days($filters=[]) {
  $output = '';
  $args = [
    'numberposts' => -1,
    'post_type' => 'day',
    'orderby' => ['date' => 'ASC'],
    'meta_query' => [
      [
        'key' => '_cmb2_archived',
        'compare' => 'NOT EXISTS'
      ]
    ]
  ];

  $days = get_posts($args);
  return $days;
}

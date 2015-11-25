<?php
/**
 * Map Functions
 *
 * Pull project locations, office locations, 
 */

namespace Firebelly\Map;

/**
 * Geocode address and save in custom fields
 */
function geocode_address($post_id) {
  $address = get_post_meta($post_id, '_cmb2_address', 1);
  $address = wp_parse_args($address, array(
      'address-1' => '',
      'address-2' => '',
      'city'      => '',
      'state'     => '',
      'zip'       => '',
   ));

  if (!empty($address['address-1'])) {
    $address_combined = $address['address-1'] . ' ' . $address['address-1'] . ' ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'];
    $request_url = "http://maps.google.com/maps/api/geocode/xml?sensor=false&address=" . urlencode($address_combined);

    $xml = simplexml_load_file($request_url);
    $status = $xml->status;
    if(strcmp($status, 'OK')===0) {
      $lat = $xml->result->geometry->location->lat;
      $lng = $xml->result->geometry->location->lng;
      update_post_meta($post_id, '_cmb2_lat', (string)$lat);
      update_post_meta($post_id, '_cmb2_lng', (string)$lng);
    }
  }
}

/**
 * Function to get map points
 */
function get_map_points() {
  global $wpdb;
  $map_posts = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_cmb2_lat'" );
  $posts = get_posts([
    'numberposts' => -1,
    'post_type' => ['project','office'],
    'post__in' => $map_posts
  ]);
  if (!$posts) return false;
  $output = '';
  foreach ($posts as $post) {
    $lat = get_post_meta($post->ID, '_cmb2_lat', true);
    $lng = get_post_meta($post->ID, '_cmb2_lng', true);
    $url = get_permalink($post->ID);
    $desc = \Firebelly\Utils\get_excerpt($post);
    $output .= '<span class="map-point" data-url="' . $url . '" data-lat="' . $lat . '" data-lng="' . $lng . '" data-title="' . $post->post_title . '" data-desc="' . $desc . '" data-id="' . $post->ID . '"></span>';
  }
  return $output;
}

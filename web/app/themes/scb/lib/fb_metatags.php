<?php

/**
 * Meta tags, OG tags, mega tags, hot tags, mad tags
 */

// Heavily borrowing from https://wordpress.org/plugins/wp-facebook-open-graph-protocol/
namespace Firebelly\Metatags;

add_action('wp_head', __NAMESPACE__ . '\build_tags', 50);

function build_tags() {
  global $post;

  if(!is_object($post) || get_class($post) != 'WP_Post') {
    return;
  }

  // Get Facebook ID for OG tags
  $facebook_app_id = \Firebelly\SiteOptions\get_option('facebook_app_id');
  if ($facebook_app_id) {
    echo '<meta property="fb:app_id" content="' . esc_attr($facebook_app_id) . '"/>' . "\n";
  }
  // Not using this yet
  // if ($facebook_admin_ids) {
  //   echo '<meta property="fb:admins" content="' . esc_attr($facebook_admin_ids) . '"/>' . "\n";
  // }

  // URL
  if (is_front_page()) {
    $metatag_url = home_url();
  } else {
    $metatag_url = 'http' . (is_ssl() ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  }
  echo '<meta property="og:url" content="' . esc_url(user_trailingslashit(trailingslashit(apply_filters('fb_metatag_url', $metatag_url)))) . '"/>' . "\n";

  // Title
  if (is_home() || is_front_page()) {
    $metatag_title = get_bloginfo('name');
  } else {
    $metatag_title = get_the_title();
  }
  echo '<meta property="og:title" content="' . esc_attr(apply_filters('fb_metatag_title', $metatag_title)) . '"/>' . "\n";

  // Site name
  echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '"/>' . "\n";

  // Description
  if (is_singular()) {
    if (has_excerpt($post->ID)) {
      $metatag_description = strip_tags(get_the_excerpt());
    } else {
      $metatag_description = str_replace("\r\n", ' ' , substr(strip_tags(strip_shortcodes($post->post_content)), 0, 160));
    }
  } else {
    $metatag_description = get_bloginfo('description');
  }
  echo '<meta property="og:description" content="' . esc_attr(apply_filters('fb_metatag_description', $metatag_description)) . '"/>' . "\n";

  // Page type
  if (is_single()) {
    $metatag_type = 'article';
  } else {
    $metatag_type = 'website';
  }
  echo '<meta property="og:type" content="' . esc_attr(apply_filters('fb_metatag_type', $metatag_type)) . '"/>' . "\n";

  // Find/output any images for use in the OGP tags
  $metatag_images = array();

  // Only find images if it isn't the homepage and the fallback isn't being forced
  if (!is_home()) {
    // Find featured thumbnail of the current post/page
    if (function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID)) {
      $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
      $link = $thumbnail_src[0];
      if (! preg_match('/^https?:\/\//', $link)) {
        // Remove any starting slash with ltrim() and add one to the end of home_url()
        $link = home_url('/') . ltrim($link, '/');
      }
      $metatag_images[] = $link; // Add to images array
    }

    if (find_images() !== false && is_singular()) { // Use our function to find post/page images
      $metatag_images = array_merge($metatag_images, find_images()); // Returns an array already, so merge into existing
    }
  }

  // Get default metatag image from site_options
  $default_metatag_image = \Firebelly\SiteOptions\get_option('default_metatag_image');

  // Add the fallback image to the images array
  if ($default_metatag_image) {
    $metatag_images[] = $default_metatag_image;
    // $metatag_images = array_reverse($metatag_images);
  }

  // Make sure there were images passed as an array and loop through/output each
  if (!empty($metatag_images)) {
    foreach ($metatag_images as $image) {
      echo '<meta property="og:image" content="' . esc_url(apply_filters('fb_metatag_image', $image)) . '"/>' . "\n";
    }
  } else {
    // Placeholder tag for AJAX content
    echo '<meta property="og:image" content=""/>' . "\n";
  }

  // Locale
  echo '<meta property="og:locale" content="' . strtolower(esc_attr(get_locale())) . '"/>' . "\n";
}


// Function to get image in content
function find_images() {
  global $post, $posts;

  if(!is_object($post) || get_class($post) != 'WP_Post') {
    return array();
  }

  // Grab content and match first image
  $content = $post->post_content;
  $output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );

  // Make sure there was an image that was found, otherwise return false
  if ( $output === FALSE ) {
    return false;
  }

  $images = array();
  foreach ( $matches[1] as $match ) {
    // If the image path is relative, add the site url to the beginning
    if ( ! preg_match('/^https?:\/\//', $match ) ) {
      // Remove any starting slash with ltrim() and add one to the end of home_url()
      $match = home_url( '/' ) . ltrim( $match, '/' );
    }
    $images[] = $match;
  }

  return $images;
}

function start_ob() {
  // Start the buffer before any output
  if (!is_feed()) {
    ob_start(__NAMESPACE__ . '\fb_callback');
  }
}

function fb_callback( $content ) {
  // Grab the page title and meta description
  $title = preg_match( '/<title>(.*)<\/title>/', $content, $title_matches );
  $description = preg_match( '/<meta name="description" content="(.*)"/', $content, $description_matches );

  // Take page title and meta description and place it in the ogp meta tags
  if ( $title !== FALSE && count( $title_matches ) == 2 ) {
    $content = preg_replace( '/<meta property="og:title" content="(.*)">/', '<meta property="og:title" content="' . $title_matches[1] . '">', $content );
  }

  if ( $description !== FALSE && count( $description_matches ) == 2 ) {
    $content = preg_replace( '/<meta property="og:description" content="(.*)">/', '<meta property="og:description" content="' . $description_matches[1] . '">', $content );
  }

  return $content;
}

function flush_ob() {
  if (!is_feed()) {
    ob_end_flush();
  }
}

add_action('init', __NAMESPACE__ . '\start_ob', 0);
add_action('wp_footer', __NAMESPACE__ . '\flush_ob', 10000); // Fire after other plugins (which default to priority 10)

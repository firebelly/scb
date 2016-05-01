<?php
/**
 * Post Collections
 *
 * Simple library for storing a collection of WP posts in db
 */

namespace Firebelly\Collections;

// Set WP_Session expiration to 24 hours
add_filter('wp_session_expiration', function() { return 24 * 60 * 60; });

/**
 * Create a new collection
 */
function new_collection() {
  global $wpdb;

  $wpdb->insert(
    $wpdb->prefix.'collections',
    [
      'created_at' => current_time('mysql'),
      'session_id' => get_session_id(),
      'user_id' => get_current_user_id(),
    ]
  );
  return $wpdb->insert_id;
}

/**
 * Add post to collection
 */
function add_post_to_collection($collection_id, $post_id) {
  global $wpdb;
  $res = false;

  $post_in_collection = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}collection_posts WHERE collection_id=%d AND post_id=%d", $collection_id, $post_id));
  if (!$post_in_collection && $post = get_post($post_id)) {
    $max_pos = $wpdb->get_var($wpdb->prepare("SELECT MAX(position) FROM {$wpdb->prefix}collection_posts WHERE collection_id=%d", $collection_id));
    $res = $wpdb->insert(
      $wpdb->prefix.'collection_posts',
      [
        'collection_id' => $collection_id,
        'post_type' => $post->post_type,
        'post_id' => $post->ID,
        'position' => $max_pos + 1,
        'created_at' => current_time('mysql'),
      ]
    );
  }
  return $res;
}

/**
 * Remove a post from a collection
 */
function remove_post_from_collection($collection_id, $post_id) {
  global $wpdb;
  $res = $wpdb->delete( $wpdb->prefix.'collection_posts', [ 'collection_id' => $collection_id, 'post_id' => $post_id ] );
  return $res;
}

/**
 * Get user's active collection or create a new one
 * @return object $collection
 */
function get_active_collection() {
  global $wpdb;

  $active_collection = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}collections WHERE session_id = %s", get_session_id())
  );
  if ($active_collection) {
    return get_collection($active_collection->ID);
  } else {
    return null;
  }
 }

/**
 * Get user's active collection or create a new one
 * @return object $collection
 */
function get_active_or_new_collection() {
  global $wpdb;

  if ($collection = get_active_collection()) {
    return $collection;
  } else {
    $id = new_collection();
    return get_collection($id);
  }
 }

/**
 * Get a collection object given id
 */
function get_collection($collection_id) {
  global $wpdb;
  $collection = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}collections WHERE ID = %d", $collection_id)
  );

  if ($collection) {
    $collection->posts = $wpdb->get_results(
      $wpdb->prepare(
        "
        SELECT p.* FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->prefix}collection_posts cp ON cp.post_id = p.ID
        WHERE cp.collection_id = %d
        AND post_status = 'publish'
        ORDER BY cp.post_type DESC, cp.position ASC
        ",
        $collection_id
      )
    );
  }

  return $collection;
}

/**
 * Is post in collection?
 */
function post_in_collection($collection, $post_id) {
  $in_collection = false;
  if (!empty($collection) && !empty($collection->posts)) {
    foreach ($collection->posts as $post) {
      if ($post->ID==$post_id)
        $in_collection = true;
    }
  }
  return $in_collection;
}

/**
 * AJAX collection events
 */
function collection_action() {
  global $wpdb;

  $collection = empty($_REQUEST['collection_id']) ? get_active_or_new_collection() : get_collection((int)$_REQUEST['collection_id']);
  if (!$collection) return;

  if ($collection && !empty($_REQUEST['do'])) {
    $do = $_REQUEST['do'];
    if ($do=='add') {
      add_post_to_collection($collection->ID, $_REQUEST['post_id']);
    } else if ($do=='remove') {
      remove_post_from_collection($collection->ID, $_REQUEST['post_id']);
    } else if ($do=='title') {
      $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}collections SET title=%s WHERE ID=%d", $_REQUEST['title'], $collection->ID));
      wp_send_json_success();
    } else if ($do=='pdf') {
      if ($collection_pdf = collection_to_pdf($collection->ID)) {
        wp_send_json_success(['pdf' => $collection_pdf]);
      } else {
        wp_send_json_error(['message' => 'Unable to generate Collection PDF']);
      }
    }
  }

  // Reload collection to add/remove & group posts
  $collection = get_collection($collection->ID);

  // Capture template output to return with AJAX call
  ob_start();
  $collection_html = include(locate_template('templates/collection.php'));;
  $collection_html = ob_get_clean();

  wp_send_json_success(['collection_html' => $collection_html]);
}
add_action('FB_AJAX_collection_action', __NAMESPACE__ . '\\collection_action');
add_action('FB_AJAX_nopriv_collection_action', __NAMESPACE__ . '\\collection_action');

/**
 * Send an email with collection PDF attached
 */
function email_collection() {
  $to = $_REQUEST['to_email'];
  // Check if valid email
  if (!is_email($to)) {
    wp_send_json_error(['message' => 'Invalid email']);
  } else if (!($collection_pdf = collection_to_pdf($_REQUEST['collection_id']))) {
    wp_send_json_error(['message' => 'Unable to generate Collection PDF']);
  } else {
    $subject = !empty($_REQUEST['subject']) ? stripslashes($_REQUEST['subject']) : 'A collection of projects from SCB';
    $message = !empty($_REQUEST['message']) ? stripslashes($_REQUEST['message']) : 'Please see attached PDF.';
    $headers = ['From: SCB <hello@scb.com>'];
    // Add Reply-to header?
    if (!empty($_REQUEST['replyto_email']) && is_email($_REQUEST['replyto_email'])) {
      // Send user email?
      if (!empty($_REQUEST['cc_me']))
        wp_mail($_REQUEST['replyto_email'], $subject, $message, $headers, [$collection_pdf['abspath']]);
      $headers[] = 'Reply-to: ' . $_REQUEST['replyto_email'];
    }
    // Try sending email with PDF attached
    if (wp_mail($to, $subject, $message, $headers, [$collection_pdf['abspath']])) {
      // If AJAX request, return JSON
      if (\Firebelly\Ajax\is_ajax()) {
        wp_send_json_success();
      } else {
        // If not AJAX, redirect
        wp_safe_redirect(esc_url_raw(get_permalink($_REQUEST['collection_id'])), 303);
        die();
      }
    } else {
      wp_send_json_error(['message' => 'Error sending email']);
    }
  }
}
add_action('FB_AJAX_email_collection', __NAMESPACE__ . '\\email_collection');
add_action('FB_AJAX_nopriv_email_collection', __NAMESPACE__ . '\\email_collection');

/**
 * Sort a collection after dragged around
 */
function collection_sort() {
  global $wpdb;
  $collection = empty($_REQUEST['collection_id']) ? get_active_or_new_collection() : get_collection((int)$_REQUEST['collection_id']);
  if ($collection && !empty($_REQUEST['post_array'])) {
    $post_array = $_REQUEST['post_array'];
    $i = 0;
    foreach ($post_array as $post_data) {
      $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}collection_posts SET position=%d WHERE collection_id=%d AND post_id=%d", $i, $collection->ID, $post_data['id']));
      $i++;
    }
    wp_send_json_success();
  } else {
    wp_send_json_error(['message' => 'Unable to get collection']);
  }
}
add_action('FB_AJAX_collection_sort', __NAMESPACE__ . '\\collection_sort');
add_action('FB_AJAX_nopriv_collection_sort', __NAMESPACE__ . '\\collection_sort');

/**
 * Collection page URLs
 */
function collection_rewrites() {
  add_rewrite_rule(
    'collection/(\d+)/?$',
    'index.php?pagename=collection&collection_id=$matches[1]',
    'top'
  );
}
add_action('init', __NAMESPACE__.'\collection_rewrites');

/**
 * Add query var collection_id
 */
function collection_query_vars($query_vars) {
  $query_vars[] = 'collection_id';
  return $query_vars;
}
add_filter('query_vars', __NAMESPACE__.'\collection_query_vars');

/**
 * Export a collection as PDF
 */
function collection_to_pdf($id) {
  $collection = get_collection($id);
  if (empty($collection->posts)) {
    return false;
  }
  $collection_pdf = [];
  // Get upload dir
  $upload_dir = wp_upload_dir();
  $base_dir = $upload_dir['basedir'];
  $collection_filename = 'collection-' . $id;
  // Append $collection->title to filename if set
  if (!empty($collection->title)) {
    $collection_filename .= '-' . sanitize_title($collection->title);
  }
  $collection_pdf['name'] = $collection_filename . '.pdf';
  $collection_pdf['url'] = $upload_dir['baseurl'] . '/collections/' . $collection_pdf['name'];
  $collection_pdf['abspath'] = $base_dir . '/collections/' . $collection_pdf['name'];
  // Create /collections/ dir in uploads if not present
  if(!file_exists($base_dir . '/collections/')) {
    mkdir($base_dir . '/collections/');
  }

  $pdf_merge = new \iio\libmergepdf\Merger();
  $num_pdfs = 0;

  // Check if there's a cover PDF specified in Site Options
  $cover = \Firebelly\SiteOptions\get_option('cover_letter_pdf');
  if ($cover) {
    // Extract relative path of cover PDF
    preg_match('/.*\/uploads(.*)$/',$cover,$m);
    $cover_abspath = $base_dir . $m[1];
    // Add cover PDF to Collection
    $pdf_merge->addFromFile($cover_abspath);
    $num_pdfs++;
  }
  $post_type = '';
  foreach ($collection->posts as $collection_post) {
    if ($post_pdf = get_post_meta($collection_post->ID, '_cmb2_pdf', true)) {
      preg_match('/.*\/uploads(.*)$/',$post_pdf,$m);
      $post_pdf_abspath = $base_dir . $m[1];
      if (preg_match('/pdf$/',$post_pdf_abspath) && file_exists($post_pdf_abspath)) {
        $pdf_merge->addFromFile($post_pdf_abspath);
        $num_pdfs++;
      }
    }

  }
  if ($num_pdfs>0) {
    file_put_contents($collection_pdf['abspath'], $pdf_merge->merge());
    if (!\Firebelly\Ajax\is_ajax()) {
      header('Pragma: public');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime($collection_pdf['abspath'])).' GMT');
      header('Cache-Control: private', false);
      header('Content-Type: application/pdf');
      header('Content-Disposition: attachment; filename="'.basename($collection_pdf['abspath']).'"');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: '.filesize($collection_pdf['abspath']));  // provide file size
      header('Connection: close');
      readfile($collection_pdf['abspath']);
      exit();
    } else {
      return $collection_pdf;
    }
  } else {
    return false;
  }
}

/**
 * Daily cronjob to clean out old, empty collections
 */
if (WP_ENV === 'production') {
  add_action('wp', __NAMESPACE__ . '\activate_collection_clean_cron');
}
function activate_collection_clean_cron() {
  if (!wp_next_scheduled('collection_clean_cron')) {
    wp_schedule_event(current_time('timestamp'), 'daily', 'collection_clean_cron');
  }
}
add_action( 'collection_clean_cron', __NAMESPACE__ . '\collection_clean_cron' );
function collection_clean_cron() {
  global $wpdb;
  $moldy_collections = $wpdb->get_results(
    "
    SELECT ID FROM {$wpdb->prefix}collections c
    LEFT JOIN {$wpdb->prefix}collection_posts cp ON cp.collection_id = c.ID
    WHERE c.created_at < NOW() - INTERVAL 30 DAY
    AND cp.collection_id IS NULL
    "
  );
  foreach($moldy_collections as $row) {
    $wpdb->delete( $wpdb->prefix.'collections', [ 'ID' => $row->ID ] );
  }
}

/**
 * Kludgy way to get session_id (from https://github.com/ericmann/wp-session-manager/issues/24)
 */
function get_session_id() {
  return substr( filter_input( INPUT_COOKIE, WP_SESSION_COOKIE, FILTER_SANITIZE_STRING ), 0, 32 );
}
<?php
/**
 * Post Collections
 *
 * Simple library for storing a collection of WP posts in db
 */

namespace Firebelly\Collections;

if (!session_id()) {
  session_start();
}

/**
 * Create a new collection
 */
function new_collection() {
  global $wpdb;

  // session_regenerate_id(TRUE);

  // check for existing
  $collection_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}collections WHERE session_id=%s", session_id()));
  if ($collection_id) return $collection_id;

  $wpdb->insert( 
    $wpdb->prefix.'collections', 
    [ 
      'created_at' => current_time('mysql'),
      'session_id' => session_id(),
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
  if ($post_in_collection) return;
  $post = get_post($post_id);
  if ($post) {
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
 * Get user's active collection
 * @return object $collection
 */
function get_active_collection() {
  global $wpdb;

  $active_collection = $wpdb->get_row( 
    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}collections WHERE session_id = %s", session_id())
  );
  if ($active_collection) {
    return get_collection($active_collection->ID);
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
        SELECT * FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->prefix}collection_posts cp ON cp.post_id = p.ID
        WHERE cp.collection_id = %d
        AND post_status = 'publish'
        ORDER BY cp.post_type, cp.position ASC
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
  foreach ($collection->posts as $post) {
    if ($post->ID==$post_id)
      $in_collection = true;
  }

  return $in_collection;
}

/**
 * AJAX collection events
 */
function collection_action() {
  $collection = empty($_REQUEST['collection_id']) ? get_active_collection() : get_collection((int)$_REQUEST['collection_id']);
  if ($collection && !empty($_REQUEST['do'])) {
    $do = $_REQUEST['do'];
    if ($do=='add')
      add_post_to_collection($collection->ID, $_REQUEST['post_id']);
    else if ($do=='remove')
      remove_post_from_collection($collection->ID, $_REQUEST['post_id']);
    else if ($do=='pdf') {
      collection_to_pdf($collection->ID);
      wp_send_json_success();
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
add_action('wp_ajax_collection_action', __NAMESPACE__ . '\\collection_action');
add_action('wp_ajax_nopriv_collection_action', __NAMESPACE__ . '\\collection_action');

function collection_sort() {
  global $wpdb;
  $collection = empty($_REQUEST['collection_id']) ? get_active_collection() : get_collection((int)$_REQUEST['collection_id']);
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
add_action('wp_ajax_collection_sort', __NAMESPACE__ . '\\collection_sort');
add_action('wp_ajax_nopriv_collection_sort', __NAMESPACE__ . '\\collection_sort');

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
  $collection_pdf = [];
  // Get upload dir
  $upload_dir = wp_upload_dir();
  $base_dir = $upload_dir['basedir'];
  $collection_pdf['name'] = '/collections/' . 'collection-' . $id . '.pdf';
  $collection_pdf['url'] = $upload_dir['baseurl'] . $collection_pdf['name'];
  $collection_pdf['abspath'] = $base_dir . $collection_pdf['name'];
  // Create /collections/ dir in uploads if not present
  if(!file_exists($base_dir)) {
    mkdir($base_dir);
  }
  // PDF options
  $pdf = new \mikehaertl\wkhtmlto\Pdf([
    'quiet',
    'no-outline',
    'print-media-type',
    'encoding' => 'UTF-8',
    'load-error-handling' => 'ignore',
    // 'margin-bottom' => '.25in',
    // 'margin-top' => '.25in',
    // 'margin-left' => '.25in',
    // 'margin-right' => '.25in',
    'viewport-size' => '1280x1024',
    'orientation' => 'Landscape',
    'page-size' => 'Letter',
  ]);

  // Set binary location of wkhtmltopdf (must be installed manually on server)
  $pdf->binary = '/usr/local/bin/wkhtmltopdf';

  // build collection PDF from URL
  $pdf->addPage('http://scb.dev/collection/'.$id.'/');

  // Check if there's a cover PDF specified in Site Options and merge with collection PDF
  $cover = \Firebelly\SiteOptions\get_option('cover_letter_pdf');
  if ($cover) {
    $tmp_pdf = $base_dir . '/collections/' . 'collection-tmp-' . $id . '.pdf';
    if (!$pdf->saveAs($tmp_pdf)) {
      echo $pdf->getError();
    } else {
      // Extract relative path of cover PDF
      preg_match('/.*\/uploads(.*)$/',$cover,$m);
      $cover_abspath = $base_dir . $m[1];
      // Merge cover PDF + collection PDF and save
      $m = new \iio\libmergepdf\Merger();
      $m->addFromFile($cover_abspath);
      $m->addFromFile($tmp_pdf);
      file_put_contents($collection_pdf['abspath'], $m->merge());
      // Remove tmp pdf file after merging
      unlink($tmp_pdf);
    }
  } else {
    // Otherwise, just output collection PDF
    if (!$pdf->saveAs($collection_pdf['abspath'])) {
      echo $pdf->getError();
    }
  }
  // $pdf->send();
  return $collection_pdf;
}

/**
 * Email a user with collection attached
 */
function email_collection($id, $email, $message) {
  if ($collection_pdf = collection_to_pdf($id)) {
    $attachments = [ $collection_pdf['abspath'] ];
    $headers = 'From: SCB Bot <no-reply@scb.com>' . "\r\n";
    wp_mail( $email, 'Collection from SCB', $message, $headers, $attachments );
  } else {
    echo 'Unable to make PDF';
  }

}

/**
 * Daily cronjob to clean out old, empty collections
 */
if (WP_ENV == 'production') add_action('wp', __NAMESPACE__ . '\activate_collection_clean_cron');
function activate_collection_clean_cron() {
  if (!wp_next_scheduled('collection_clean_cron')) {
    wp_schedule_event(current_time('timestamp'), 'twicedaily', __NAMESPACE__ . '\collection_clean_cron');
  }
}
if (WP_ENV == 'production') add_action( 'collection_clean_cron', __NAMESPACE__ . '\collection_clean_cron' );
function collection_clean_cron() {
 $moldy_collections = $wpdb->get_results(
   "
   SELECT ID FROM {$wpdb->prefix}collections c
   LEFT JOIN {$wpdb->prefix}collection_posts cp ON cp.collection_id = c.ID
   WHERE c.created_at < NOW() - INTERVAL 1 DAY
   AND cp.collection_id IS NULL
   "
 );
 foreach($moldy_collections as $row) {
    $wpdb->delete( $wpdb->prefix.'collections', [ 'ID' => $row['ID'] ] );
  }  
}
<?php
// Custom streamlined AJAX handler (from https://coderwall.com/p/of7y2q/faster-ajax-for-wordpress)
// Mimic the actual admin-ajax
define('DOING_AJAX', true);

if (!isset( $_REQUEST['action']))
    die('-1');

require_once('../../../../wp/wp-load.php');

// Typical headers
header('Content-Type: text/html');
send_nosniff_header();

// Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');

$action = esc_attr(trim($_REQUEST['action']));
$allowed_actions = array(
    'load_post_modal',
    'load_more_posts',
    'load_more_projects',
    'collection_sort',
    'collection_action',
);
if (in_array($action, $allowed_actions)){
    if(is_user_logged_in())
        do_action('FB_AJAX_'.$action);
    else
        do_action('FB_AJAX_nopriv_'.$action);
} else {
    die('-1');
}
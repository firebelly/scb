<?php
/**
 * Applicant post type
 */

namespace Firebelly\PostTypes\Applicant;

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Applicants',
    'singular_name'       => 'Applicant',
    'menu_name'           => 'Applicants',
    'parent_item_colon'   => '',
    'all_items'           => 'All Applicants',
    'view_item'           => 'View Applicant',
    'add_new_item'        => 'Add New Applicant',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Applicant',
    'update_item'         => 'Update Applicant',
    'search_items'        => 'Search Applicants',
    'not_found'           => 'Not found',
    'not_found_in_trash'  => 'Not found in Trash',
  );
  $rewrite = array(
    'slug'                => '',
    'with_front'          => false,
    'pages'               => false,
    'feeds'               => false,
  );
  $args = array(
    'label'               => 'applicant',
    'description'         => 'Applicants',
    'labels'              => $labels,
    'supports'            => array( 'title', ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_applicant'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'applicant',
    'map_meta_cap'        => true
  );
  register_post_type( 'applicant', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_applicant');
  $role_admin->add_cap('read_applicant');
  $role_admin->add_cap('delete_applicant');
  $role_admin->add_cap('edit_applicants');
  $role_admin->add_cap('edit_others_applicants');
  $role_admin->add_cap('publish_applicants');
  $role_admin->add_cap('read_private_applicants');
  $role_admin->add_cap('delete_applicants');
  $role_admin->add_cap('delete_private_applicants');
  $role_admin->add_cap('delete_published_applicants');
  $role_admin->add_cap('delete_others_applicants');
  $role_admin->add_cap('edit_private_applicants');
  $role_admin->add_cap('edit_published_applicants');
  $role_admin->add_cap('create_applicants');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    '_cmb2_related_position' => 'Position',
    '_cmb2_application_type' => 'Application Type',
    // 'featured_image' => 'Image',
    'date' => 'Date',
  );
  return $columns;
}
add_filter('manage_applicant_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'applicant' ) {
    if ( $column == 'featured_image' )
      echo the_post_thumbnail('thumbnail');
    elseif ( $column == '_cmb2_related_position' ) {
      $custom = get_post_custom();
      if (!empty($custom[$column])) {
        $position = get_post($custom[$column][0]);
        echo $position->post_title;
      }
    } else {
      $custom = get_post_custom();
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  };
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['related_position'] = array(
    'id'            => 'related_position',
    'title'         => __( 'Related Position', 'cmb2' ),
    'object_types'  => array( 'applicant', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => true,
    'fields'        => array(
      array(
        'id'       => $prefix . 'related_position',
        'type'     => 'select',
        'show_option_none' => true,
        'options'  => \Firebelly\CMB2\get_post_options(['post_type' => 'position', 'numberposts' => -1]),
      ),
    ),
  );

  $meta_boxes['applicant_attachments'] = array(
    'id'            => 'applicant_attachments',
    'title'         => __( 'Attachments', 'cmb2' ),
    'object_types'  => array( 'applicant', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'id'   => $prefix . 'attachments',
        'type' => 'file_list',
      ),
    ),
  );

  $meta_boxes['applicant_details'] = array(
    'id'            => 'applicant_details',
    'title'         => __( 'Details', 'cmb2' ),
    'object_types'  => array( 'applicant', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name'   => 'Type',
        'id'   => $prefix . 'application_type',
        'type' => 'select',
        'options' => [
          'position' => 'Position',
          'internship' => 'Internship',
          'portfolio' => 'Portfolio',
        ],
      ),
      array(
        'name' => 'First Name',
        'id'   => $prefix . 'first_name',
        'type' => 'text',
      ),
      array(
        'name' => 'Last Name',
        'id'   => $prefix . 'last_name',
        'type' => 'text',
      ),
      array(
        'name' => 'Email',
        'id'   => $prefix . 'email',
        'type' => 'text',
      ),
      array(
        'name' => 'Phone',
        'id'   => $prefix . 'phone',
        'type' => 'text',
      ),
      array(
        'name' => 'Staff Notes',
        'desc' => 'Not shown on front end of site',
        'id'   => $prefix . 'notes',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 6,
        ),
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

function has_applied($email, $application_type='portfolio', $position_id='') {
  $args = [
    'post_status' => 'all',
    'post_type' => 'applicant',
    'meta_query' => [
      [
        'key' => '_cmb2_email',
        'value' => $email,
        'compare' => '='
      ],
      [
        'key' => '_cmb2_application_type',
        'value' => $application_type,
        'compare' => '='
      ]
    ]
  ];
  if (!empty($position_id) && is_numeric($position_id)) {
    $args['meta_query'][] = [
      'key' => '_cmb2_related_position',
      'value' => $position_id,
      'compare' => '='
    ];
  }
  return get_posts($args);
}

function new_applicant() {
  $errors = [];
  $attachments = $attachments_size = [];
  $notification_email = false;
  $name = $_POST['application_first_name'] .' '. $_POST['application_last_name'];

  if ( has_applied($_POST['application_email'], $_POST['application_type'], (!empty($_POST['position_id']) ? $_POST['position_id'] : '')) ) {
    return ["We've already received your application."];
  }

  $applicant_post = array(
    'post_title'    => 'Application from ' . $name,
    'post_type'     => 'applicant',
    'post_author'   => 1,
    'post_status'   => 'draft',
  );
  $post_id = wp_insert_post($applicant_post);
  if ($post_id) {

    update_post_meta($post_id, '_cmb2_application_type', $_POST['application_type']);
    update_post_meta($post_id, '_cmb2_first_name', $_POST['application_first_name']);
    update_post_meta($post_id, '_cmb2_last_name', $_POST['application_last_name']);
    update_post_meta($post_id, '_cmb2_email', $_POST['application_email']);
    update_post_meta($post_id, '_cmb2_phone', $_POST['application_phone']);

    if (!empty($_FILES['application_files'])) {
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');

      $files = $_FILES['application_files'];
      foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
          $file = array(
            'name' => $files['name'][$key],
            'type' => $files['type'][$key],
            'tmp_name' => $files['tmp_name'][$key],
            'error' => $files['error'][$key],
            'size' => $files['size'][$key]
          );
          $_FILES = array('application_files' => $file);
          $attachment_id = media_handle_upload('application_files', $post_id);

          if (is_wp_error($attachment_id)) {
            $errors[] = 'There was an error uploading '.$file['name'];
          } else {
            $attachment_url = wp_get_attachment_url($attachment_id);
            $attachments[$attachment_id] = $attachment_url;
            $attachments_size[$attachment_id] = $files['size'][$key];
            // if Enhanced Media Library is installed, set category
            if (function_exists('wpuxss_eml_enqueue_media')) {
              wp_set_object_terms($attachment_id, 'applications', 'media_category');
            }
          }
        }
      }
      if (!empty($attachments)) {
        update_post_meta($post_id, '_cmb2_attachments', $attachments);
      }
    }

    // Relate to Position post?
    if (!empty($_POST['position_id']) && is_numeric($_POST['position_id'])) {
      update_post_meta($post_id, '_cmb2_related_position', (int)$_POST['position_id']);

      // Send email notification?
      $notification_email = get_post_meta((int)$_POST['position_id'], '_cmb2_notification_email', true);
      if ($notification_email) {
        $position = get_post((int)$_POST['position_id']);
        $subject = 'New application for ' . $position->post_title . ' from ' . $_POST['application_first_name'] . ' ' . $_POST['application_last_name'];
        $message = 'A new application was received for ' . $position->post_title . ":\n\n";
      }
    }

    if (preg_match('/(internship|portfolio)/',$_POST['application_type'])) {
      // Pull notification email from site_options
      $notification_email = \Firebelly\SiteOptions\get_option($_POST['application_type'].'_notification_email');
      $subject = 'New ' . ucfirst($_POST['application_type']) . ' submission from ' . $_POST['application_first_name'] . ' ' . $_POST['application_last_name'];
      $message = 'A new ' . ucfirst($_POST['application_type']) . " submission was received:\n\n";
    }

    // Send email if notification_email was set for position or in site_options for internships/portfolio
    if ($notification_email) {
      $headers = ['From: Nate Beaty <nate@clixel.com>'];
      // $headers = ['From: SCB <www-data@scb.org>'];
      $message .= $_POST['application_first_name'] . ' ' . $_POST['application_last_name'] . "\n";
      $message .= 'Email: ' . $_POST['application_email'] . "\n";
      $message .= 'Phone: ' . $_POST['application_phone'] . "\n\n";
      $message .= get_edit_post_link($post_id, 'email');
      if (!empty($attachments)) {
        $attachment_paths = [];
        $size = 0;
        foreach ($attachments as $attachment_id => $attachment_url) {
          $size += $attachments_size[$attachment_id];
          $attachment_paths[] = get_attached_file($attachment_id);
        }
        if ($size >= 8000000) {
          $message .= "\n\n** Attachments exceeded 8mb, please see WP admin link above to review attached PDFs. **";
          wp_mail($notification_email, $subject, $message, $headers);
        } else {
          wp_mail($notification_email, $subject, $message, $headers, $attachment_paths);
        }
      } else {
        wp_mail($notification_email, $subject, $message, $headers);
      }
    }

  } else {
    $errors[] = 'Error inserting post';
  }

  if (empty($errors)) {
    return true;
  } else {
    return $errors;
  }
}


/**
 * AJAX Application submissions
 */
function application_submission() {
  if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['application_form_nonce'])) {
    if (wp_verify_nonce($_POST['application_form_nonce'], 'application_form')) {

      // Server side validation of required fields
      $required_fields = ['application_type',
                          'application_first_name',
                          'application_last_name',
                          'application_email',
                          'application_phone'];
      foreach($required_fields as $required) {
        if (empty($_POST[$required])) {
          $required_txt = ucwords(str_replace('_', ' ', str_replace('application_','',$required)));
          wp_send_json_error(['message' => 'Please enter a value for '.$required_txt]);
        }
      }

      // Check for valid Email
      if (!is_email($_POST['application_email'])) {
        wp_send_json_error(['message' => 'Invalid email']);
      } else {

        // Try to save new Applicant post
        $return = new_applicant();
        if (is_array($return)) {
          wp_send_json_error(['message' => 'There was an error: '.implode("\n", $return)]);
        } else {
          wp_send_json_success(['message' => 'Application was saved OK']);
        }

      }
    } else {
      // Bad nonce, man!
      wp_send_json_error(['message' => 'Invalid form submission (bad nonce)']);
    }
  }
  wp_send_json_error(['message' => 'Invalid post']);
}
add_action('wp_ajax_application_submission', __NAMESPACE__ . '\\application_submission');
add_action('wp_ajax_nopriv_application_submission', __NAMESPACE__ . '\\application_submission');

// Inject application form in footer if on career page
function application_form() {
  if (preg_match('/^\/(careers|about|office)/', $_SERVER['REQUEST_URI'])) {
    echo '<div class="application-form-template">';
    include(locate_template('templates/application-form.php'));
    echo '</div>';
  }
}
add_action('wp_footer', __NAMESPACE__ . '\\application_form', 100);

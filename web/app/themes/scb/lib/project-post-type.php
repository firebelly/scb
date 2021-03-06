<?php
/**
 * Project post type
 */

namespace Firebelly\PostTypes\Project;

// Custom image size for post type
function add_image_sizes() {
  add_image_size('project-large', 1600, 1200);
}
add_action('after_setup_theme', __NAMESPACE__.'\add_image_sizes');

/**
 * Register Custom Post Type
 */
function post_type() {

  $labels = array(
    'name'                => 'Projects',
    'singular_name'       => 'Project',
    'menu_name'           => 'Projects',
    'parent_item_colon'   => '',
    'all_items'           => 'All Projects',
    'view_item'           => 'View Project',
    'add_new_item'        => 'Add New Project',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Project',
    'update_item'         => 'Update Project',
    'search_items'        => 'Search Projects',
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
    'label'               => 'project',
    'description'         => 'Projects',
    'labels'              => $labels,
    'supports'            => array( 'title', 'thumbnail', ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'project',
    'map_meta_cap'        => true
  );
  register_post_type( 'project', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_project');
  $role_admin->add_cap('read_project');
  $role_admin->add_cap('delete_project');
  $role_admin->add_cap('edit_projects');
  $role_admin->add_cap('edit_others_projects');
  $role_admin->add_cap('publish_projects');
  $role_admin->add_cap('read_private_projects');
  $role_admin->add_cap('delete_projects');
  $role_admin->add_cap('delete_private_projects');
  $role_admin->add_cap('delete_published_projects');
  $role_admin->add_cap('delete_others_projects');
  $role_admin->add_cap('edit_private_projects');
  $role_admin->add_cap('edit_published_projects');
  $role_admin->add_cap('create_projects');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

/**
 * Custom admin columns for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    '_cmb2_client' => 'Client',
    '_cmb2_location' => 'Location',
    '_cmb2_pdf' => 'PDF',
    'date' => 'Date',
    // 'taxonomy-focus_area' => 'Focus Area',
    // 'featured_image' => 'Image',
  );
  return $columns;
}
add_filter('manage_project_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'project' ) {
    if ( $column == 'featured_image' )
      echo the_post_thumbnail('thumbnail');
    elseif ( $column == '_cmb2_pdf' ) {
      $custom = get_post_custom();
      echo array_key_exists($column, $custom) ? '&#9989;' : '';
    }
    else {
      $custom = get_post_custom();
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  };
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

/**
 * Add sorting by custom columns
 */
add_action('pre_get_posts', __NAMESPACE__.'\custom_admin_sort', 1);
function custom_admin_sort( $query ) {
   if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
      switch( $orderby ) {
         case '_cmb2_pdf':
            $query->set( 'meta_key', '_cmb2_pdf' );
            $query->set( 'orderby', 'meta_value' );
            break;
      }
   }
}
add_filter( 'manage_edit-project_sortable_columns', __NAMESPACE__.'\custom_sortable_columns' );
function custom_sortable_columns( $sortable_columns ) {
  $sortable_columns[ '_cmb2_pdf' ] = 'PDF';
  return $sortable_columns;
}


/**
 * Custom CMB2 fields for post type
 */
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['project_metabox'] = array(
    'id'            => 'project_metabox',
    'title'         => __( 'Project Details', 'cmb2' ),
    'object_types'  => array( 'project', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Client',
        'desc' => '',
        'id'   => $prefix . 'client',
        'type' => 'text',
      ),
      array(
        'name' => 'Intro',
        'desc' => 'Brief description',
        'id'   => $prefix . 'intro',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 8,
        ),
      ),
      // array(
      //   'name' => 'Additional Info',
      //   'desc' => 'e.g. stats, awards',
      //   'id'   => $prefix . 'addl_info',
      //   'type' => 'wysiwyg',
      //   'options' => array(
      //     'textarea_rows' => 8,
      //   ),
      // ),
      array(
        'name' => 'Project Layout Orientation',
        'desc' => 'Determines layout based on primary project photos',
        'id'   => $prefix . 'orientation',
        'type' => 'radio_inline',
        'default' => 'horizontal',
        'options' => array(
          'horizontal' => __( 'Horizontal', 'cmb' ),
          'vertical'   => __( 'Vertical', 'cmb' ),
        ),
      ),
    ),
  );

  $meta_boxes['project_location'] = array(
    'id'            => 'project_location',
    'title'         => __( 'Project Location', 'cmb2' ),
    'object_types'  => array( 'project', ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Location Title',
        'desc' => 'e.g. Tempe, Arizona — shown on single Project pages',
        'id'   => $prefix . 'location',
        'type' => 'text',
      ),
      array(
          'name'    => 'Address',
          'id'      => $prefix . 'address',
          'type'    => 'address',
      ),
    ),
  );

  $meta_boxes['project_pdf'] = array(
    'id'            => 'project_pdf',
    'title'         => __( 'Collection PDF', 'cmb2' ),
    'object_types'  => array( 'project', ),
    'context'       => 'side',
    'priority'      => 'low',
    'show_names'    => false,
    'fields'        => array(
      array(
          'name'    => 'PDF',
          // 'desc'    => 'Used when generating Collections',
          'id'      => $prefix . 'pdf',
          'type'    => 'file',
      ),
    ),
  );

  /**
   * Repeating blocks
   */
  $cmb_group = new_cmb2_box( array(
    'id'           => $prefix . 'metabox',
    'title'        => __( 'Project Detail Blocks', 'cmb2' ),
    'priority'      => 'low',
    'object_types' => array( 'project', ),
  ) );

  $group_field_id = $cmb_group->add_field( array(
    'id'          => $prefix . 'project_blocks',
    'type'        => 'group',
    'description' => __( 'Note that you must switch Text mode and refresh to reorder the Project Blocks', 'cmb' ),
    'options'     => array(
        'group_title'   => __( 'Block {#}', 'cmb' ),
        'add_button'    => __( 'Add Another Block', 'cmb' ),
        'remove_button' => __( 'Remove Block', 'cmb' ),
        'sortable'      => true, // beta
    ),
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
      'name' => 'Images',
      'id'   => 'images',
      'type' => 'file_list',
      'description' => __( 'If you are adding images to a block then the block should:<br> 1) be for <strong>images only</strong> (leave all text areas blank)<br> 2) should <strong>NOT</strong> be checked as an emphasis block', 'cmb' ),
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Image Layout',
    'id'   => 'image_layout',
    'type' => 'radio_inline',
    'description' => __( 'If you have added images you <strong>must</strong> select an image layout to ensure proper formatting', 'cmb' ),
    'options' => [
      1 => '<img style="vertical-align: middle" src="/app/themes/scb/dist/images/image-layout-1.png">',
      2 => '<img style="vertical-align: middle" src="/app/themes/scb/dist/images/image-layout-2.png">',
      3 => '<img style="vertical-align: middle" src="/app/themes/scb/dist/images/image-layout-3.png">',
      4 => '<img style="vertical-align: middle" src="/app/themes/scb/dist/images/image-layout-4.png">',
      5 => '<img style="vertical-align: middle" src="/app/themes/scb/dist/images/image-layout-5.png">',
    ]
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Emphasis Block',
    'id'   => 'emphasis_block',
    'type' => 'checkbox',
    'desc' => '<strong>Do not</strong> check this box if this is an image block.'
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Stat Number',
    'id'   => 'stat_number',
    'type' => 'text',
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Stat Label',
    'id'   => 'stat_label',
    'type' => 'text',
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Callout/Full-width Text',
    'desc' => 'This should not be regular body copy, but call-out style text added as a blockquote (“). Regular body copy should be placed in the <strong>Body copy/Right Column Text</strong> area.',
    'id'   => 'full_width_text',
    'type' => 'wysiwyg',
    'options' => array(
      'textarea_rows' => 4,
    ),
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Secondary content/Left Column Text',
    'desc' => 'e.g. stats, awards; <strong>always</strong> paired with Right Column Text',
    'id'   => 'left_column_text',
    'type' => 'wysiwyg',
    'options' => array(
      'textarea_rows' => 4,
    ),
  ) );

  $cmb_group->add_group_field( $group_field_id, array(
    'name' => 'Body copy/Right Column Text',
    'desc' => 'Used for regular body copy, either alone or paired with secondary content (like stats, awards, etc) in the Left Column Text area',
    'id'   => 'right_column_text',
    'type' => 'wysiwyg',
    'options' => array(
      'textarea_rows' => 4,
    ),
  ) );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Geocode address and save in custom fields
 */
add_action('save_post_project', '\Firebelly\Map\geocode_address', 20, 2);

/**
 * Get num active Projects
 */
function get_num_projects() {
  global $wpdb;
  return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'project' AND post_status = 'publish'" );
}

/**
 * Get Projects matching category
 */
function get_projects($filters=[]) {
  $output = '';
  $args = array(
    'numberposts' => -1,
    'post_type' => 'project',
    );
  if (!empty($filters['num_posts'])) {
    $args['numberposts'] = $filters['num_posts'];
  }
  if (!empty($filters['category'])) {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'project_category',
        'field' => 'slug',
        'terms' => $filters['category']
      )
    );
  }
  if (!empty($filters['search'])) {
    $args['s'] = $filters['search'];
  }

  $project_posts = get_posts($args);
  return $project_posts;
}

/**
 * Get Project Blocks
 */
function get_project_blocks($post) {
  $output = '';
  $project_blocks = get_post_meta($post->ID, '_cmb2_project_blocks', true);
  if ($project_blocks) {
    foreach ($project_blocks as $project_block) {
      if (empty($project_block['images']) && empty($project_block['stat_number']) && empty($project_block['full_width_text']) && empty($project_block['left_column_text']) && empty($project_block['right_column_text'])) {
        $output .= '';
      } else {
        $output .= '<div class="project-block' . (!empty($project_block['images']) ? ' image-block image-layout-' . $project_block['image_layout'] : '') . (!empty($project_block['emphasis_block']) ? ' emphasis-block' : '') . (!empty($project_block['stat_number']) ? ' has-stat' : '') . '">';

        if (!empty($project_block['images'])) {
          $output .= '<div class="image-grid">';
          $i = 1;
          foreach ($project_block['images'] as $image_id => $image_src) {
            $image = wp_get_attachment_image_src($image_id, 'large');
            $output .= '<div class="image image-' . $i++ . '"><span><img src="' . $image[0] . '">';
              if ($i - 1 == count($project_block['images'])) {
                if (!empty($project_block['stat_number']) || !empty($project_block['stat_label'])) {
                  $long_stat = strlen($project_block['stat_number']) > 2 ? (strlen($project_block['stat_number']) > 4 ? ' long-stat extra-long-stat' : ' long-stat') : '';
                  $output .= '<div class="stat'.$long_stat.'">';
                  $output .= !empty($project_block['stat_number']) ? '<div class="stat-number">' . $project_block['stat_number'] . '</div>' : '';
                  $output .= !empty($project_block['stat_label']) ? '<div class="stat-label">' . $project_block['stat_label'] . '</div>' : '';
                  $output .= '</span></div>';
                }
              }
            $output .= '</div>';
          }
          $output .= '</div><!-- .image-grid -->';
        }

        // if they enter only a stat
        if (!empty($project_block['stat_number']) && empty($project_block['images']) && empty($project_block['full_width_text']) && empty($project_block['left_column_text']) && empty($project_block['right_column_text'])) {
          $long_stat = strlen($project_block['stat_number']) > 2 ? (strlen($project_block['stat_number']) > 4 ? ' long-stat extra-long-stat' : ' long-stat') : '';
          $output .= '<div class="stat solo-stat'.$long_stat.'">';
          $output .= !empty($project_block['stat_number']) ? '<div class="stat-number">' . $project_block['stat_number'] . '</div>' : '';
          $output .= !empty($project_block['stat_label']) ? '<div class="stat-label">' . $project_block['stat_label'] . '</div>' : '';
          $output .= '</span></div>';
        }

        if (!empty($project_block['full_width_text'])) {
          $output .= '<div class="full-width-text user-content">' . apply_filters('the_content', $project_block['full_width_text']) . '</div>';
        }

        if (!empty($project_block['left_column_text']) || !empty($project_block['right_column_text'])) {
          $output .= '<div class="column-text-wrap">';

            if (!empty($project_block['left_column_text'])) {
              $output .= '<div class="left-column-text user-content column -left">' . apply_filters('the_content', $project_block['left_column_text']) . '</div>';
              if (empty($project_block['right_column_text'])) {
                $output .='<div class="right-column-text user-content column -right -empty"></div>';
              }
            }

            if (!empty($project_block['right_column_text'])) {
              if (empty($project_block['left_column_text'])) {
                $output .='<div class="left-column-text user-content column -left -empty"></div>';
              }
              $output .= '<div class="right-column-text user-content column -right">' . apply_filters('the_content', $project_block['right_column_text']) . '</div>';
            }

          $output .= '</div>';
        }

        $output .= '</div>';
      }
    }
  }
  return $output;
}

/**
 * Show link to Sort Projects page
 */
add_action('admin_menu', __NAMESPACE__ . '\sort_projects_admin_menu');
function sort_projects_admin_menu() {
  add_submenu_page('edit.php?post_type=project', 'Sort Projects', 'Sort Projects', 'manage_options', 'sort_projects', __NAMESPACE__ . '\sort_projects_form');
}

/**
 * Hook into FB metatag for this post type
 */
function project_metatag_description($string) {
  global $post;
  if (is_singular()) {
    $intro = get_post_meta($post->ID, '_cmb2_intro', true);
    if ($intro) {
      $string = $intro;
    }
  }
  return $string;
}
add_filter('fb_metatag_description', __NAMESPACE__ . '\project_metatag_description');

/**
 * Basic Sort Projects admin page
 */
function sort_projects_form() {
?>
  <div class="wrap">
    <h2>Sort Projects</h2>
    <div id="sort-projects-form">
    <select name="category_id" class="filter-projects">
    <option value=''>All Categories</option>
    <?php
    $project_cats = get_terms('project_category', [ 'parent' => 0 ]);
    foreach ($project_cats as $cat)
      echo '<option value="'.$cat->slug.'" '.($_GET['category_name']==$cat->slug ? 'selected' : '').'>'.$cat->name.'</option>';
    ?>
    </select>
      <ul>
      <?php
      $category = !empty($_GET['category_name']) ? $_GET['category_name'] : '';
      $projects = \Firebelly\PostTypes\Project\get_projects(['category' => $category]);
      foreach ($projects as $project_post):
      ?>
        <li class="project" id="post-<?= $project_post->ID ?>">
          <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($project_post->ID)): ?>
            <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
          <?php endif; ?>
          <div class="wrap">
            <h1 class="article-title"><a target="_blank" href="<?= get_edit_post_link($project_post->ID) ?>"><?= $project_post->post_title ?></a></h1>
          </div>
        </li>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php
}

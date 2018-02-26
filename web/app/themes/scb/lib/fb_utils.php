<?php

namespace Firebelly\Utils;

/**
 * Adjust search query posts
 */
// function search_queries( $query ) {
//   if ( !is_admin() && is_search() ) {
//     $query->set( 'posts_per_page', 40 );
//   }
//   return $query;
// }
// add_filter( 'pre_get_posts', __NAMESPACE__ . '\\search_queries' );

/**
 * Custom li'l excerpt function
 */
function get_excerpt( $post, $length=15, $force_content=false ) {
  $excerpt = trim($post->post_excerpt);
  if (!$excerpt || $force_content) {
    $excerpt = $post->post_content;
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = apply_filters( 'the_content', $excerpt );
    $excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
    $excerpt_length = apply_filters( 'excerpt_length', $length );
    $excerpt = wp_trim_words( $excerpt, $excerpt_length );
  }
  return $excerpt;
}

/**
 * Pulls X <li> items from $post->post_content
 */
function get_li_excerpt($post, $length=4) {
  if (!is_object($post)) return;
  $excerpt = '';
  $dom = new \DOMDocument();
  $dom->loadHTML($post->post_content);
  $list_items = $dom->getElementsByTagName('li');
  $i = 0;
  foreach ($list_items as $item) {
    $excerpt .= '<li>' . $item->ownerDocument->saveHtml($item) . '</li>';
    if (++$i >= $length) break;
  }
  return $excerpt;
}

/**
 * Get top ancestor for post
 */
function get_top_ancestor($post){
  if (!$post) return;
  $ancestors = $post->ancestors;
  if ($ancestors) {
    return end($ancestors);
  } else {
    return $post->ID;
  }
}

/**
 * Get top parent cat slug
 */
function get_top_parent_cat($post){
  $return = false;
  $categories = wp_get_post_terms($post->ID, 'project_category');
  if ($categories) {
    $parent_cat = $categories[0];
    $cat_class= $parent_cat->slug;
    $return = $cat_class;
  }
  return $return;
}

/**
 * Get first term for post
 */
function get_first_term($post, $taxonomy='category') {
  $return = false;
  if ($terms = get_the_terms($post->ID, $taxonomy))
    $return = array_pop($terms);
  return $return;
}

/**
 * Get page content from slug
 */
function get_page_content($slug) {
  $return = false;
  if ($page = get_page_by_path($slug))
    $return = apply_filters('the_content', $page->post_content);
  return $return;
}

/**
 * Get post id from slug
 */
function get_id_by_slug($page_slug) {
  $page = get_page_by_path($page_slug);
  if ($page) {
    return $page->ID;
  } else {
    return null;
  }
}

/**
 * Get office for post
 */
function get_office($post) {
  if ($office_id = get_post_meta($post->ID, '_cmb2_related_office', true)) {
    return get_post($office_id);
  } else return false;
}

/**
 * Get category for post
 */
function get_category($post) {
  if ($category = get_the_category($post)) {
    return $category[0];
  } else return false;
}

/**
 * Get num_pages for category given slug + per_page
 */
function get_total_pages($category, $per_page) {
  $cat_info = get_category_by_slug($category);
  $num_pages = ceil($cat_info->count / $per_page);
  return $num_pages;
}

/**
 * Get num_posts for post_type in particular taxonomy
 * @param  [string] $post_type [post,project,etc]
 * @param  [string] $taxonomy  [category,project_category]
 * @param  [integer] $term_id   [ID of term]
 */
function get_num_posts_in_category($post_type, $taxonomy, $term_id) {
  global $wpdb;
  return (int) $wpdb->get_var($wpdb->prepare("
    SELECT COUNT(*) FROM $wpdb->posts
    LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
    LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
    WHERE $wpdb->posts.post_status = 'publish'
    AND $wpdb->posts.post_type = %s
    AND $wpdb->term_taxonomy.taxonomy = %s
    AND $wpdb->term_taxonomy.term_id = %d
    ORDER BY post_date DESC
    ", $post_type, $taxonomy, $term_id));
}

/**
 * Get Related News Post
 */
function get_related_news_post($post_or_focus_area) {
  global $news_post;
  $output = $posts = $focus_area = false;
  if (is_object($post_or_focus_area)) {
    // If post_type is Program see if there's a directly related Post
    if ($post_or_focus_area->post_type == 'program') {
      $posts = get_posts([
        'numberposts' => 1,
        'meta_query' => [
          [
            'key' => '_cmb2_related_program',
            'value' => [$post_or_focus_area->ID],
            'compare' => 'IN'
          ],
        ],
      ]);
    }
    // Can't find one? Get the Focus Area for query below
    if (!$posts)
      $focus_area = get_focus_area($post_or_focus_area);
  } else {
    $focus_area = $post_or_focus_area;
  }

  // Didn't find a blog article directly related above? Find one by Focus Area
  if (!$posts && !empty($focus_area))
    $posts = get_posts('numberposts=1&focus_area='.$focus_area);

  if ($posts) {
    $output = '<div class="related related-news">';
    $output .= '<h2 class="flag">Blog &amp; News</h2>';
    $show_view_all_button = true;
    ob_start();
    foreach ($posts as $news_post)
      include(locate_template('templates/article-news.php'));
    $output .= ob_get_clean();
    $output .= '</div>';
  }
  return $output;
}

/**
 * Get Page Blocks
 */
function get_page_blocks($post) {
  $output = '';
  $page_blocks = get_post_meta($post->ID, '_cmb2_page_blocks', true);
  if ($page_blocks) {
    foreach ($page_blocks as $page_block) {
      if (empty($page_block['hide_block'])) {
        $block_title = $block_body = '';
        if (!empty($page_block['title']))
          $block_title = $page_block['title'];
        if (!empty($page_block['body'])) {
          $block_body = apply_filters('the_content', $page_block['body']);
          $output .= '<div class="page-block">';
          if ($block_title) {
            $output .= '<h2 class="flag">' . $block_title . '</h2>';
          }
          $output .= '<div class="user-content">' . $block_body . '</div>';
          $output .= '</div>';
        }
      }
    }
  }
  return $output;
}

/**
 * Remove p tags around category description
 */
remove_filter('term_description','wpautop');

/**
 * Spit out image slideshow
 */
function image_slideshow($image_slideshow) {
  $output = '';
  if ($image_slideshow) {
    $output .= '<div class="images slider-mini">';
    foreach ((array)$image_slideshow as $attachment_id => $attachment_url) {
      $large = wp_get_attachment_image_src($attachment_id, 'large');
      if ($large) {
        $title = get_post_field('post_excerpt', $attachment_id);
        $output .= '<div class="slide-item"><img src="'.$large[0].'" title="'.$title.'"></div>';
      }
    }
    $output .= '</div>';
  }
  return $output;
}

<?php

namespace Firebelly\Utils;

/**
 * Bump up # search results
 */
function search_queries( $query ) {
  if ( !is_admin() && is_search() ) {
    $query->set( 'posts_per_page', 40 );
  }
  return $query;
}
add_filter( 'pre_get_posts', __NAMESPACE__ . '\\search_queries' );

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
 * Get focus area for post
 */
function get_focus_area($post) {
  if ($focus_areas = get_the_terms($post->ID, 'focus_area')) {
    return $focus_areas[0]->name;
  } else return false;
}

/**
 * Get related Program
 */
function get_program($post) {
  if ($program = get_post_meta($post->ID, '_cmb2_related_program', true)) {
    return get_post($program);
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
 * Get Related Event
 * @param  [Object or String] $post_or_focus_area [$post object or $focus_area slug]
 */
function get_related_event_post($post_or_focus_area) {
  $output = $event = false;
  
  if (is_object($post_or_focus_area)) {
    // If post_type is Program see if there's a directly related Event
    if ($post_or_focus_area->post_type == 'program') {
      $event = \Firebelly\PostTypes\Event\get_events(['num_posts' => 1, 'program' => $post_or_focus_area->ID, 'show_view_all_button' => true]);
    }
    // Can't find one? Get the Focus Area for query below
    if (!$event)
      $focus_area = get_focus_area($post_or_focus_area);
  } else {
    $focus_area = $post_or_focus_area;
  }

  // If we didn't find a directly related event above, try to find one by Focus Area
  if (!$event)
    $event = \Firebelly\PostTypes\Event\get_events(['num_posts' => 1, 'focus_area' => $focus_area, 'show_view_all_button' => true]);
  
  if ($event) {
    $output = '<div class="related related-events">';
    $output .= '<h2 class="flag">Attend an Event</h2>';
    $output .= $event;
    $output .= '</div>';
  }
  return $output;
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

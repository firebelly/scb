<?php

namespace Firebelly\Init;

/**
 * Don't run wpautop before shortcodes are run! wtf Wordpress. from http://stackoverflow.com/a/14685465/1001675
 */
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'wpautop' , 99);
add_filter('the_content', 'shortcode_unautop',100);

/**
 * Various theme defaults
 */
function setup() {
  // Default Image options
  update_option('image_default_align', 'none');
  update_option('image_default_link_type', 'none');
  update_option('image_default_size', 'large');
}
add_action('after_setup_theme', __NAMESPACE__ . '\setup');

/*
 * Tiny MCE options
 */
function mce_buttons_2($buttons) {
  array_unshift($buttons, 'styleselect');
  return $buttons;
}
add_filter('mce_buttons_2', __NAMESPACE__ . '\mce_buttons_2');

function simplify_tinymce($settings) {
  // What goes into the 'formatselect' list
  $settings['block_formats'] = 'Header=h3;Small Caps=h4;Paragraph=p;';

  $settings['inline_styles'] = 'false';
  if (!empty($settings['formats']))
    $settings['formats'] = substr($settings['formats'],0,-1).",underline: { inline: 'u', exact: true} }";
  else
    $settings['formats'] = "{ underline: { inline: 'u', exact: true} }";
  
  // What goes into the toolbars. Add 'wp_adv' to get the Toolbar toggle button back
  $settings['toolbar1'] = 'styleselect,bold,italic,underline,strikethrough,formatselect,bullist,numlist,blockquote,link,unlink,hr,wp_more,outdent,indent,AccordionShortcode,AccordionItemShortcode,fullscreen';
  $settings['toolbar2'] = '';
  $settings['toolbar3'] = '';
  $settings['toolbar4'] = '';

  // $settings['autoresize_min_height'] = 250;
  $settings['autoresize_max_height'] = 1000;

  // Clear most formatting when pasting text directly in the editor
  $settings['paste_as_text'] = 'true';

  $style_formats = array( 
    // array( 
    //   'title' => 'Two Column',
    //   'block' => 'div',
    //   'classes' => 'two-column',
    //   'wrapper' => true,
    // ),  
    // array( 
    //   'title' => 'Three Column',
    //   'block' => 'div',
    //   'classes' => 'three-column',
    //   'wrapper' => true,
    // ),
    array( 
      'title' => 'Button',
      'block' => 'span',
      'classes' => 'button',
    ),
    // array( 
    //   'title' => '» Arrow Link',
    //   'block' => 'span',
    //   'classes' => 'arrow-link',
    // ),
 );  
  $settings['style_formats'] = json_encode($style_formats);

  return $settings;
}
add_filter('tiny_mce_before_init', __NAMESPACE__ . '\simplify_tinymce');

/**
 * Also search postmeta
 */
function search_join($join) {
  global $wpdb;
  if (is_search()) {
    $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
  }
  return $join;
}
add_filter('posts_join', __NAMESPACE__ . '\search_join');

function search_where($where) {
  global $wpdb;
  if (is_search()) {
    $where = preg_replace(
      "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
      "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where);
  }
  return $where;
}
add_filter('posts_where', __NAMESPACE__ . '\search_where');

function search_distinct($where) {
  global $wpdb;
  if (is_search()) {
    return "DISTINCT";
  }
  return $where;
}
add_filter('posts_distinct', __NAMESPACE__ . '\search_distinct');

function search_num( $query ) {
  if ( !is_admin() && is_search() ) {
    $query->set( 'posts_per_page', 250 );
  }
  return $query;
}
add_filter( 'pre_get_posts', __NAMESPACE__ . '\\search_num' );

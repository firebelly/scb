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

/**
 * Custom Site Options page for various fields
 */
function add_site_options() {
  add_options_page('Site Settings', 'Site Settings', 'manage_options', 'functions', __NAMESPACE__ . '\site_options');
}
function site_options() {
?>
    <div class="wrap">
        <h2>Site Options</h2>

        <form method="post" action="options.php">
          <?php wp_nonce_field('update-options') ?>
          <table class="form-table">
              <tr>
                <th scope="row"><label for="twitter_id">Twitter Account:</label></th>
                <td><input type="text" id="twitter_id" name="twitter_id" size="45" value="<?php echo get_option('twitter_id'); ?>" /></td>
              </tr>
              <tr>
                <th scope="row"><label for="facebook_id">Facebook Account:</label></th>
                <td><input type="text" id="facebook_id" name="facebook_id" size="45" value="<?php echo get_option('facebook_id'); ?>" /></td>
              </tr>
              <tr>
                <th scope="row"><label for="linkedin_id">LinkedIn Account:</label></th>
                <td><input type="text" id="linkedin_id" name="linkedin_id" size="45" value="<?php echo get_option('linkedin_id'); ?>" /></td>
              </tr>
              <tr>
                <th scope="row"><label for="contact_email">Contact Email</label></th>
                <td><input type="text" id="contact_email" name="contact_email" size="45" value="<?php echo get_option('contact_email'); ?>" /><br>
              </tr>
          </table>
          <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" /></p>

          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="page_options" value="twitter_id,facebook_id,linkedin_id,contact_email" />
        </form>
    </div>
<?php
}
add_action('admin_menu', __NAMESPACE__ . '\add_site_options');

/**
 * Add link to Site Settings in main admin dropdown
 */
add_action('admin_bar_menu', __NAMESPACE__ . '\add_link_to_admin_bar',999);
function add_link_to_admin_bar($wp_admin_bar) {
  $wp_admin_bar->add_node(array(
    'parent' => 'site-name',
    'id'     => 'site-settings',
    'title'  => 'Site Settings',
    'href'   => esc_url(admin_url('options-general.php?page=functions' ) ),
  ));
}

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
  $settings['block_formats'] = 'H2=h2;H3=h3;Paragraph=p';

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

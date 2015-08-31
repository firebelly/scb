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
                <th scope="row"><label for="linkedin_id">Instagram Account:</label></th>
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

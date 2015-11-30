<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/utils.php',                     // Utility functions
  'lib/init.php',                      // Initial theme setup and constants
  'lib/wrapper.php',                   // Theme wrapper class
  'lib/conditional-tag-check.php',     // ConditionalTagCheck class
  'lib/config.php',                    // Configuration
  'lib/assets.php',                    // Scripts and stylesheets
  'lib/titles.php',                    // Page titles
  'lib/extras.php',                    // Custom functions
];

$firebelly_includes = [
  'lib/disable-comments.php',          // Disables WP comments in admin and frontend
  'lib/fb_init.php',                   // FB theme setups
  'lib/fb_assets.php',                 // FB assets
  'lib/media.php',                     // FB media
  'lib/ajax.php',                      // AJAX functions
  'lib/map.php',                       // Map functions
  'lib/custom-functions.php',          // Rando utility functions and miscellany
  'lib/project-post-type.php',         // Project post type
  'lib/person-post-type.php',          // Person post type
  'lib/office-post-type.php',          // Office post type
  'lib/position-post-type.php',        // Position post type
  'lib/applicant-post-type.php',       // Applicant post type
  'lib/stat-post-type.php',            // Stat post type
  'lib/post-collections.php',          // Post collections
  'lib/project_category-taxonomy.php', // Project Categories
  'lib/person_category-taxonomy.php',  // Person Categories
  // 'lib/taxonomy-meta-boxes.php',       // Extra CMB2 Taxonomy fields
  'lib/cmb2-custom-fields.php',        // Custom CMB2
  'lib/site-options.php',              // Site Options page with CMB2 fields
  'lib/post-meta-boxes.php',           // Various tweaks for multiple post types
];

$sage_includes = array_merge($sage_includes, $firebelly_includes);

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

<?php
/**
 * Template Name: Map
 */

$map_image = \Firebelly\SiteOptions\get_option('projects_map_image');

?>

<div data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>">
  <h1>Active Projects</h1>
  <div id="map">
    <img src="<?= $map_image ?>" alt="A map of the active projects in the US">
  </div>
</div>
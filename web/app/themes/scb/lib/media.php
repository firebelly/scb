<?php
/**
 * Various media functions
 */

namespace Firebelly\Media;

// image size for popout thumbs
add_image_size( 'popout-thumb', 250, 300, ['center', 'top'] );

/**
 * Get thumbnail image for post
 * @param  integer $post_id
 * @return string image URL
 */
function get_post_thumbnail($post_id, $size='medium') {
	$return = false;
	if (has_post_thumbnail($post_id)) {
		$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
		$return = $thumb[0];
	}
	return $return;
}

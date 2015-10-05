<?php
/**
 * Template Name: Collection
 */

if ($collection_id = get_query_var('collection_id')) {
  $collection = \Firebelly\Collections\get_collection($collection_id);
} else {
  $collection = \Firebelly\Collections\get_active_collection();
}
?>

<?= $post->post_content ?>

<?php if ($collection): ?>

  <?php include(locate_template('templates/collection.php')); ?>

<?php else: ?>

  <p>You've reached this page in error.</p>

<?php endif; ?>
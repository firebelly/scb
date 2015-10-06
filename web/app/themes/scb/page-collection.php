<?php
/**
 * Template Name: Collection
 */

if ($collection_id = get_query_var('collection_id')) {
  $collection = \Firebelly\Collections\get_collection($collection_id);
}
?>

<?= $post->post_content ?>

<?php if ($collection): ?>

  <section class="collection" data-id="<?= $collection->ID ?>">
    <?php include(locate_template('templates/collection.php')); ?>
  </section>

<?php else: ?>

  <p>You've reached this page in error.</p>

<?php endif; ?>
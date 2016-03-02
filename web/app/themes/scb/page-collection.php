<?php
/**
 * Template Name: Collection
 */
?>

<section class="collection <?= (empty($collection) || empty($collection->posts)) ? 'empty' : '' ?>" data-id="<?= !empty($collection) ? $collection->ID : '' ?>">
  <?php include(locate_template('templates/collection.php')); ?>
</section>
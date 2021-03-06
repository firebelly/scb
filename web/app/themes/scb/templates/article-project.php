<?php
$orientation = get_post_meta($project_post->ID, '_cmb2_orientation', true);
$location = get_post_meta($project_post->ID, '_cmb2_location', true);
$categories = wp_get_post_terms($project_post->ID, 'project_category');
$project_categories = '';
if ($categories):
  foreach($categories as $cat)
    $project_categories .= '<a href="'.get_term_link($cat).'">'.$cat->name.'</a> <span class="slash">/</span><br />';
endif;
?>
<article class="project grid-item show-post-modal <?= $orientation ?>" data-id="<?= $project_post->ID ?>" data-page-title="<?= $project_post->post_title ?>" data-page-url="<?= get_permalink($project_post) ?>" data-modal-type="project">
  <div class="wrap">
    <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($project_post->ID)): ?>
      <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
    <?php endif; ?>
    <h1 class="article-title"><a href="<?= get_permalink($project_post) ?>"><?= $project_post->post_title ?></a></h1>
    <div class="overlay-content">
      <?php if ($location): ?>
        <h3 class="location"><?= $location ?></h3>
      <?php endif ?>
      <?php if ($project_categories): ?>
        <h3 class="categories"><?= $project_categories ?></h3>
      <?php endif ?>

      <p class="actions">
        <?php if (\Firebelly\Collections\post_in_collection($collection,$project_post->ID)): ?>
          <a href="#" class="collection-action collection-remove" data-action="remove" data-id="<?= $project_post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span><span class="icon icon-remove"><?php include(get_template_directory().'/assets/svgs/icon-remove.svg'); ?></span> <span class="collection-text">Remove from Collection</span></a>
        <?php else: ?>
          <a href="#" class="collection-action collection-add" data-action="add" data-id="<?= $project_post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span><span class="icon icon-remove"><?php include(get_template_directory().'/assets/svgs/icon-remove.svg'); ?></span> <span class="collection-text">Add to Collection</span></a>
        <?php endif; ?>
      </p>
      <div class="big-plus"><a href="<?= get_permalink($project_post) ?>"><span class="sr-only">view project</span></a></div>
      <div class="drag-handle collection-action"><span class="icon icon-drag-handle"><?php include(get_template_directory().'/assets/svgs/icon-drag-handle.svg'); ?></span></div>
    </div>
  </div>
</article>
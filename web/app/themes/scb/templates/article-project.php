<?php 
$location = get_post_meta($project_post->ID, '_cmb2_location', true);
$categories = wp_get_post_terms($project_post->ID, 'project_category');
$project_categories = '';
if ($categories):
  foreach($categories as $cat)
    $project_categories .= '<a href="'.get_term_link($cat).'">'.$cat->name.'</a> <span class="slash">/</span><br />';
endif;
?>
<article class="project grid-item show-post-modal" data-id="<?= $project_post->ID ?>" data-modal-type="project-modal">
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
          <a href="#" class="collection-action collection-remove no-ajaxy" data-action="remove" data-id="<?= $project_post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span><button class="plus-button close"><div class="plus"></div></button> <span class="collection-text">Remove from Collection</span></a>
        <?php else: ?>
          <a href="#" class="collection-action collection-add no-ajaxy" data-action="add" data-id="<?= $project_post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span><button class="plus-button close"><div class="plus"></div></button><span class="collection-text">Add to Collection</span></a>
        <?php endif; ?>
      </p>
      <div class="big-plus"><a href="<?= get_permalink($project_post) ?>" class="show-post-modal" data-id="<?= $project_post->ID; ?>" data-modal-type="project-modal"><span class="sr-only">view project</span></a></div>
    </div>
  </div>
</article>
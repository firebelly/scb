<?php 
$location = get_post_meta($project_post->ID, '_cmb2_location', true);
$categories = wp_get_post_terms($project_post->ID, 'project_category');
$project_categories = '';
if ($categories):
  foreach($categories as $cat)
    $project_categories .= '<a href="'.get_term_link($cat).'">'.$cat->name."</a> /<br />";
endif;
?>
<article class="project" data-id="<?= $project_post->ID ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($project_post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>
  <div class="wrap">
    <h1 class="article-title"><a href="<?= get_permalink($project_post) ?>"><?= $project_post->post_title ?></a></h1>
    <?php if ($location): ?>
      <h3 class="location"><?= $location ?></h3>
    <?php endif ?>
    <?php if ($project_categories): ?>
      <h3 class="categories"><?= $project_categories ?></h3>
    <?php endif ?>

    <p class="actions">
      <?php if (\Firebelly\Collections\post_in_collection($collection,$project_post->ID)): ?>
        <a href="#" class="collection-action" data-action="remove" data-id="<?= $project_post->ID ?>">Remove from Collection</a>
      <?php else: ?>
        <a href="#" class="collection-action" data-action="add" data-id="<?= $project_post->ID ?>">Add to Collection</a>
      <?php endif; ?>
    </p>
  </div>
</article>

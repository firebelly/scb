<?php 
$location = get_post_meta($project_post->ID, '_cmb2_location', true);
$categories = wp_get_post_terms($project_post->ID, 'project_category');
$product_categories = '';
if ($categories):
  foreach($categories as $cat)
    $product_categories .= '<a href="'.get_term_link($cat).'">'.$cat->name."</a> /<br />";
endif;
?>
<article class="project" data-id="<?= $project_post->ID ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($project_post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>
  <div class="wrap">
    <h1 class="article-title"><a href="<?= get_permalink($project_post) ?>"><?= $project_post->post_title ?></a></h1>
    <div class="overlay-content">    
      <?php if ($location): ?>
        <h3 class="location"><?= $location ?></h3>
      <?php endif ?>
      <?php if ($product_categories): ?>
        <h3 class="categories"><?= $product_categories ?></h3>
      <?php endif ?>

      <p class="actions">
        <?php if (\Firebelly\Collections\post_in_collection($collection,$project_post->ID)): ?>
          <a href="#" class="collection-action" data-action="remove" data-id="<?= $project_post->ID ?>">Remove from Collection</a>
        <?php else: ?>
          <a href="#" class="collection-action" data-action="add" data-id="<?= $project_post->ID ?>"><svg class="icon icon-download"><use xlink:href="#icon-download"></svg> Add to Collection</a>
        <?php endif; ?>
      </p>
      <div class="big-plus"><a href="<?= get_permalink($project_post) ?>"><span class="sr-only">view project</span></a></div>
    </div>
  </div>
</article>

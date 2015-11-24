<?php 
$orientation = get_post_meta($post->ID, '_cmb2_orientation', true);
$client = get_post_meta($post->ID, '_cmb2_client', true);
$location = get_post_meta($post->ID, '_cmb2_location', true);
$intro = get_post_meta($post->ID, '_cmb2_intro', true);
$categories = wp_get_post_terms($post->ID, 'project_category');
$project_categories = '';
if ($categories):
  foreach($categories as $cat)
    $project_categories .= '<a href="'.get_term_link($cat).'">'.$cat->name."</a> /<br />";
endif;
?>
<article class="project <?= $orientation ?>" data-id="<?= $post->ID ?>">
  <div class="actions">
    <?php if (\Firebelly\Collections\post_in_collection($collection,$post->ID)): ?>
      <a href="#" class="collection-action" data-action="remove" data-id="<?= $post->ID ?>">Remove from Collection</a>
    <?php else: ?>
      <a href="#" class="collection-action" data-action="add" data-id="<?= $post->ID ?>">Add to Collection</a>
    <?php endif; ?>
  </div>

  <?php if ($orientation == 'vertical') { ?>

    <div class="project-intro grid">
      <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID, 'full')): ?>
        <div class="image-wrap column -left" style="background-image:url(<?= $thumb ?>);"></div>
      <?php endif; ?>
      <div class="column -right">
        <h1 class="article-title"><?= $post->post_title ?></h1>
        <?php if ($intro): ?>
          <p class="project-intro-text">
            <?= $intro ?>          
          </p>
        <?php endif ?>
        <div class="project-meta">
            <div class="grid-item one-half -left">              
              <div class="-inner grid">
                <?php if ($client): ?>
                  <h3>Client</h3>
                  <h4><?= $client ?></h4>
                <?php endif ?>
                <?php if ($location): ?>
                  <h3>Location</h3>
                  <h4><?= $location ?></h4>
                <?php endif ?>
              </div>
            </div>
            <div class="grid-item one-half -right">
              <div class="-inner">
                <?php if ($project_categories): ?>
                  <h3>Category</h3>
                  <h4 class="categories"><?= $project_categories ?></h4>
                <?php endif ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?= \Firebelly\PostTypes\Project\get_project_blocks($post); ?>


  <?php } else { ?>

    <div class="project-intro grid">
      <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID, 'full')): ?>
        <div class="image-wrap" style="background-image:url(<?= $thumb ?>);"></div>
      <?php endif; ?>
      <div class="wrap">
        <div class="project-meta column -left">
          <div class="-inner">
            <?php if ($client): ?>
              <h3>Client</h3>
              <h4><?= $client ?></h4>
            <?php endif ?>
            <?php if ($location): ?>
              <h3>Location</h3>
              <h4><?= $location ?></h4>
            <?php endif ?>
            <?php if ($project_categories): ?>
              <h3>Category</h3>
              <h4 class="categories"><?= $project_categories ?></h4>
            <?php endif ?>
          </div>
        </div>
        <div class="column -right">
          <h1 class="article-title"><?= $post->post_title ?></h1>
          <?php if ($intro): ?>
            <p class="project-intro-text">
              <?= $intro ?>          
            </p>
          <?php endif ?>
        </div>
      </div>
    </div>

    <?= \Firebelly\PostTypes\Project\get_project_blocks($post); ?>

  <?php } ?>

</article>

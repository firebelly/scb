<article class="project show-post-modal" data-id="<?= $project_post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>" data-pageClass="<?= $parent_cat ?>" data-modal-type="project">
  <div class="wrap">
    <h1 class="article-title"><a href="<?= get_permalink($project_post) ?>"><?= $project_post->post_title ?></a></h1>

    <p class="actions">
      <a href="<?= get_permalink($project_post) ?>"><button class="plus-button"><div class="plus"></div></button></a>
      <?php if (\Firebelly\Collections\post_in_collection($collection,$project_post->ID)): ?>
        <a href="#" class="collection-action collection-remove no-ajaxy" data-action="remove" data-id="<?= $project_post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span><span class="icon icon-remove"><?php include(get_template_directory().'/assets/svgs/icon-remove.svg'); ?></span> <span class="collection-text sr-only">Remove from Collection</span></a>
      <?php else: ?>
        <a href="#" class="collection-action collection-add no-ajaxy" data-action="add" data-id="<?= $project_post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span><span class="icon icon-remove"><?php include(get_template_directory().'/assets/svgs/icon-remove.svg'); ?></span> <span class="collection-text sr-only">Add to Collection</span></a>
      <?php endif; ?>
    </p>
  </div>
</article>
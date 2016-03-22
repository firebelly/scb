<article class="person" data-id="<?= $person_post->ID ?>" data-page-title="<?= $person_post->post_title ?>" data-page-url="<?= get_permalink($person_post) ?>" data-modal-type="person">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($person_post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>
  <h1 class="article-title"><a href="<?= get_permalink($person_post) ?>"><?= $person_post->post_title ?></a></h1>

  <p class="actions">
    <?php if (\Firebelly\Collections\post_in_collection($collection,$person_post->ID)): ?>
      <a href="#" class="collection-action" data-action="remove" data-id="<?= $person_post->ID ?>">Remove from Collection</a>
    <?php else: ?>
      <a href="#" class="collection-action" data-action="add" data-id="<?= $person_post->ID ?>">Add to Collection</a>
    <?php endif; ?>
  </p>
</article>
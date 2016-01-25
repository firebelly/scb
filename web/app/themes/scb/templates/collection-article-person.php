<article class="person" data-id="<?= $person_post->ID ?>">
  <h1 class="article-title"><a href="<?= get_permalink($person_post) ?>"><?= $person_post->post_title ?></a></h1>

  <p class="actions">
    <a href="#" class="collection-action" data-action="remove" data-id="<?= $person_post->ID ?>"><span class="sr-only">Remove from Collection</span> <button class="plus-button close"><div class="plus"></div></button></a>
  </p>
</article>
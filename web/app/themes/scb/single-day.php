<article class="day single" data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>" data-modal-type="day">

  <div class="actions">
    <a href="/careers/" class="plus-button close single-close"><div class="plus"></div></a>
  </div>

  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <div class="article-body">
    <header class="article-header">
      <h1 class="article-title"><?= $post->post_title ?></h1>
    </header>
    <div class="entry-content">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>
  </div>
</article>

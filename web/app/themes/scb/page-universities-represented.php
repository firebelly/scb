<article class="single single-page" data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>" data-modal-type="page">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <div class="actions">
    <a href="/people/" class="plus-button close single-close"><div class="plus"></div></a>
  </div>

  <header class="article-header">
    <h1 class="article-title"><?= !empty($display_title) ? $display_title : $post->post_title ?></h1>
  </header>

  <div class="article-body">

    <div class="content user-content">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>

  </div>

</article>

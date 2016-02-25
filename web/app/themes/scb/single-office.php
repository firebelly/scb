<?php 
$intro = get_post_meta($post->ID, '_cmb2_intro', true);
$address = get_post_meta($post->ID, '_cmb2_address', true);
?>
<article class="single single-office" data-id="<?= $post->ID ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <header class="article-header">    
    <h1 class="article-title"><?= !empty($display_title) ? $display_title : $post->post_title ?></h1>
    <?php if (!empty($intro)): ?>
      <h2><?= $intro ?></h2>
    <?php endif; ?>
  </header>
  
  <div class="article-body -two-column">

    <div class="info -left">
      <div class="info-section">
        <h3>Location</h3>
        <p>
        <?= $address['address-1'] ?>
        <?php if (!empty($address['address-2'])): ?>
          <br><?= $address['address-2'] ?>
        <?php endif; ?>
        <br><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?>
        </p>

      </div>
    </div>

    <div class="content user-content -right">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>  

  </div>

</article>

<?php 
$contact = get_post_meta($post->ID, '_cmb2_contact', true);
$display_title = get_post_meta($post->ID, '_cmb2_display_title', true);
$subtitle = get_post_meta($post->ID, '_cmb2_subtitle', true);
$education = get_post_meta($post->ID, '_cmb2_education', true);
$office = \Firebelly\Utils\get_office($post);
?>
<article class="person single" data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <header class="article-header">    
    <h1 class="article-title"><?= !empty($display_title) ? $display_title : $post->post_title ?></h1>
    <?php if (!empty($subtitle)): ?>
      <h3><?= $subtitle ?></h3>
    <?php endif; ?>
    <p class="actions">
      <?php if (\Firebelly\Collections\post_in_collection($collection,$post->ID)): ?>
        <a href="#" class="collection-action" data-action="remove" data-id="<?= $post->ID ?>"><span class="icon icon-remove"><?php include(get_template_directory().'/assets/svgs/icon-remove.svg'); ?></span> <span class="sr-only">Add to Collection</span></a>
      <?php else: ?>
        <a href="#" class="collection-action" data-action="add" data-id="<?= $post->ID ?>"><span class="icon icon-download"><?php include(get_template_directory().'/assets/svgs/icon-download.svg'); ?></span> <span class="sr-only">Add to Collection</span></a>
      <?php endif; ?>
    </p>
  </header>
  
  <div class="article-body -two-column">

    <div class="info -left">
      <?php if ($office): ?>
        <div class="info-section">
          <h3>Office</h3>
          <p><?= $office->post_title ?></p>
        </div>
      <?php endif; ?>

      <?php if ($contact): ?>
        <div class="info-section">
          <h3 class="contact">Contact</h3>
          <?= apply_filters('the_content', $contact) ?>
        </div>
      <?php endif ?>
      
      <?php if ($education): ?>
        <div class="info-section education">
          <h3 class="education">Education</h3>
          <?= apply_filters('the_content', $education) ?>
        </div>
      <?php endif ?>
    </div>

    <div class="content user-content -right">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>  

  </div>

</article>

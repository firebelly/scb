<?php 
$contact = get_post_meta($post->ID, '_cmb2_contact', true);
$subtitle = get_post_meta($post->ID, '_cmb2_subtitle', true);
$education = get_post_meta($post->ID, '_cmb2_education', true);
$office = \Firebelly\Utils\get_office($post);
$categories = wp_get_post_terms($post->ID, 'people_category');
// $people_categories = '';
// if ($categories):
//   foreach($categories as $cat)
//     $people_categories .= '<a href="'.get_term_link($cat).'">'.$cat->name."</a> /<br />";
// endif;
?>
<article class="person" data-id="<?= $post->ID ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>
  
  <h1 class="article-title"><a href="<?= get_permalink($post) ?>"><?= $post->post_title ?></a></h1>
  <?php if ($subtitle): ?>
    <h3><?= $subtitle ?></h3>
  <?php endif; ?>

  <div class="info">
    <?php if ($office): ?>
      <h3>Office</h3>
      <p><?= $office->post_title ?></p>
    <?php endif; ?>

    <?php if ($contact): ?>
      <h3 class="contact">Contact</h3>h3>
      <?= apply_filters('the_content', $contact) ?>
    <?php endif ?>
    
    <?php if ($education): ?>
      <h3 class="education">Education</h3>
      <?= apply_filters('the_content', $education) ?>
    <?php endif ?>
  </div>

  <div class="content">
    <?= apply_filters('the_content', $post->post_content) ?>
  </div>
</article>

<?php 
$office = \Firebelly\Utils\get_office($post);
$open_for_applications = get_post_meta($post->ID, '_cmb2_open_for_applications', true);
$discipline = \Firebelly\Utils\get_first_term($post, 'project_category');
?>
<article class="person" data-id="<?= $post->ID ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>
  
  <h1 class="article-title"><a href="<?= get_permalink($post) ?>"><?= $post->post_title ?></a></h1>

  <div class="info">
    <?php if ($office): ?>
      <h3>Office</h3>
      <p><?= $office->post_title ?></p>
    <?php endif; ?>

    <?php if ($discipline): ?>
      <h3 class="discipline">Discipline</h3>
      <a href="<?= get_term_link($discipline) ?>"><?= $discipline->name ?></a>
    <?php endif ?>

    <?php if ($open_for_applications): ?>
      <h3>Apply Now</h3>
      <?php 
      $position_id = $post->ID; // Associate Application with this Position when submitted
      include(locate_template('templates/application-form.php')); 
      ?>
    <?php endif ?>
  </div>

  <div class="content">
    <?= apply_filters('the_content', $post->post_content) ?>
  </div>
</article>

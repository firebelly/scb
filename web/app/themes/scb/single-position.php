<?php 
$office = \Firebelly\Utils\get_office($post);
$open_for_applications = get_post_meta($post->ID, '_cmb2_open_for_applications', true);
$discipline = \Firebelly\Utils\get_first_term($post, 'project_category');
?>
<div class="feedback-container"><div class="feedback"><p></p></div></div></div>
<article class="position single" data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>
  
  <h1 class="article-title"><?= $post->post_title ?></h1>

  <div class="article-body -two-column">

    <div class="info -left">
      <?php if ($office): ?>
        <div class="info-section">
          <h3>Office</h3>
          <p><?= $office->post_title ?></p>
        </div>
      <?php endif; ?>

      <?php if ($discipline): ?>
        <div class="info-section">
          <h3 class="discipline">Discipline</h3>
          <a href="<?= get_term_link($discipline) ?>"><?= $discipline->name ?></a>
        </div>
      <?php endif ?>

      <?php if ($open_for_applications): ?>
        <div class="info-section">
          <h3>Apply Now</h3>
          <?php 
          $position_id = $post->ID; // Associate Application with this Position when submitted
          include(locate_template('templates/application-form.php')); 
          ?>
        </div>
      <?php endif ?>
    </div>

    <div class="content user-content -right">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>

  </div>

</article>

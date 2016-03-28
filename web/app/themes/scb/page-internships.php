<?php
/**
 * Template Name: Internships
 */

$intro_content = get_post_meta($post->ID, '_cmb2_intro_content', true);

$chicago_id = url_to_postid('/office/chicago');
$san_francisco_id = url_to_postid('/office/san-francisco');

?>
<article class="internships single single-office" data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>" data-modal-type="office">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <header class="article-header">
    <h1 class="article-title"><?= !empty($display_title) ? $display_title : $post->post_title ?></h1>
    <?php if (!empty($intro_content)) {
      echo $intro_content;
    } ?>
  </header>

  <div class="article-body -two-column">

    <div class="info -left">
      <div class="info-section locations">
        <h3>Locations</h3>
        <p><a href="/office/chicago" class="show-post-modal" data-id="<?= $chicago_id ?>" data-modal-type="office">Chicago</a></p>
        <p><a href="/office/san-francisco" class="show-post-modal" data-id="<?= $san_francisco_id ?>" data-modal-type="office">San Francisco</a></p>
      </div>

      <div class="info-section duration">
        <h3>Duration</h3>
        <p>Summer Term</p>
        <p>Full Academic Year</p>
      </div>

      <div class="info-section application">
        <h3>Apply Now</h3>
        <?php
        $position_id = $post->ID; // Associate Application with this Position when submitted
        $application_type = 'internship';
        include(locate_template('templates/application-form.php'));
        ?>
      </div>
    </div>

    <div class="content user-content -right">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>

  </div>

</article>

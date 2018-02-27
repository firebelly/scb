<?php
/**
 * Template Name: People
 */

$person_categories = get_terms('person_category');
$associates_category = get_term_by('slug', 'associates', 'person_category');
$associate_principals_category = get_term_by('slug', 'associate-principals', 'person_category');
$slideshow_images = get_post_meta($post->ID, '_cmb2_slideshow_images', true);
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
?>

<div class="grid">

  <section class="page-content grid-item one-half">
    <?= apply_filters('the_content', $post->post_content) ?>

    <?php if (!empty($slideshow_images)): ?>
      <?= \Firebelly\Utils\image_slideshow($slideshow_images); ?>
    <?php endif; ?>

    <?= !empty($secondary_content) ? apply_filters('the_content', $secondary_content) : '' ?>

    <div class="stat">
      <div class="stat-number"><?= \Firebelly\SiteOptions\get_option('num_universities_represented'); ?></div>
      <div class="stat-label"><a href="/people/universities-represented/" class="show-post-modal">Universities Represented</a></div>
    </div>
  </section>

  <section class="people-section grid-item one-half">
    <div class="text-grid">
      <div class="column one-third"><div class="-inner">
      <?php
      foreach($person_categories as $person_category) {
        if (!preg_match('/(associates|associate-principals)/',$person_category->slug)) {
          echo Firebelly\PostTypes\Person\get_people_category($person_category);
        }
      }
      ?>
      </div></div>
      <div class="column one-third"><div class="-inner">
        <?= Firebelly\PostTypes\Person\get_people_category($associate_principals_category); ?>
      </div></div>
      <div class="column one-third"><div class="-inner">
        <?= Firebelly\PostTypes\Person\get_people_category($associates_category); ?>
      </div></div>
    </div>
  </section>

</div>


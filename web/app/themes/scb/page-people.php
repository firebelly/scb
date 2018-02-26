<?php
/**
 * Template Name: People
 */

$person_categories = get_terms('person_category');
$associates_category = get_term_by('slug', 'associates', 'person_category');
$associate_principles_category = get_term_by('slug', 'associate-principals', 'person_category');
?>

<div class="grid">

  <section class="page-content grid-item one-half">
    <?= apply_filters('the_content', $post->post_content) ?>
    <div class="stat">
      <div class="stat-number"><?= \Firebelly\SiteOptions\get_option('num_universities_represented'); ?></div>
      <div class="stat-label">Universities Represented</div>
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
      <?= Firebelly\PostTypes\Person\get_people_category($associates_category); ?>
      </div></div>
      <div class="column one-third"><div class="-inner">
      <?= Firebelly\PostTypes\Person\get_people_category($associate_principles_category); ?>
      </div></div>
    </div>
  </section>

</div>


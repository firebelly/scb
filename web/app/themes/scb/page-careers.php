<?php
/**
 * Template Name: Careers
 */

$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
$project_images = get_post_meta($post->ID, '_cmb2_careers_images', true);
if ($project_images) {
  $i = 0;
  foreach ($project_images as $image_id => $image_src) {
    $image = wp_get_attachment_image_src($image_id, 'large');
    $project_images[$i] = $image[0];
    $i++;
  }
}
$middle_column_1 = apply_filters('the_content', get_post_meta($post->ID, '_cmb2_middle_column_1', true));
$middle_column_2 = apply_filters('the_content', get_post_meta($post->ID, '_cmb2_middle_column_2', true));
$middle_column_3 = apply_filters('the_content', get_post_meta($post->ID, '_cmb2_middle_column_3', true));
$terms_left = apply_filters('the_content', get_post_meta($post->ID, '_cmb2_terms_left', true));
$terms_right = apply_filters('the_content', get_post_meta($post->ID, '_cmb2_terms_right', true));

$chicago_id = url_to_postid('/office/chicago/');
$san_francisco_id = url_to_postid('/office/san-francisco/');
$internships_id = url_to_postid('/careers/internships/');

$num_offices = \Firebelly\PostTypes\Office\get_num_offices();
$num_design_professionals = \Firebelly\SiteOptions\get_option('num_design_professionals');
$num_internships = \Firebelly\SiteOptions\get_option('num_internships');
?>

<div class="grid wrap -top">
  <div class="page-intro grid-item one-half -left">
    <?= $post->post_content ?>
    <p class="careers-actions"><a href="/careers/submit-portfolio/" rel="nofollow" class="button submit-portfolio">Submit your portfolio</a> <span class="slash">/</span> <a href="#positions" class="view-positions smoothscroll">View open positions</a></p>
  </div>

  <div class="page-intro grid-item one-half -right">
    <?= apply_filters('the_content', $secondary_content); ?>
  </div>
</div>

<div class="grid wrap middle-section">
  <div class="grid-item -left">
    <?php if (!empty($project_images[0])): ?>
      <img src="<?= $project_images[0] ?>" alt="Careers at SCB">
    <?php endif; ?>
    <div class="text-grid">
      <div class="one-third">
        <div class="-inner">
          <?= $middle_column_1 ?>
        </div>
      </div>
      <div class="one-third">
        <div class="-inner">
          <?= $middle_column_2 ?>
        </div>
      </div>
      <div class="one-third">
        <div class="-inner">
          <?= $middle_column_3 ?>
        </div>
      </div>
    </div>
  </div>

  <div class="grid-item -right">
    <?php if (!empty($project_images[1])): ?>
      <img src="<?= $project_images[1] ?>" alt="Careers at SCB">
    <?php endif; ?>
    <div class="stats">
      <div class="stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_offices ?></p>
          <p class="stat-label">Offices</p>
          <p class="stat-link"><a href="/office/chicago/" class="show-post-modal" data-id="<?= $chicago_id ?>" data-url="<?= get_permalink($chicago_id) ?>" data-modal-type="office">Chicago</a> / <a href="/office/san-francisco/" class="show-post-modal" data-id="<?= $san_francisco_id ?>"  data-url="<?= get_permalink($chicago_id) ?>" data-modal-type="office">San Francisco</a></p>
        </div>
      </div>
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_design_professionals ?></p>
          <p class="stat-label">Design Professionals</p>
          <p class="stat-link"><a href="/people/">Meet our people</a></p>
        </div>
      </div>
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_internships ?></p>
          <p class="stat-label">Interns This Year</p>
          <p class="stat-link"><a href="/careers/internships/" class="show-post-modal" data-id="<?= $internships_id ?>" data-url="<?= get_permalink($internships_id) ?>" data-modal-type="position">Intern Program</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid wrap bottom-section">
  <div class="-top grid">
    <div class="image-wrap image-left">
      <?php if (!empty($project_images[2])): ?>
        <div class="image" style="background-image: url('<?= $project_images[2] ?>');"></div>
      <?php endif; ?>
    </div>
    <div class="image-wrap image-right">
      <?php if (!empty($project_images[3])): ?>
        <div class="image" style="background-image: url('<?= $project_images[3] ?>');"></div>
      <?php endif; ?>
    </div>
  </div>
  <div class="-bottom grid">
    <div class="positions -left" id="positions">

      <?php
      $offices = Firebelly\PostTypes\Office\get_offices();
      foreach($offices as $office) {
        echo '<div class="positions-list" id="'.$office->slug.'-positions"><h2>'.$office->post_title.'</h2>';
        if ($positions = Firebelly\PostTypes\Position\get_positions()) {
          echo '<ul>';
          foreach($positions as $position) {
            $related_office = Firebelly\Utils\get_office($position);
            if ($related_office == $office) {
              if (!empty($position->post_content))
                echo '<li><h3><a href="'.get_permalink($position).'" data-page-title="'.$position->post_title.'" data-url="'.get_permalink($position).'" data-id="'.$position->ID.'" data-modal-type="position" class="show-post-modal">'.$position->post_title.'</a></h3>
              <a href="'.get_permalink($position).'" class="show-post-modal read-more-link" data-modal-type="position" data-page-title="'.$position->post_title.'" data-url="'.get_permalink($position).'" data-id="'.$position->ID.'"><button class="plus-button"><div class="plus"></div></button> <span class="sr-only">Continued</span></a></li>';
              else
                echo '<li>'.$position->post_title.'</li>';
            }
          }
          echo '</ul></div>';
        }
      }
      ?>
    </div>
    <div class="positions-image -right">
    <?php if (!empty($project_images[4])): ?>
      <img src="<?= $project_images[4] ?>" alt="Open positions at SCB">
    <?php endif; ?>
    </div>
  </div>
  <div class="terms grid">
    <div class="term -left">
      <div class="-inner">
        <?= $terms_left ?>
      </div>
    </div>
    <div class="term -right">
      <div class="-inner">
        <?= $terms_right ?>
      </div>
    </div>
  </div>
</div>
<?php
/**
 * Template Name: Careers
 */

$slideshow_images = get_post_meta($post->ID, '_cmb2_slideshow_images', true);
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

$num_offices = \Firebelly\PostTypes\Office\get_num_offices();
$num_design_professionals = \Firebelly\SiteOptions\get_option('num_design_professionals');
$num_internships = \Firebelly\SiteOptions\get_option('num_internships');
?>

<div class="grid wrap -top">
  <div class="page-intro grid-item one-half -left">
    <?= $post->post_content ?>
    <p class="careers-actions"><a href="#positions" class="button view-positions smoothscroll">View open positions</a></p>
  </div>

  <div class="grid-item one-half -right">
    <div class="stats">
      <div class="stat long-stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_offices ?></p>
          <p class="stat-label">Offices</p>
          <?php
          $offices = Firebelly\PostTypes\Office\get_offices();
          $officeLinks = [];
          foreach($offices as $office) {
            $officeLinks[] = sprintf('<a href="/office/%s" class="show-post-modal">%s</a>', $office->post_name, $office->post_title);
          }
          ?>
          <p class="stat-link"><?= implode('<br>', $officeLinks) ?></p>
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
          <p class="stat-link"><a href="/careers/internships/" class="show-post-modal">Intern Program</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid wrap middle-section">
  <div class="grid-item -left">
    <?php if (!empty($slideshow_images)): ?>
      <?= \Firebelly\Utils\image_slideshow($slideshow_images); ?>
    <?php elseif (!empty($project_images[0])): ?>
      <img src="<?= $project_images[0] ?>" alt="Careers at SCB">
    <?php endif; ?>
    <div class="text-grid">
      <div class="column one-third">
        <div class="-inner">
          <?= $middle_column_1 ?>
        </div>
      </div>
      <div class="column one-third">
        <div class="-inner">
          <?= $middle_column_2 ?>
        </div>
      </div>
      <div class="column one-third">
        <div class="-inner">
          <?= $middle_column_3 ?>
        </div>
      </div>
    </div>
  </div>

  <div class="grid-item -right">
    <?php
    echo '<div class="positions positions-list days"><h2>We are SCB</h2>';
    if ($days = Firebelly\PostTypes\Day\get_days()) {
      echo '<ul>';
      foreach($days as $day) {
        echo '<li><h3><a href="'.get_permalink($day).'" class="show-post-modal">'.$day->post_title.'</a></h3>
        <a href="'.get_permalink($day).'" class="show-post-modal read-more-link"><button class="plus-button"><div class="plus"></div></button> <span class="sr-only">Continued</span></a></li>';
      }
      echo '</ul></div>';
    }
    ?>
  </div>
</div>

<div class="grid wrap bottom-section">
  <div class="-top grid">
    <div class="image-wrap image-left">
      <?php if (!empty($project_images[1])): ?>
        <div class="image" style="background-image: url('<?= $project_images[1] ?>');"></div>
      <?php endif; ?>
    </div>
    <div class="image-wrap image-right">
      <?php if (!empty($project_images[2])): ?>
        <div class="image" style="background-image: url('<?= $project_images[2] ?>');"></div>
      <?php endif; ?>
    </div>
  </div>
  <div class="-bottom grid">
    <div class="positions" id="positions">

      <div class="text-grid">
        <?php
        $offices = Firebelly\PostTypes\Office\get_offices();
        foreach($offices as $office) {
          echo '<div class="column one-fourth positions-list" id="'.$office->post_name.'-positions"><div class="-inner"><h2><a href="' . get_permalink($office) . '" class="show-post-modal">'.$office->post_title.'</a></h2>';
          if ($positions = Firebelly\PostTypes\Position\get_positions(['office' => $office->ID])) {
            echo '<ul>';
            foreach($positions as $position) {
              if (!empty($position->post_content))
                echo '<li><h3><a href="'.get_permalink($position).'" class="show-post-modal">'.$position->post_title.'</a></h3>
              <a href="'.get_permalink($position).'" class="show-post-modal read-more-link"><button class="plus-button"><div class="plus"></div></button> <span class="sr-only">Continued</span></a></li>';
              else
                echo '<li>'.$position->post_title.'</li>';
            }
            echo '</ul>';
          } else {
            echo '<p>No current positions</p>';
          }
          echo '</div></div>';
        }
        // Empty columns if < 4 offices
        for ($i=0; $i < 4 - count($offices); $i++) {
          echo '<div class="column one-fourth">&nbsp;</div>';
        }
        ?>
      </div>

      <div class="submit-portfolio-link">
        <p>Don’t see what you’re looking for listed here? We’re always looking for creative, dynamic individuals to join our team. <a href="/careers/submit-portfolio/" rel="nofollow" class="submit-portfolio nobreak">Submit your portfolio</a></p>
      </div>

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
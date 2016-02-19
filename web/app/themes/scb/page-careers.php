<?php
/**
 * Template Name: Careers
 */

use Firebelly\Utils;
$num_people = \Firebelly\PostTypes\Person\get_num_people();
$num_offices = \Firebelly\PostTypes\Office\get_num_offices();
$secondary_content = get_post_meta($post->ID, '_cmb2_secondary_content', true);
$project_images[] = get_post_meta($post->ID, '_cmb2_careers_images', true);
?>

<div class="grid wrap -top">
  <div class="page-intro grid-item one-half -left">
    <?= $post->post_content ?>
    <p><a href="#" class="button">Submit your portfolio</a> / <a href="#">View open positions</a></p>
  </div>

  <div class="page-intro grid-item one-half -right">
    <?= apply_filters('the_content', $secondary_content); ?>
  </div>
</div>

<div class="grid wrap middle-section">
  <div class="grid-item -left">
    <img src="<?= get_bloginfo('template_directory'); ?>/assets/images/careers-1.jpg" alt="Careers at SCB">
    <div class="text-grid">
      <div class="one-third">
        <div class="-inner">
          <h3>The opportunity</h3>
          <p>To support the diversity of our work and the strength of our design, we strive to cultivate a culture of collaboration and creativity, where team members at every level are empowered to contribute to our mission.</p>
          <h3>Culture</h3>
          <p>We encourage our team to seek opportunities beyond our office walls to engage with the architecture and design community, expand networks, and find design inspiration. At the same time, we regularly engage in office-wide pinups and design critiques, support professional development of all staff members, and hold R&D discussion groups across multiple market sectors to facilitate knowledge-sharing and collaboration.</p>
        </div>
      </div>
      <div class="one-third">
        <div class="-inner">
          <h3>Work</h3>
          <p>Our offices have their own regional personalities, but they are without exception challenging and supportive environments where motivated individuals can be autonomous, take ownership over their work, and make an impact on real projects being developed for real clients in real communities across the country.</p>
          <h3>People</h3>
          <p>Our people come from a range of backgrounds, and we value the varied perspectives, experience, and expertise they bring to their work. From architects, master planners, and interior designers to finance specialists, legal experts, marketers, human resource professionals, and more, we are all deeply committed to SCB’s vision of creating elegant design solutions that support our clients’ needs and make a positive impact on the communities we work in.</p>
        </div>
      </div>
      <div class="one-third">
        <div class="-inner">
          <h3>Work Environment</h3>
          <p>We know firsthand that the way people work is changing. We continually evolve and look for ways to make the work environment comfortable while cultivating a stimulating atmosphere that promotes design and creativity.</p>
          <p>In our San Francisco office you can join our weekly coffee club, where you might find yourself sitting with your colleagues around our central farmhouse table, having breakfast while discussing SCB’s impact on the San Francisco skyline or citywide efforts to address affordable housing. In our Chicago headquarters, you might gather over lunch in the canteen to participate in a discussion about SCB’s growing influence in the world of higher education, or partake in a group study session for your next ARE.</p>
          <p>We value our people. We’re committed to personal and professional growth, and offer a range of resources to support career development, including mentoring, professional association memberships, and licensing examination reimbursement.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="grid-item -right">
    <img src="<?= get_bloginfo('template_directory'); ?>/assets/images/careers-2.jpg" alt="Careers at SCB">
    <div class="stats">
      <div class="stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_offices ?></p>
          <p class="stat-label">Offices</p>
          <p class="stat-link"><a href="#">Chicago</a> / <a href="#">San Francisco</a></p>
        </div>
      </div>
      <div class="stat">
        <div class="wrap">
          <p class="stat-number"><?= $num_people ?></p>
          <p class="stat-label">Design Professionals</p>
          <p class="stat-link"><a href="#">View on map</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
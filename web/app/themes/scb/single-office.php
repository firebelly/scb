<?php
$intro = get_post_meta($post->ID, '_cmb2_intro', true);
$address = get_post_meta($post->ID, '_cmb2_address', true);
$phone = get_post_meta($post->ID, '_cmb2_phone', true);
$fax = get_post_meta($post->ID, '_cmb2_fax', true);
$submit_portfolio_call = get_post_meta($post->ID, '_cmb2_submit_portfolio_call', true);
$extra_info = get_post_meta($post->ID, '_cmb2_extra_info', true);
?>
<article class="single single-office" data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>" data-modal-type="office">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <div class="actions">
    <a href="/about/" class="plus-button close single-close"><div class="plus"></div></a>
  </div>

  <header class="article-header">
    <h1 class="article-title"><?= !empty($display_title) ? $display_title : $post->post_title ?></h1>
    <?php if (!empty($intro)): ?>
      <h2><?= $intro ?></h2>
    <?php endif; ?>
  </header>

  <div class="article-body -two-column">

    <div class="info -left">
      <div class="info-section location">
        <h3>Location</h3>
        <p>
        <?= $address['address-1'] ?>
        <?php if (!empty($address['address-2'])): ?>
          <br><?= $address['address-2'] ?>
        <?php endif; ?>
        <br><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?>
        <?= $phone ? '<br>T: '.$phone : '' ?>
        <?= $fax ? '<br>F: '.$fax : '' ?>
        </p>
      </div>

      <?php if ($extra_info): ?>
      <div class="info-section office-details">
        <?= apply_filters('the_content', $extra_info); ?>
      </div>
      <?php endif; ?>

      <?php
        $office = $post->post_name;
        $positions = Firebelly\PostTypes\Position\get_positions(['office' => $office]);
        if (!empty($positions)) {
          echo '<div class="info-section positions"><h3>Open Positions</h3><ul>';
          foreach($positions as $position) {
            if (!empty($position->post_content))
              echo '<li><h3><a href="'.get_permalink($position).'" class="show-post-modal">'.$position->post_title.'</a></h3>
            <a href="'.get_permalink($position).'" class="show-post-modal read-more-link"><button class="plus-button"><div class="plus"></div></button> <span class="sr-only">Continued</span></a></li>';
            else
              echo '<li>'.$position->post_title.'</li>';
          }
          echo '</ul></div>';
        }
      ?>

      <?php if ($submit_portfolio_call) { ?>
        <div class="info-section portfolio-submission">
          <p><?= $submit_portfolio_call ?></p>
          <a href="#" class="button submit-portfolio">Submit your portfolio</a>
        </div>
      <?php } ?>
    </div>

    <div class="content user-content -right">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>

  </div>

</article>

<footer class="site-footer content-info" role="contentinfo">
  <div class="wrap grid">
    <div class="container">
      <?php dynamic_sidebar('sidebar-footer'); ?>
    </div>

    <div class="grid-item one-half -left">
      <div class="container">
        <p class="email-link"><a href="mailto:<?= \Firebelly\SiteOptions\get_option( 'contact_email', 'hello@scb.com' ); ?>" class="highlight-hover"><?= \Firebelly\SiteOptions\get_option( 'contact_email', 'hello@scb.com' ); ?></a></p>

        <ul class="social">
          <li><a target="_blank" href="https://www.facebook.com/<?php echo \Firebelly\SiteOptions\get_option( 'facebook_id', 'SolomonCordwellBuenz' ); ?>">Facebook</a></li>
          <li><a target="_blank" href="https://twitter.com/<?php echo \Firebelly\SiteOptions\get_option( 'twitter_id', 'SoloCordBuenz' ); ?>">Twitter</a></li>
          <li><a target="_blank" href="https://instagram.com/<?php echo \Firebelly\SiteOptions\get_option( 'instagram_id', 'solomoncordwellbuenz' ); ?>">Instagram</a></li>
          <li><a target="_blank" href="http://linkedin.com/company/<?php echo \Firebelly\SiteOptions\get_option( 'linkedin_id', 'solomon-cordwell-buenz' ); ?>">LinkedIn</a></li>
        </ul>
      </div>
    </div>

    <?php
    $offices = Firebelly\PostTypes\Office\get_offices();
    $officeBlocks = [];
    foreach($offices as $office):
      $address = get_post_meta($office->ID, '_cmb2_address', true);
      $phone = get_post_meta($office->ID, '_cmb2_phone', true);
      $fax = get_post_meta($office->ID, '_cmb2_fax', true);
      $officeBlock = '';
      ob_start();
      ?>
      <div class="footer-office">
        <h3><a href="<?= get_permalink($office) ?>" class="show-post-modal"><?= $office->post_title ?></a></h3>
        <ul>
          <li class="address">
            <address class="vcard">
              <span class="street-address"><?= $address['address-1'] ?><?= !empty($address['address-2']) ? '<span>'.$address['address-2'].'</span>' : '' ?></span>
              <span class="locality"><?= $address['city'] ?>, <?= $address['state'] ?></span>
              <span class="postal-code"><?= $address['zip'] ?> USA</span>
            </address>
          </li>
          <li class="contact">
            <?php if (!empty($phone)): ?>
              <span class="tel"><b>T</b> <?= $phone ?></span>
            <?php endif; ?>
            <?php if (!empty($fax)): ?>
              <span class="tel"><b>F</b> <?= $fax ?></span>
            <?php endif; ?>
          </li>
        </ul>
      </div>
      <?php $officeBlocks[] = ob_get_clean(); ?>

    <?php endforeach; ?>

    <div class="grid grid-item one-half -right">
      <div class="contact-group" id="contact">
        <?= $officeBlocks[0] ?>
        <?= !empty($officeBlocks[2]) ? $officeBlocks[2] : '' ?>
      </div>

      <div class="contact-group">
        <?= $officeBlocks[1] ?>
        <?= !empty($officeBlocks[3]) ? $officeBlocks[3] : '' ?>
      </div>

      <div class="contact-group">
        <h3>Media</h3>
        <ul>
          <li class="contact">
            <?= \Firebelly\SiteOptions\get_option( 'media_contact', '' ); ?>
          </li>
        </ul>
      </div>
    </div>

  </div><!-- /.wrap -->

</footer>

<?php
  global $collection;
  if (!isset($collection)) {
    $collection = \Firebelly\Collections\get_active_collection();
  }
?>

<div class="global-modal modal"><button class="plus-button close hide-modal"><div class="plus"></div></button><div class="modal-content"></div></div>
<section class="collection mini modal <?= (empty($collection) || empty($collection->posts)) ? 'empty' : '' ?>" data-id="<?= !empty($collection) ? $collection->ID : '' ?>">
  <?php include(locate_template('templates/collection.php')); ?>
</section>
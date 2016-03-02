<?php 
$intro = get_post_meta($post->ID, '_cmb2_intro', true);
$address = get_post_meta($post->ID, '_cmb2_address', true);
?>
<article class="single single-office" data-id="<?= $post->ID ?>">
  <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
    <div class="image-wrap" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></div>
  <?php endif; ?>

  <header class="article-header">    
    <h1 class="article-title"><?= !empty($display_title) ? $display_title : $post->post_title ?></h1>
    <?php if (!empty($intro)): ?>
      <h2><?= $intro ?></h2>
    <?php endif; ?>
  </header>
  
  <div class="article-body -two-column">

    <div class="info -left">
      <div class="info-section">
        <h3>Location</h3>
        <p>
        <?= $address['address-1'] ?>
        <?php if (!empty($address['address-2'])): ?>
          <br><?= $address['address-2'] ?>
        <?php endif; ?>
        <br><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?>
        </p>
      </div>
      
      <?php
        $office = $post->post_name;
        if ($positions = Firebelly\PostTypes\Position\get_positions()) {
          echo '<div class="info-section positions"><h3>Open Positions</h3><ul>';
          foreach($positions as $position) {
            $related_office = Firebelly\Utils\get_office($position);
            if ($related_office->post_name == $office) {
              if (!empty($position->post_content))
                echo '<li><h3><a href="'.get_permalink($position).'" data-id="'.$position->ID.'" data-modal-type="position-modal" class="show-post-modal">'.$position->post_title.'</a></h3>
              <a href="'.get_permalink($position).'" class="show-post-modal read-more-link" data-modal-type="position-modal" data-id="'.$position->ID.'"><button class="plus-button"><div class="plus"></div></button> <span class="sr-only">Continued</span></a></li>';
              else
                echo '<li>'.$position->post_title.'</li>';
            }
          }
          echo '</ul></div>';
        }
      ?>
    </div>

    <div class="content user-content -right">
      <?= apply_filters('the_content', $post->post_content) ?>
    </div>  

  </div>

</article>

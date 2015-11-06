<header class="site-header" role="banner">
  <div class="wrap grid">
    <h1 class="logo grid-item one-half"><a class="brand" href="<?= esc_url(home_url('/')); ?>"><svg class="icon icon-logo"><use xlink:href="#icon-logo"/></svg><span class="sr-only"><?php bloginfo('name'); ?></span></a> <span class="sub-title">— Design for a changing world.</span></h1>
    <nav class="site-nav grid-item one-half" role="navigation">
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
      <div class="nav-actions">
        <a class="show-collection" href="/collection/"><span class="sr-only">Collection</span><svg class="icon icon-collection"><use xlink:href="#icon-collection"></svg></a>
        <a class="show-search" href="/search/"><span class="sr-only">Search</span><svg class="icon icon-search"><use xlink:href="#icon-search"></svg></a>
      </div>
    </nav>
  </div>

  <?php global $collection; $collection = \Firebelly\Collections\get_active_collection(); ?>
  <section class="collection mini" data-id="<?= $collection->ID ?>">
    <?php include(locate_template('templates/collection.php')); ?>
  </section>
</header>

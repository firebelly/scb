<header class="banner" role="banner">
  <div class="container">
    <a class="brand" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <nav role="navigation">
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
      <a class="show-collection" href="/collection/">Collection</a>
      <a class="show-search" href="/search/">Search</a>
    </nav>
  </div>

  <?php global $collection; $collection = \Firebelly\Collections\get_active_collection(); ?>
  <section class="collection mini">
    <?php include(locate_template('templates/collection.php')); ?>
  </section>
</header>

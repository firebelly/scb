<?php

use Roots\Sage\Config;
use Roots\Sage\Wrapper;
if (is_tax()) {
  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
  $parent = get_term($term->parent, get_query_var('taxonomy') );
  $parentTerm = ($term->parent == 0) ? '' : $parent->slug;
} elseif ($post->post_type=='project') {
  $parentTerm = \Firebelly\Utils\get_top_parent_cat($post);
} else {
  $parentTerm = '';
}

?>

<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>
  <body <?php body_class(); ?> data-pageClass="<?= $parentTerm ?>">
    <!--[if lt IE 9]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <div class="site-container" role="document">
      <div class="content row">
        <main class="main" role="main">
          <?php include Wrapper\template_path(); ?>
        </main><!-- /.main -->
        <?php if (Config\display_sidebar()) : ?>
          <aside class="sidebar" role="complementary">
            <?php include Wrapper\sidebar_path(); ?>
          </aside><!-- /.sidebar -->
        <?php endif; ?>
      </div><!-- /.content -->
    </div><!-- /.container -->
    <?php
      do_action('get_footer');
      get_template_part('templates/footer');
      wp_footer();
    ?>
    <?php if (WP_ENV === 'development'): ?>
    <script type='text/javascript' id="__bs_script__">//<![CDATA[
        document.write("<script async src='http://HOST:3000/browser-sync/browser-sync-client.2.11.1.js'><\/script>".replace("HOST", location.hostname));
    //]]></script>
    <?php endif; ?>
  </body>
</html>

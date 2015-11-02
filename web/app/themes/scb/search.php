<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php while (have_posts()) : the_post();

  if ($post->post_type==='person'):

    $person_post = $post;
    include(locate_template('templates/article-person.php'));

  elseif ($post->post_type==='project'):

    $project_post = $post;
    include(locate_template('templates/article-project.php'));

  elseif (preg_match('/(page)/',$post->post_type)):

    get_template_part('templates/content', 'search');

  endif;

endwhile; ?>

<?php the_posts_navigation(); ?>

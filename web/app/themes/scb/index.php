<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<div class="article-list">
<?php while (have_posts()) : the_post(); ?>
	<?php 
	$news_post = $post;
	include(locate_template('templates/article-news-excerpt.php'));
	?>
<?php endwhile; ?>
</div>

<?php the_posts_navigation(); ?>
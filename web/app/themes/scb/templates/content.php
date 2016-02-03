<?php 
use Firebelly\Utils;
$category = Utils\get_category($post);
$has_image_class = !empty($show_images) && has_post_thumbnail($post->ID) ? 'has-image' : '';
$thumb = \Firebelly\Media\get_post_thumbnail($post->ID);
?>
<article class="article <?= $has_image_class ?>" style="background-image:url(<?= $thumb ?>);">
  <time datetime="<?= get_post_time('c', true); ?>"><?= get_the_date(); ?></time>
  <div class="article-inner">  
    <header>
      <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?>

      <h2 class="entry-title"><a href="<?php the_permalink(); ?>" class="show-post-modal" data-modal-type="news-modal" data-id="<?= $post->ID; ?>"><?php the_title(); ?></a></h2>
    </header>
    <div class="entry-summary">
      <?php the_excerpt(); ?>
    </div>
  </div>
</article>
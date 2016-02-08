<?php 
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$post_date_timestamp = strtotime($news_post->post_date);
$has_image_class = !empty($show_images) && has_post_thumbnail($news_post->ID) ? 'has-image' : '';
$thumb = \Firebelly\Media\get_post_thumbnail($news_post->ID);
$featured_class = get_post_meta($news_post->ID, '_featured', true) ? ' featured-post' : '';
?>
<article <?php post_class("article".$featured_class, $news_post->ID); ?>>
  <div class="background-image-wrap">
    <div class="background-image" <?php if ($thumb) { echo 'style="background-image:url('.$thumb.');"'; } ?>></div>
    <div class="article-inner">
      <header>
        <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?>

        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" class="show-post-modal" data-modal-type="news-modal" data-id="<?= $news_post->ID; ?>"><?= wp_trim_words($news_post->post_title, 10); ?></a></h2>
      </header>
      <div class="entry-summary">
        <?= Utils\get_excerpt($news_post); ?>
        <a href="<?php the_permalink(); ?>" class="show-post-modal read-more-link" data-modal-type="news-modal" data-id="<?= $news_post->ID; ?>">+ <span class="sr-only">Continued</span></a>
      </div>
    </div>
  </div>
</article>

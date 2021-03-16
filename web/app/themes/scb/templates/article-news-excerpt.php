<?php
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$thumb = \Firebelly\Media\get_post_thumbnail($news_post->ID);
$featured_class = get_post_meta($news_post->ID, '_featured', true) ? ' featured-post' : '';
?>
<article <?php post_class("article show-post-modal".$featured_class, $news_post->ID); ?> data-id="<?= $news_post->ID; ?>" data-page-title="<?= $news_post->post_title ?>" data-page-url="<?= get_permalink($news_post) ?>" data-pageClass="<?= !empty($parent_cat) ? $parent_cat : '' ?>" data-modal-type="news">
  <div class="background-image-wrap">
    <div class="background-image" <?php if ($thumb) { echo 'style="background-image:url('.$thumb.');"'; } ?>></div>
    <div class="article-inner">
      <header>
        <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?>

        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?= wp_trim_words($news_post->post_title, 10); ?></a></h2>
      </header>
      <div class="entry-summary">
        <p><?= Utils\get_excerpt($news_post); ?></p>
        <a href="<?php the_permalink(); ?>" class="read-more-link">+ <span class="sr-only">Continued</span></a>
      </div>
    </div>
  </div>
</article>

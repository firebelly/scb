<?php 
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$post_date_timestamp = strtotime($news_post->post_date);
$thumb = \Firebelly\Media\get_post_thumbnail($news_post->ID);
$featured_class = get_post_meta($news_post->ID, '_featured', true) ? ' featured-post' : '';
?>
<article <?php post_class("article".$featured_class, $news_post->ID); ?> data-id="<?= $post->ID ?>" data-page-title="<?= $post->post_title ?>" data-page-url="<?= get_permalink($post) ?>">
  <div class="background-image-wrap" <?php if ($thumb) { echo 'style="background-image:url('.$thumb.');"'; } ?>>
    <time class="article-date" datetime="<?= date('c', $post_date_timestamp); ?>"><span><?= date('m/', $post_date_timestamp); ?></span><span><?= date('d/', $post_date_timestamp); ?></span><span><?= date('y', $post_date_timestamp); ?></span></time>
  </div>
    <div class="article-body">
      <header>
        <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?>
        <h1 class="article-title"><?= $news_post->post_title ?></h1>
      </header>
      <div class="entry-content">
        <?= apply_filters('the_content', $post->post_content) ?>
      </div>
    </div>
</article>

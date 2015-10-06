<?php 
if (!$collection) {
  die('No collection');
}
$post_type = '';

if (empty($collection->posts)):

  echo '<p>Your collection is empty</p>';

else:

  foreach ($collection->posts as $collection_post):

    // Show header above each group of post types
    if ($post_type != $collection_post->post_type) {
      echo '<h2>'.ucfirst($collection_post->post_type).'s</h2>';
      $post_type = $collection_post->post_type;
    }
    if ($post_type=='project') {
      $project_post = $collection_post;
      include(locate_template('templates/article-project.php'));
    }
    if ($post_type=='person') {
      $person_post = $collection_post;
      include(locate_template('templates/article-person.php'));
    }

  endforeach;

endif;
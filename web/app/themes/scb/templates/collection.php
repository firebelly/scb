<?php 
if (!$collection) {
  die('No collection');
}
$post_type = '';
$post_type_titles = [
  'person' => 'Bios',
  'project' => 'Projects'
];

if (empty($collection->posts)):

  echo '<p>Your collection is empty</p>';

else:

  echo '<div class="post-group sortable" data-id="'.$collection->ID.'">';
  foreach ($collection->posts as $collection_post):

    // Show header above each group of post types
    if ($post_type != $collection_post->post_type) {
      if ($post_type) echo '</div><div class="post-group sortable" data-id="'.$collection->ID.'">';
      echo '<h2>'.$post_type_titles[$collection_post->post_type].'</h2>';
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
  echo '</div>';

endif;
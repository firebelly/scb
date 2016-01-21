<?php 
$post_type = '';
$post_type_titles = [
  'person' => 'Bios',
  'project' => 'Projects'
];
$post_type_plurals = [
  'person' => 'people',
  'project' => 'projects'
];

echo '<button class="plus-button close hide-collection"><div class="plus"></div></button>';

if (!isset($collection) || empty($collection->posts)):

  echo '<p>Your collection is empty. Add Something 👍</p>';

else:

  echo '<h1>Collection <span class="collection-id" contentEditable="true">'.$collection->ID.'</span></h1>';
  foreach ($collection->posts as $collection_post):


    // Show header above each group of post types
    if ($post_type != $collection_post->post_type) {
      echo (!empty($post_type) ? '</div>' : '') . '<div class="'.$post_type_plurals[$collection_post->post_type].' post-group sortable" data-id="'.$collection->ID.'">';
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
  echo '<div class="collection-actions"><a href="#" class="button collection-action" data-action="email">Email</a>';
  echo '<a href="#" class="button collection-action" data-action="pdf">Save as pdf</a>';
  echo '<a href="#" class="button collection-action" data-action="print">print</a></div>';

endif;
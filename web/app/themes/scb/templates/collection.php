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
?>

<button class="plus-button close hide-modal hide-collection"><div class="plus"></div></button>
<div class="feedback-container"></div>

<?php if (empty($collection) || empty($collection->posts)): ?>

  <p class="empty-message">Your collection is empty. Add Something 👍</p>

<?php else: ?>

  <h1 class="collection-name">Collection <span class="collection-title" contentEditable data-id="<?= $collection->ID ?>"><?= !empty($collection->title) ? stripslashes($collection->title) : $collection->ID ?></span></h1>
  <div class="post-group" data-id="<?= $collection->ID ?>">

  <?php 
  foreach ($collection->posts as $collection_post):
    // Show header above each group of post types
    if ($post_type != $collection_post->post_type) {
      echo (!empty($post_type) ? '</div>' : '') . '<div class="'.$post_type_plurals[$collection_post->post_type].' post-group" data-id="'.$collection->ID.'">';
      echo '<h2>'.$post_type_titles[$collection_post->post_type].'</h2><div class="grid-wrapper sortable">';
      $post_type = $collection_post->post_type;
    }
    
    if ($post_type=='project') {
      $project_post = $collection_post;
      include(locate_template('templates/article-project.php'));
    }
    if ($post_type=='person') {
      $person_post = $collection_post;
      include(locate_template('templates/collection-article-person.php'));
    }

  endforeach;
    echo '</div>';
  ?>

  </div>
  <div class="collection-actions"><a href="#" class="button email-collection" data-action="email">Email</a>
  <a href="#" class="button collection-action" data-action="pdf">Save as pdf</a>
  <a href="#" class="button collection-action" data-action="pdf">print</a></div>

  <div id="email-collection-form">
    <form action="<?= admin_url('admin-ajax.php') ?>" method="post">
      <input name="to_email" type="email" placeholder="to email" required>
      <input name="replyto_email" type="email" placeholder="your email" class="optional">
      <input name="subject" type="text" placeholder="subject" require>
      <textarea name="message" cols="30" rows="10" placeholder="message"></textarea>
      <input name="collection_id" type="hidden" value="<?= $collection->ID ?>">
      <input name="action" type="hidden" value="email_collection"><br>
      <label><input id="cc_me" name="cc_me" type="checkbox"> Send me a copy</label>
      <input type="submit">
    </form>
  </div>

<?php endif; ?>
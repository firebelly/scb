<?php
/**
 * Template Name: People
 */

$person_categories = get_terms('person_category');
?>

<?= $post->post_content ?>

<section class="people">
  <?php 
  foreach($person_categories as $person_category) {
  	echo '<h2>'.$person_category->name.'</h2>';
  	if ($people_posts = Firebelly\PostTypes\Person\get_people(['person_category' => $person_category->slug])) {
  		echo '<ul>';
  		foreach($people_posts as $people_post) {
  			echo '<li><a href="'.get_permalink($people_post).'">'.$people_post->post_title.'</a></li>';
  		}
  		echo '</ul>';
  	}
  }
  ?>
</section>

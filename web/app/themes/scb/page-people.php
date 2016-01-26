<?php
/**
 * Template Name: People
 */

$person_categories = get_terms('person_category');
?>

<div class="grid">
  
  <section class="page-content grid-item one-half">
    <?= $post->post_content ?>
  </section>

  <section class="people grid-item one-half">
    <?php 
    foreach($person_categories as $person_category) {
    	echo '<div class="category-group"><h2>'.$person_category->name.'</h2>';
    	if ($people_posts = Firebelly\PostTypes\Person\get_people(['person_category' => $person_category->slug])) {
    		echo '<ul>';
    		foreach($people_posts as $people_post) {
    			if (!empty($people_post->post_content))
  	  			echo '<li><a href="'.get_permalink($people_post).'">'.$people_post->post_title.'</a></li>';
  	  		else
  	  			echo '<li>'.$people_post->post_title.'</li>';
    		}
    		echo '</ul></div>';
    	}
    }
    ?>
  </section>

</div>


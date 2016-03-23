<?php
/**
 * Template Name: People
 */

$person_categories = get_terms('person_category');
?>

<div class="grid">

  <section class="page-content grid-item one-half">
    <?= $post->post_content ?>
    <div class="stat">
      <div class="stat-number">77</div>
      <div class="stat-label">Universities Represented</div>
    </div>
  </section>

  <section class="people-section grid-item one-half">
    <?php
    foreach($person_categories as $person_category) {
    	echo '<div class="category-group"><h2>'.$person_category->name.'</h2>';
    	if ($people_posts = Firebelly\PostTypes\Person\get_people(['person_category' => $person_category->slug])) {
    		echo '<ul>';
    		foreach($people_posts as $people_post) {
    			if (!empty($people_post->post_content))
  	  			echo '<li><a href="'.get_permalink($people_post).'" data-id="'.$people_post->ID.'" data-modal-type="person-modal" class="show-post-modal">'.$people_post->post_title.'</a></li>';
  	  		else
  	  			echo '<li>'.$people_post->post_title.'</li>';
    		}
    		echo '</ul></div>';
    	}
    }
    ?>
  </section>

</div>


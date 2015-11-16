<?php
/**
 * Template Name: Apply
 */

// Uploading?
if('POST'===$_SERVER['REQUEST_METHOD'] && !empty($_POST['application_form_nonce'])) {
  if (wp_verify_nonce($_POST['application_form_nonce'], 'application_form')) {
    $return = \Firebelly\PostTypes\Applicant\new_applicant();
    if (is_array($return)) {
      echo '<h2>Error saving application</h2>'.implode("<br>", $return);
    } else {
      echo '<h2>Application was saved OK</h2>';
    }
  }
}

?>

<header><?= $post->post_content ?></header>

<?php 
$application_type = 'portfolio';
include(locate_template('templates/application-form.php')); 
?>

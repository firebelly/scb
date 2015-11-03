<?php
/**
 * Template Name: Apply
 */

// Uploading?
if('POST'===$_SERVER['REQUEST_METHOD']) {
  if (!empty($_POST['application_form_nonce']) && wp_verify_nonce($_POST['application_form_nonce'], 'application_form')) {
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

<form method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">
  
  <div><input type="text" name="application_first_name" placeholder="First Name"></div>
  <div><input type="text" name="application_last_name" placeholder="Last Name"></div>
  <div><input type="email" name="application_email" placeholder="Email Address"></div>
  <div><input type="text" name="application_phone" placeholder="Phone Number"></div>
  
  <input type="file" name="application_files[]" multiple>
  <?php wp_nonce_field( 'application_form', 'application_form_nonce' ); ?>
  <input type="submit" />

</form>

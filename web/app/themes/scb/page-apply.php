<?php
/**
 * Template Name: Apply
 */

// Uploading?
if ( 
	isset( $_POST['application_form_nonce'], $_POST['post_id'] ) 
	&& wp_verify_nonce( $_POST['application_form_nonce'], 'application_form' )
	&& current_user_can( 'edit_post', $_POST['post_id'] )
) {
	// The nonce was valid and the user has the capabilities, it is safe to continue.

	// These files need to be included as dependencies when on the front end.
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	
	// Let WordPress handle the upload.
	// Remember, 'application_form' is the name of our file input in our form above.
	$attachment_id = media_handle_upload( 'application_form', $_POST['post_id'] );
	
	if ( is_wp_error( $attachment_id ) ) {
		// There was an error uploading the image.
	} else {
		// The image was uploaded successfully!
	}

} else {

	// The security check failed, maybe show the user an error.
}

?>

<?= $post->post_content ?>

<form method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">
	
	<div><input type="text" name="application_first_name" placeholder="First Name"></div>
	<div><input type="text" name="application_last_name" placeholder="Last Name"></div>
	<div><input type="email" name="application_email" placeholder="Email Address"></div>
	<div><input type="email" name="application_phone" placeholder="Phone Number"></div>
	
	<input type="file" name="application_files" multiple="true" />
	<?php wp_nonce_field( 'application_form', 'application_form_nonce' ); ?>
	<input type="submit" />
	
</form>
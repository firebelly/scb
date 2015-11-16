<form class="application-form" method="post" enctype="multipart/form-data">
  
  <div><input type="text" name="application_first_name" placeholder="First Name"></div>
  <div><input type="text" name="application_last_name" placeholder="Last Name"></div>
  <div><input type="email" name="application_email" placeholder="Email Address"></div>
  <div><input type="text" name="application_phone" placeholder="Phone Number"></div>

  <?php if (!empty($position_id)): ?>
    <input type="hidden" name="position_id" value="<?= $position_id ?>">
  <?php endif ?>

  <input type="hidden" name="application_type" value="<?= !empty($application_type) ? $application_type : !empty($position_id) ? 'position' : 'portfolio' ?>">

  <input type="file" name="application_files[]" multiple>
  <?php wp_nonce_field( 'application_form', 'application_form_nonce' ); ?>
  <input type="submit" />

</form>

<?php 
if (empty($application_type)) {
  $application_type = !empty($position_id) ? 'position' : 'portfolio';
}
?>
<form action="<?= admin_url('admin-ajax.php') ?>" class="application-form" method="post" enctype="multipart/form-data" novalidate>

  <?php if (empty($position_id)): ?>
    <h2>Send Us Your Portfolio</h2>
  <?php endif ?>

  <fieldset>
    <div><input type="text" name="application_first_name" placeholder="First Name" required></div>
    <div><input type="text" name="application_last_name" placeholder="Last Name" required></div>
    <div><input type="email" name="application_email" placeholder="Email Address" required></div>
    <div><input type="tel" name="application_phone" placeholder="Phone Number" required></div>

    <?php if (!empty($position_id)): ?>
      <input type="hidden" name="position_id" value="<?= $position_id ?>">
    <?php endif ?>

    <input type="hidden" name="application_type" value="<?= $application_type ?>">
    <input name="action" type="hidden" value="application_submission">

    <input type="file" name="application_files[]" multiple>
    <?php wp_nonce_field( 'application_form', 'application_form_nonce' ); ?>
  </fieldset>

  <input type="submit" value="submit" class="button">
</form>

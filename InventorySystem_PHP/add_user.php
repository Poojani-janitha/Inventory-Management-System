<?php
  $page_title = 'Add User';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php include_once('layouts/header.php'); ?>
  <?php echo display_msg($msg); ?>

  <!-- Include the shared form/processing block -->
  <?php include_once('add_user_form.php'); ?>

<?php include_once('layouts/footer.php'); ?>

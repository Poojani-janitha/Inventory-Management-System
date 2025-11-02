<?php
  $page_title = 'Home Page';
  require_once('includes/load.php');
  //extra add prabashi 
  $msg = $session->msg();
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
 <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
        <img src="assets/images/logo.png" alt="HealStock Logo" class="home-logo" style="max-height:300px; width:auto; max-width:100%; margin-bottom:20px; display:block; margin-left:auto; margin-right:auto;" />
        <h3>
    Welcome to HealStock’s all-in-one inventory management system.
</h3>
      </div>
    </div>
 </div>
</div>
<?php include_once('layouts/footer.php'); ?>

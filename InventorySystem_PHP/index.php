<?php
  ob_start();
  require_once('includes/load.php');
  $msg = $session->msg();
  if($session->isUserLoggedIn(true)) { redirect('home.php', false); }
?>
<?php include_once('layouts/header.php'); ?>

<style>
/* ====== FULLSCREEN CENTERING ====== */
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.fullscreen-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ====== LOGIN CONTAINER ====== */
.login-page {
  width: 380px;
  background: #ffffff;
  padding: 40px 30px;
  border-radius: 15px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  text-align: center;
  animation: fadeInUp 0.8s ease-in-out;
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* ====== TEXT STYLES ====== */
.login-page h1 {
  font-size: 28px;
  font-weight: 700;
  color: #333;
  margin-bottom: 5px;
}

.login-page h4 {
  font-size: 16px;
  color: #777;
  margin-bottom: 30px;
}

/* ====== FORM ELEMENTS ====== */
.form-group {
  text-align: left;
  margin-bottom: 20px;
}

label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 6px;
  display: block;
}

.form-control {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px 12px;
  width: 100%;
  font-size: 14px;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  outline: none;
}

/* ====== BUTTON STYLE ====== */
.btn-login {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 8px;
  color: #fff;
  font-weight: 600;
  width: 100%;
  padding: 12px;
  transition: all 0.3s ease;
  font-size: 15px;
}

.btn-login:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: scale(1.02);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

/* ====== FOOTER TEXT ====== */
.footer-text {
  font-size: 13px;
  color: #777;
  margin-top: 15px;
}

.footer-text a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.footer-text a:hover {
  text-decoration: underline;
}

/* ====== RESPONSIVE ====== */
@media (max-height: 600px) {
  .fullscreen-wrapper {
    align-items: flex-start;
    padding-top: 40px;
  }
}
</style>

<div class="fullscreen-wrapper">
  <div class="login-page">
    <div class="text-center">
      <h1>Login Panel</h1>
      <h4>Inventory Management System</h4>
    </div>
    
    <?php echo display_msg($msg); ?>
    
    <form method="post" action="auth.php" class="clearfix">
      <div class="form-group">
        <label for="username" class="control-label">Username</label>
        <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
      </div>

      <div class="form-group">
        <label for="password" class="control-label">Password</label>
        <input type="password" id="password" class="form-control" name="password" placeholder="Enter your password" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-login">
          <span class="glyphicon glyphicon-log-in"></span> Login
        </button>
      </div>
    </form>

    <p class="footer-text">
      © <?php echo date('Y'); ?> Inventory Management System<br>
      <a href="#">Need help?</a>
    </p>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

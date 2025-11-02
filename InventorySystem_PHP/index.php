<?php
  ob_start();
  require_once('includes/load.php');
  $msg = $session->msg();
  if($session->isUserLoggedIn(true)) { redirect('home.php', false); }
?>
<?php include_once('layouts/header.php'); ?>

<style>
/* ====== RESET & BASE STYLES ====== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
  background-size: 400% 400%;
  animation: gradientBG 15s ease infinite;
  overflow-x: hidden;
}

@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* ====== FULLSCREEN WRAPPER ====== */
.fullscreen-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

/* ====== DECORATIVE BLOBS ====== */
.blob {
  position: absolute;
  opacity: 0.1;
  mix-blend-mode: multiply;
  border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
  animation: blob 7s infinite;
}

.blob-1 {
  width: 200px;
  height: 200px;
  background: #ffffff;
  top: -50px;
  left: -50px;
  animation-delay: 0s;
}

.blob-2 {
  width: 300px;
  height: 300px;
  background: #ffffff;
  bottom: -100px;
  right: -100px;
  animation-delay: 2s;
}

.blob-3 {
  width: 150px;
  height: 150px;
  background: #ffffff;
  top: 50%;
  right: 10%;
  animation-delay: 4s;
}

@keyframes blob {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(20px, -50px) scale(1.1); }
  50% { transform: translate(-20px, 20px) scale(0.9); }
  75% { transform: translate(50px, 50px) scale(1.05); }
}

/* ====== LOGIN CONTAINER ====== */
.login-page {
  width: 100%;
  max-width: 360px;
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  padding: 15px 20px;
  border-radius: 20px;
  box-shadow: 
    0 8px 32px rgba(31, 38, 135, 0.37),
    0 20px 60px rgba(0, 0, 0, 0.2);
  text-align: center;
  animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  z-index: 10;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

@keyframes slideInUp {
  0% { 
    opacity: 0; 
    transform: translateY(60px); 
  }
  100% { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

/* ====== LOGO/BRAND SECTION ====== */
.login-brand {
  margin-bottom: 8px;
  position: relative;
}

.brand-icon {
  width: auto;
  height: auto;
  margin: 0 auto 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-icon img {
  max-width: 70px;
  height: 70px;
  object-fit: contain;
}

.login-page h1 {
  font-size: 26px;
  font-weight: 900;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 4px;
  letter-spacing: -0.5px;
}

.login-page h4 {
  font-size: 13px;
  color: #999;
  font-weight: 600;
  letter-spacing: 2px;
  text-transform: uppercase;
}

/* ====== FORM ELEMENTS ====== */
.form-group {
  text-align: left;
  margin-bottom: 12px;
  position: relative;
}

label {
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 6px;
  display: block;
  font-size: 13px;
  letter-spacing: 0.5px;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #667eea;
  z-index: 2;
}

.form-control {
  border: 2px solid #e8e8e8;
  border-radius: 14px;
  padding: 14px 15px 14px 50px;
  width: 100%;
  font-size: 15px;
  transition: all 0.4s ease;
  background: #f8f9fa;
  color: #333;
}

.form-control::placeholder {
  color: #bbb;
}

.form-control:focus {
  border-color: #667eea;
  background: #fff;
  box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.15);
  outline: none;
  transform: translateY(-2px);
}

.form-control:hover {
  border-color: #667eea;
}

/* ====== PASSWORD SHOW/HIDE TOGGLE ====== */
.password-toggle {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #667eea;
  font-size: 18px;
  z-index: 2;
  transition: all 0.3s ease;
}

.password-toggle:hover {
  transform: translateY(-50%) scale(1.2);
}

/* ====== BUTTON STYLE ====== */
.btn-login {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  border: none;
  border-radius: 14px;
  color: #fff;
  font-weight: 700;
  width: 100%;
  padding: 14px;
  transition: all 0.4s ease;
  font-size: 16px;
  letter-spacing: 0.5px;
  cursor: pointer;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  position: relative;
  overflow: hidden;
}

.btn-login::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.3);
  transition: left 0.4s ease;
}

.btn-login:hover::before {
  left: 100%;
}

.btn-login:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
}

.btn-login:active {
  transform: translateY(-1px);
}

/* ====== CHECKBOX STYLE ====== */
.checkbox-group {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  gap: 8px;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #667eea;
}

.checkbox-group label {
  margin: 0;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  color: #666;
}

/* ====== FOOTER TEXT ====== */
.footer-text {
  font-size: 13px;
  color: #999;
  margin-top: 20px;
  line-height: 1.8;
}

.footer-text a {
  color: #667eea;
  text-decoration: none;
  font-weight: 700;
  transition: all 0.3s ease;
  position: relative;
}

.footer-text a::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  transition: width 0.3s ease;
}

.footer-text a:hover::after {
  width: 100%;
}

/* ====== RESPONSIVE ====== */
@media (max-width: 480px) {
  .login-page {
    padding: 35px 25px;
    border-radius: 20px;
  }

  .login-page h1 {
    font-size: 26px;
  }

  .brand-icon img {
    max-width: 75px;
  }

  .form-control {
    padding: 12px 15px 12px 45px;
    font-size: 14px;
  }
}

/* ====== MESSAGE STYLING ====== */
.alert {
  border-radius: 14px;
  border: none;
  margin-bottom: 18px;
  padding: 14px 16px;
  font-size: 13px;
  animation: slideDown 0.5s ease-out;
  font-weight: 600;
}

@keyframes slideDown {
  0% {
    opacity: 0;
    transform: translateY(-15px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-success {
  background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(76, 175, 80, 0.05));
  color: #2e7d32;
  border-left: 5px solid #4CAF50;
}

.alert-danger {
  background: linear-gradient(135deg, rgba(244, 67, 54, 0.15), rgba(244, 67, 54, 0.05));
  color: #c62828;
  border-left: 5px solid #f44336;
}

.alert-warning {
  background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
  color: #f57f17;
  border-left: 5px solid #FFC107;
}
</style>

<div class="fullscreen-wrapper">
  <!-- Decorative blobs -->
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <div class="login-page">
    <div class="login-brand">
      <div class="brand-icon">
        <img src="assets/images/logo.png" alt="HealStock Logo">
      </div>
      <h1>HealStock Pvt Ltd</h1>
      <h4>Inventory System</h4>
    </div>
    
    <?php echo display_msg($msg); ?>
    
    <form method="post" action="auth.php" class="clearfix">
      <div class="form-group">
        <label for="username" class="control-label">Username</label>
        <div class="input-wrapper">
          <span class="input-icon">👤</span>
          <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
        </div>
      </div>

      <div class="form-group">
        <label for="password" class="control-label">Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" class="form-control" name="password" placeholder="Enter your password" required>
          <button type="button" class="password-toggle" onclick="togglePassword()">👁️</button>
        </div>
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember me</label>
      </div>

      <div class="form-group">
        <button type="submit" class="btn-login">
          <span class="glyphicon glyphicon-log-in"></span> Login Now
        </button>
      </div>
    </form>

    <p class="footer-text">
      © <?php echo date('Y'); ?> HealStock Pvt Ltd<br>
      <a href="#">Need help? Contact Support</a>
    </p>
  </div>
</div>

<script>
function togglePassword() {
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.querySelector('.password-toggle');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleBtn.textContent = '👁️‍🗨️';
  } else {
    passwordInput.type = 'password';
    toggleBtn.textContent = '👁️';
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>
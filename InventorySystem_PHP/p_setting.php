<?php
$page_title = 'Profile Settings';
require_once('includes/load.php');
page_require_level(2);

$user = current_user();
$msg = '';
?>

<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/profile_setting.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="row">
  <div class="col-md-12">
    <?php echo $msg; ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default" id="mainPanel">
      <!-- Stylish Title -->
      <div class="panel-heading">
        <strong><i class="glyphicon glyphicon-cog"></i> PROFILE SETTINGS</strong>
      </div>

      <div class="panel-body fadeIn">

        <!-- Profile Photo & Info (Centered) -->
        <div class="profile-section text-center mb-4">
          <div class="profile-photo mb-2">
            <img src="uploads/users/<?php echo $user['image'] ?? 'default.png'; ?>" 
                 class="img-circle profile-img"
                 alt="User Image">
          </div>
          <div class="profile-info">
            <h3 class="user-name"><?php echo remove_junk(ucwords($user['name'])); ?></h3>
            <p class="user-role text-muted"><?php echo $user['user_level'] == '1' ? 'Admin' : 'User'; ?></p>
          </div>
        </div>

        <!-- Notification Settings Section -->
        <div class="settings-section">
          <h4 class="section-title"><i class="glyphicon glyphicon-bell"></i> Notification Settings</h4>
          <div class="notification-settings">
            <?php
              $notification_settings = [
                'notifToggle' => 'Notifications',
                'popupToggle' => 'Show Pop-ups',
                'soundToggle' => 'Play Pop-up Sound'
              ];
              foreach ($notification_settings as $id => $label):
            ?>
              <div class="setting-item">
                <span><?php echo $label; ?></span>
                <label class="switch">
                  <input type="checkbox" id="<?php echo $id; ?>" checked>
                  <span class="slider round"></span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Additional Settings Section -->
        <div class="settings-section mt-4">
          <h4 class="section-title"><i class="glyphicon glyphicon-cog"></i> Additional Settings</h4>
          <div class="notification-settings">
            <?php
              $additional_settings = [
                'darkModeToggle' => 'Dark Mode',
                'emailAlertToggle' => 'Email Alerts'
              ];
              foreach ($additional_settings as $id => $label):
            ?>
              <div class="setting-item">
                <span><?php echo $label; ?></span>
                <label class="switch">
                  <input type="checkbox" id="<?php echo $id; ?>" <?php echo ($id != 'darkModeToggle') ? 'checked' : ''; ?> >
                  <span class="slider round"></span>
                </label>
              </div>
            <?php endforeach; ?>

            <!-- Static Privacy Policy -->
            <div class="setting-item">
              <span>Privacy Policy Accepted</span>
              <span class="text-success" style="font-weight:600;">✔ Accepted</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
$(function(){

  // Fade out alerts
  setTimeout(() => $(".alert").fadeOut(), 4000);

  // Toggle feedback popup
  $(".switch input").on('change', function(){
    const name = $(this).attr('id').replace('Toggle','');
    const status = $(this).is(':checked') ? 'ON' : 'OFF';
    const message = `<div class='alert-popup'>
                      <i class='glyphicon glyphicon-info-sign'></i> ${name.toUpperCase()} turned ${status}
                    </div>`;
    $("body").append(message);
    setTimeout(() => $(".alert-popup").fadeOut(600, function(){ $(this).remove(); }), 1500);
  });

  // Dark Mode
  $("#darkModeToggle").on('change', function(){
    if($(this).is(':checked')){
      $("body").addClass("dark-mode");
    } else {
      $("body").removeClass("dark-mode");
    }
  });

  // Email Alert Simulation
  $("#emailAlertToggle").on('change', function(){
    $(this).is(':checked')
      ? alert("✅ Email alerts enabled! You’ll get stock and activity updates.")
      : alert("🚫 Email alerts disabled.");
  });

});
</script>

<style>
/* ======= General ======= */
body {
  background-color: #ffffff;
  color: #333;
}
.panel {
  border-radius: 15px;
  background: #f9f9ff;
  color: #333;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: all 0.4s ease;
}
.panel-heading {
  background: linear-gradient(135deg, #6c63ff, #8576ff);
  color: #fff;
  text-align: left;
  font-weight: 700;
  font-size: 22px;
  letter-spacing: 1px;
  text-transform: uppercase;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  padding: 15px 25px;
  position: relative;
  overflow: hidden;
}
.panel-heading i { margin-right: 8px; }

/* ======= Animated Page Entrance ======= */
.fadeIn { animation: fadeIn 0.8s ease-in-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ======= Profile Section ======= */
.profile-section { text-align: center; }
.profile-photo { margin: 0 auto 10px auto; }
.profile-img { 
  width: 120px; height: 120px; 
  object-fit: cover; 
  border: 4px solid #6c63ff; 
  border-radius: 50%; }
.profile-info .user-name { 
  font-weight: 600;
  margin: 0; }
.profile-info .user-role { 
  font-size: 14px; 
  color: #555; 
  margin: 2px 0 0 0; }

/* ======= Settings Sections ======= */
.settings-section { margin-bottom: 25px; }
.section-title {
   margin-bottom: 15px; 
   font-weight: 600; 
   color: #333; }
.notification-settings { 
  background: #ffffff; 
  padding: 20px; 
  border-radius: 12px; 
  box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.setting-item { 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  padding: 12px 0; 
  border-bottom: 1px solid #eaeaea; }
.setting-item:last-child { 
  border-bottom: none; }

/* ======= Toggle Switch ======= */
.switch { 
  position: relative; 
  display: inline-block; 
  width: 52px; 
  height: 28px; }
.switch input { 
  opacity: 0; 
  width: 0; 
  height: 0; }
.slider { 
  position: absolute; 
  cursor: pointer; 
  top: 0; 
  left: 0; 
  right: 0; 
  bottom: 0; 
  background-color: #ccc; 
  transition: .4s; 
  border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #6c63ff; box-shadow: 0 0 8px rgba(108,99,255,0.5); }
input:checked + .slider:before { transform: translateX(24px); }

/* ======= Alert Popup ======= */
.alert-popup { position: fixed; bottom: 25px; right: 25px; background: #6c63ff; color: #fff; padding: 12px 18px; border-radius: 10px; font-size: 14px; z-index: 9999; box-shadow: 0 4px 8px rgba(0,0,0,0.2); animation: fadeIn 0.4s ease; }

/* ======= Dark Mode ======= */
body.dark-mode { background-color: #1e1e2f; color: #f0f0f0; }
body.dark-mode .panel { background: #2a2a3d; color: #f0f0f0; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
body.dark-mode .panel-heading { background: linear-gradient(135deg, #4e4ecf, #6c63ff); color: #fff; }
body.dark-mode .notification-settings { background: #2a2a3d; color: #f0f0f0; }
body.dark-mode .setting-item { border-bottom: 1px solid #444; }
body.dark-mode .alert-popup { background: #4e4ecf; color: #fff; }
body.dark-mode .profile-info .user-role { color: #ccc; }
body.dark-mode .section-title { color: #b3aaff; text-shadow: 0 0 6px rgba(108,99,255,0.7); }
</style>

<?php include_once('layouts/footer.php'); ?>

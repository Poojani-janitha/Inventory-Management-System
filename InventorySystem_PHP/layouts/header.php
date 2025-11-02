<?php 
  $user = current_user(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>
    <?php 
      if (!empty($page_title))
        echo remove_junk($page_title);
      elseif(!empty($user))
        echo ucfirst($user['name']);
      else 
        echo "Inventory Management System";
    ?>
  </title>

  <!-- Bootstrap & Styles -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
  <link rel="stylesheet" href="libs/css/main.css" />

  <style>
    /* Sidebar Styling */
    .sidebar {
      background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%) !important;
      border-right: none !important;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3) !important;
    }
    .sidebar ul { padding: 10px 0 !important; }
    .sidebar ul li {
      margin: 2px 8px !important;
      border-radius: 8px !important;
    }
    .sidebar ul li a {
      color: rgba(255,255,255,0.9) !important;
      padding: 12px 15px !important;
      border-radius: 8px !important;
    }
    .sidebar ul li:hover {
      background-color: rgba(255,255,255,0.15) !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
      transform: translateX(5px) !important;
      transition: all 0.3s ease-in-out !important;
    }
    .sidebar ul li a:hover {
      color: white !important;
      text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
    }
    .sidebar ul li a i {
      color: rgba(255,255,255,0.9) !important;
      padding: 0 15px 0 0 !important;
      width: 20px !important;
      display: inline-block !important;
    }
    .sidebar ul li a span {
      font-weight: 500 !important;
      letter-spacing: 0.5px !important;
      text-shadow: 0 1px 2px rgba(0,0,0,0.2) !important;
    }

    /* Header Logo */
    header > .logo {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
    }
    header > .logo img {
      height: 60px !important;
      max-height: 60px !important;
      width: auto !important;
      display: inline-block !important;
    }

    /* Submenu Styling */
    ul.submenu {
      background: rgba(0,0,0,0.2) !important;
      margin: 5px 0 !important;
      border-radius: 8px !important;
      padding: 5px 0 !important;
    }
    ul.submenu li a {
      color: rgba(255,255,255,0.8) !important;
      font-size: 13px !important;
    }
    ul.submenu li a:hover {
      background-color: rgba(255,255,255,0.1) !important;
    }
    ul.submenu li:before {
      border-color: rgba(255,255,255,0.3) !important;
    }
    ul.submenu li a:before {
      border-top-color: rgba(255,255,255,0.3) !important;
    }
  </style>
</head>
<body>

<?php if ($session->isUserLoggedIn(true)): ?>
  <header id="header">
    <div class="logo pull-left">
      <img src="assets/images/logo.png" alt="Inventory System" />
    </div>
    <div class="header-content">
      <div class="header-date pull-left">
        <?php date_default_timezone_set('Asia/Colombo'); // ✅ Timezone fixed ?>
        <strong id="live-time"><?php echo date("F j, Y, g:i a"); ?></strong>
      </div>

      <div class="pull-right clearfix">
        <ul class="info-menu list-inline list-unstyled">

          <!-- IoT Temperature Widget -->
          <li class="temperature-widget">
            <div id="header-iot-widget" style="display:flex; align-items:center; gap:8px; padding:8px 12px; background:#6f79ff; color:#fff; border-radius:6px; box-shadow:0 2px 5px rgba(0,0,0,0.2); min-width:100px;">
              <span class="glyphicon glyphicon-tint" style="font-size:16px;"></span>
              <div style="line-height:1.2;">
                <div id="header-temp" style="font-weight:600; font-size:12px;">--°C</div>
                <div id="header-hum" style="font-size:10px; opacity:0.9;">--% RH</div>
              </div>
            </div>
          </li>

          <!-- Profile Dropdown -->
          <li class="profile">
            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
              <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">
              <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="profile.php?id=<?php echo (int)$user['id'];?>"><i class="glyphicon glyphicon-user"></i> Profile</a></li>
              <li><a href="edit_account.php"><i class="glyphicon glyphicon-cog"></i> Settings</a></li>
              <li class="last"><a href="logout.php"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </header>

  <!-- IoT + Time Auto Refresh Scripts -->
  <script>
    // Auto-refresh current time every 30 seconds
    function updateTime() {
      const el = document.getElementById('live-time');
      if (!el) return;
      const now = new Date();
      const options = { 
        year: 'numeric', month: 'long', day: 'numeric', 
        hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true 
      };
      el.textContent = now.toLocaleString('en-US', options);
    }
    setInterval(updateTime, 30000);

    // IoT temperature widget with sound alert
    (function(){
      var soundCooldown = false;
      function beepOnce(){
        if(soundCooldown) return;
        soundCooldown = true;
        try{
          var ctx = new (window.AudioContext || window.webkitAudioContext)();
          var oscillator = ctx.createOscillator();
          var gainNode = ctx.createGain();
          oscillator.type = 'sine';
          oscillator.frequency.value = 800;
          oscillator.connect(gainNode);
          gainNode.connect(ctx.destination);
          gainNode.gain.setValueAtTime(0.001, ctx.currentTime);
          gainNode.gain.exponentialRampToValueAtTime(0.3, ctx.currentTime + 0.1);
          oscillator.start(ctx.currentTime);
          setTimeout(function(){
            gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            setTimeout(function(){ 
              oscillator.stop();
              ctx.close();
            }, 100);
          }, 400);
        }catch(e){
          console.log('Audio not supported:', e);
        }
        setTimeout(function(){ soundCooldown = false; }, 1000);
      }

      function refreshHeaderIot(){
        fetch('iot/latest.php')
          .then(r => r.json())
          .then(d => {
            if(d && d.success && d.row){
              var tempEl = document.getElementById('header-temp');
              var humEl = document.getElementById('header-hum');
              var widget = document.getElementById('header-iot-widget');
              if(!tempEl || !humEl || !widget) return;
              var tempNum = parseFloat(d.row.temperature);
              var humNum = parseFloat(d.row.humidity);
              tempEl.textContent = (isNaN(tempNum) ? '--' : tempNum.toFixed(1)) + '°C';
              humEl.textContent = (isNaN(humNum) ? '--' : humNum.toFixed(1)) + '% RH';
              if(!isNaN(tempNum) && tempNum >= 40){
                widget.style.background = '#e53935';
                beepOnce();
              } else {
                widget.style.background = '#6f79ff';
              }
            }
          })
          .catch(err => console.debug('Header iot widget error:', err));
      }

      document.addEventListener('DOMContentLoaded', function(){
        updateTime();
        refreshHeaderIot();
        setInterval(refreshHeaderIot, 15000);
      });
    })();
  </script>

  <!-- Sidebar Section -->
  <div class="sidebar">
    <?php if($user['user_level'] === '1'): ?>
      <?php include_once('admin_menu.php');?>
    <?php elseif($user['user_level'] === '2'): ?>
      <?php include_once('special_menu.php');?>
    <?php elseif($user['user_level'] === '3'): ?>
      <?php include_once('user_menu.php');?>
    <?php endif;?>
  </div>

<?php endif; ?>

<!-- Page container -->
<div class="page">
  <div class="container-fluid">
    <!-- Page content starts here -->

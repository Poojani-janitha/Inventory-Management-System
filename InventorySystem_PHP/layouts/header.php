<?php $user = current_user(); ?>
<!DOCTYPE html>
  <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title><?php if (!empty($page_title))
           echo remove_junk($page_title);
            elseif(!empty($user))
           echo ucfirst($user['name']);
            else echo "Inventory Management System";?>
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="libs/css/main.css" />
  </head>
  <body>
  <?php  if ($session->isUserLoggedIn(true)): ?>
    <header id="header">
      <div class="logo pull-left"> Inventory System</div>
      <div class="header-content">
      <div class="header-date pull-left">
        <strong><?php echo date("F j, Y, g:i a");?></strong>
      </div>
      <div class="pull-right clearfix">
        <ul class="info-menu list-inline list-unstyled">
          <li class="profile">
            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
              <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">
              <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                  <a href="profile.php?id=<?php echo (int)$user['id'];?>">
                      <i class="glyphicon glyphicon-user"></i>
                      Profile
                  </a>
              </li>
             <li>
                 <a href="edit_account.php" title="edit account">
                     <i class="glyphicon glyphicon-cog"></i>
                     Settings
                 </a>
             </li>
             <li class="last">
                 <a href="logout.php">
                     <i class="glyphicon glyphicon-off"></i>
                     Logout
                 </a>
             </li>
           </ul>
          </li>
        </ul>
      </div>
     </div>
    </header>
    <!-- Global floating IOT widget placed under profile area -->
    <div id="iot-floating" style="position:fixed; top:60px; right:16px; z-index:9999; min-width:170px; background:#6f79ff; color:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.15);">
      <div style="display:flex; align-items:center;">
        <div style="width:68px; height:68px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.15); border-top-left-radius:8px; border-bottom-left-radius:8px;">
          <span class="glyphicon glyphicon-tint" style="font-size:28px;"></span>
        </div>
        <div style="padding:10px 12px;">
          <div id="iot-temp" style="font-weight:600; font-size:16px; line-height:1.2;">-- °C</div>
          <div id="iot-hum" style="opacity:0.9; font-size:12px;">-- % RH</div>
        </div>
      </div>
      <div id="iot-alert" style="display:none; padding:6px 10px; font-size:12px; background:#e53935; color:#fff; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">High temperature!</div>
    </div>
    <script>
    // Global refresher for the floating IoT widget
    (function(){
      function refreshIotBox(){
        fetch('iot/latest.php')
          .then(function(r){return r.json();})
          .then(function(d){
            if(d && d.success && d.row){
              var tEl=document.getElementById('iot-temp');
              var hEl=document.getElementById('iot-hum');
              var box=document.getElementById('iot-floating');
              var alertEl=document.getElementById('iot-alert');
              if(!tEl||!hEl||!box||!alertEl) return;
              var tempNum=(d.row.temperature!=null)?parseFloat(d.row.temperature):NaN;
              var humNum=(d.row.humidity!=null)?parseFloat(d.row.humidity):NaN;
              tEl.textContent=(isNaN(tempNum)?'--':tempNum.toFixed(1))+' °C';
              hEl.textContent=(isNaN(humNum)?'--':humNum.toFixed(1))+' % RH';
              if(!isNaN(tempNum) && tempNum>=40){
                box.style.background='#e53935';
                alertEl.style.display='block';
                beepOnce();
              }else{
                box.style.background='#6f79ff';
                alertEl.style.display='none';
              }
            }
          })
          .catch(function(err){
            // keep silent in header to avoid breaking page
            console && console.debug && console.debug('iot widget', err);
          });
      }
      var cooldown=false;
      function beepOnce(){
        if(cooldown) return; cooldown=true;
        try{
          var ctx=new (window.AudioContext||window.webkitAudioContext)();
          var o=ctx.createOscillator(); var g=ctx.createGain();
          o.type='sine'; o.frequency.value=700; o.connect(g); g.connect(ctx.destination);
          g.gain.setValueAtTime(0.001, ctx.currentTime);
          g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime+0.02);
          o.start();
          setTimeout(function(){g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime+0.02);o.stop();ctx.close();}, 350);
        }catch(e){}
        setTimeout(function(){cooldown=false;},5000);
      }
      document.addEventListener('DOMContentLoaded', function(){
        refreshIotBox();
        setInterval(refreshIotBox, 15000);
      });
    })();
    </script>
    <div class="sidebar">
      <?php if($user['user_level'] === '1'): ?>
        <!-- admin menu -->
      <?php include_once('admin_menu.php');?>

      <?php elseif($user['user_level'] === '2'): ?>
        <!-- Special user -->
      <?php include_once('special_menu.php');?>

      <?php elseif($user['user_level'] === '3'): ?>
        <!-- User menu -->
      <?php include_once('user_menu.php');?>

      <?php endif;?>

   </div>
<?php endif;?>

<div class="page">
  <div class="container-fluid">

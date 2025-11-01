<?php
  $page_title = 'Edit User';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
   //extra add prabashi 
    $msg = $session->msg();
?>
<?php
  $e_user = find_by_id('users',(int)$_GET['id']);
  $groups  = find_all('user_groups');
  if(!$e_user){
    $session->msg("d","Missing user id.");
    redirect('users.php');
  }
?>

<?php
// AJAX helper: verify old password (returns simple tokens)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_old_password'])){
  $old = isset($_POST['old_password']) ? remove_junk($db->escape($_POST['old_password'])) : '';
  if(sha1($old) === $e_user['password']){
    echo 'OK_OLD';
  } else {
    echo 'NO_OLD';
  }
  exit();
}

?>

<?php
//Update User basic info
  if(isset($_POST['update'])) {
    $req_fields = array('name','username','level');
    validate_fields($req_fields);
    if(empty($errors)){
             $id = (int)$e_user['id'];
           $name = remove_junk($db->escape($_POST['name']));
       $username = remove_junk($db->escape($_POST['username']));
          $level = (int)$db->escape($_POST['level']);
       $status   = remove_junk($db->escape($_POST['status']));
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}',user_level='{$level}',status='{$status}' WHERE id='{$db->escape($id)}'";
         $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"Acount Updated ");
            redirect('edit_user.php?id='.(int)$e_user['id'], false);
          } else {
            $session->msg('d',' Sorry failed to updated!');
            redirect('edit_user.php?id='.(int)$e_user['id'], false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('edit_user.php?id='.(int)$e_user['id'],false);
    }
  }
?>
<?php
// Update user password with old-password verification and validation
if(isset($_POST['update-pass'])) {
  $req_fields = array('old-password','password','confirm-password');
  validate_fields($req_fields);
  if(empty($errors)){
    $id = (int)$e_user['id'];
    $old_password = remove_junk($db->escape($_POST['old-password']));
    $new_password = remove_junk($db->escape($_POST['password']));
    $confirm_password = remove_junk($db->escape($_POST['confirm-password']));

    // Check old password matches current one
    $old_h = sha1($old_password);
    if($old_h !== $e_user['password']){
      $session->msg('d','The old password you entered is incorrect.');
      redirect('edit_user.php?id='.(int)$e_user['id'], false);
    }

    // New password validation: length, uppercase, number, special char
    if(strlen($new_password) < 6 || strlen($new_password) > 10){
      $errors[] = 'Password must be between 6 and 10 characters.';
    }
    if(!preg_match('/[A-Z]/', $new_password)){
      $errors[] = 'Password must contain at least one capital letter.';
    }
    if(!preg_match('/[0-9]/', $new_password)){
      $errors[] = 'Password must contain at least one number.';
    }
    if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)){
      $errors[] = 'Password must contain at least one special character.';
    }
    if($new_password !== $confirm_password){
      $errors[] = 'New password and confirmation do not match.';
    }

    if(empty($errors)){
      $h_pass = sha1($new_password);
      $sql = "UPDATE users SET password='{$h_pass}' WHERE id='{$db->escape($id)}'";
      $result = $db->query($sql);
      if($result && $db->affected_rows() === 1){
        $session->msg('s','User password has been updated.');
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      } else {
        $session->msg('d','Sorry failed to update user password!');
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      }
    } else {
      $session->msg('d', $errors);
      redirect('edit_user.php?id='.(int)$e_user['id'], false);
    }
  } else {
    $session->msg('d', $errors);
    redirect('edit_user.php?id='.(int)$e_user['id'], false);
  }
}

?>
<?php include_once('layouts/header.php'); ?>
 <div class="row">
   <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-6">
     <div class="panel panel-default">
       <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Update <?php echo remove_junk(ucwords($e_user['name'])); ?> Account
        </strong>
       </div>
       <div class="panel-body">
          <form method="post" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" class="clearfix">
            <div class="form-group">
                  <label for="name" class="control-label">Name</label>
                  <input type="name" class="form-control" name="name" value="<?php echo remove_junk(ucwords($e_user['name'])); ?>">
            </div>
            <div class="form-group">
                  <label for="username" class="control-label">Username</label>
                  <input type="text" class="form-control" name="username" value="<?php echo remove_junk(ucwords($e_user['username'])); ?>">
            </div>
            <div class="form-group">
              <label for="level">User Role</label>
                <select class="form-control" name="level">
                  <?php foreach ($groups as $group ):?>
                   <option <?php if($group['group_level'] === $e_user['user_level']) echo 'selected="selected"';?> value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
                <select class="form-control" name="status">
                  <option <?php if($e_user['status'] === '1') echo 'selected="selected"';?>value="1">Active</option>
                  <option <?php if($e_user['status'] === '0') echo 'selected="selected"';?> value="0">Deactive</option>
                </select>
            </div>
            <div class="form-group clearfix">
                    <button type="submit" name="update" class="btn btn-info">Update</button>
            </div>
        </form>
       </div>
     </div>
  </div>
  <!-- Change password form -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Change <?php echo remove_junk(ucwords($e_user['name'])); ?> password
        </strong>
      </div>
      <div class="panel-body">
        <form action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="post" class="clearfix" id="changePassForm">
          <div class="form-group">
                <label for="old-password" class="control-label">Current Password</label>
                <input type="password" class="form-control" name="old-password" id="old-password" placeholder="Enter current password">
                <small id="oldPassFeedback" class="help-block text-danger" style="display:none;"></small>
          </div>
          <div class="form-group">
                <label for="password" class="control-label">New Password</label>
                <input type="password" class="form-control" name="password" id="new-password" placeholder="Type new password">
                <small class="help-block text-muted">Password must be 6-10 chars, include 1 uppercase, 1 number and 1 special character.</small>
                <small id="newPassFeedback" class="help-block text-danger" style="display:none;"></small>
          </div>
          <div class="form-group">
                <label for="confirm-password" class="control-label">Confirm New Password</label>
                <input type="password" class="form-control" name="confirm-password" id="confirm-password" placeholder="Re-type new password">
                <small id="confirmPassFeedback" class="help-block text-danger" style="display:none;"></small>
          </div>
          <div class="form-group clearfix">
                  <button type="submit" name="update-pass" id="changePassBtn" class="btn btn-danger pull-right" disabled>Change</button>
          </div>
        </form>

        <script>
        (function(){
          var oldInput = document.getElementById('old-password');
          var newInput = document.getElementById('new-password');
          var confirmInput = document.getElementById('confirm-password');
          var changeBtn = document.getElementById('changePassBtn');
          var oldFb = document.getElementById('oldPassFeedback');
          var newFb = document.getElementById('newPassFeedback');
          var confFb = document.getElementById('confirmPassFeedback');

          var oldValid = false, newValid = false, confValid = false;

          function updateButton(){
            changeBtn.disabled = !(oldValid && newValid && confValid);
          }

          // Debounced check of current password via server (to avoid exposing full password, this simply checks equality)
          var oldTimer = null;
          oldInput.addEventListener('input', function(){
            clearTimeout(oldTimer);
            var val = this.value;
            oldValid = false;
            oldFb.style.display='none';
            updateButton();
            if(val.length === 0) return;
            oldTimer = setTimeout(function(){
              var fd = new FormData(); fd.append('check_old_password', '1'); fd.append('old_password', val);
              fetch('edit_user.php?id=<?php echo (int)$e_user['id'];?>', { method: 'POST', body: fd })
                .then(function(r){ return r.text(); })
                .then(function(text){
                  // Server returns a simple token 'OK_OLD' when old password matches, else 'NO_OLD'
                  if(text.indexOf('OK_OLD') !== -1){
                    oldValid = true; oldFb.style.display='none';
                  } else {
                    oldValid = false; oldFb.style.display='block'; oldFb.textContent = 'Current password does not match.';
                  }
                  updateButton();
                }).catch(function(){ oldValid=false; updateButton(); });
            }, 300);
          });

          // New password validation
          function validateNew(){
            var p = newInput.value;
            var errs = [];
            if(p.length < 6 || p.length > 10) errs.push('6-10 characters');
            if(!/[A-Z]/.test(p)) errs.push('an uppercase letter');
            if(!/[0-9]/.test(p)) errs.push('a number');
            if(!/[!@#$%^&*(),.?":{}|<>]/.test(p)) errs.push('a special character');
            if(errs.length){ newFb.style.display='block'; newFb.textContent = 'Missing: ' + errs.join(', '); newValid=false; }
            else { newFb.style.display='none'; newValid=true; }
            updateButton();
          }
          newInput.addEventListener('input', function(){ validateNew(); validateConfirm(); });

          function validateConfirm(){
            if(confirmInput.value !== newInput.value){ confFb.style.display='block'; confFb.textContent='Confirmation does not match.'; confValid=false; }
            else { confFb.style.display='none'; confValid=true; }
            updateButton();
          }
          confirmInput.addEventListener('input', validateConfirm);

        })();
        </script>
      </div>
    </div>
  </div>

 </div>
<?php include_once('layouts/footer.php'); ?>

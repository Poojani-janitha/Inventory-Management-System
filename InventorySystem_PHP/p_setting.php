<?php
$page_title = 'Profile Settings';
require_once('includes/load.php');
page_require_level(2);

$user = current_user();
$msg = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // Update profile info
    if(isset($_POST['update_profile'])){
        $name = remove_junk($db->escape($_POST['full_name']));
        $username = remove_junk($db->escape($_POST['username']));
        $email = remove_junk($db->escape($_POST['email']));
        $password = $_POST['password'] ?? '';

        $sql = "UPDATE users SET name='{$name}', username='{$username}', email='{$email}'";

        if(!empty($password)){
            $hashed = sha1($password);
            $sql .= ", password='{$hashed}'";
        }

        $sql .= " WHERE id=".$user['id'];

        if($db->query($sql)){
            $msg = display_msg("Profile updated successfully!", "success");
        } else {
            $msg = display_msg("Failed to update profile.", "danger");
        }
    }

    // Update profile photo
    if(isset($_POST['update_photo']) && isset($_FILES['user_image'])){
        $file_name = $_FILES['user_image']['name'];
        $file_tmp = $_FILES['user_image']['tmp_name'];
        $target_dir = "uploads/users/";
        $target_file = $target_dir . basename($file_name);

        if(move_uploaded_file($file_tmp, $target_file)){
            $sql = "UPDATE users SET image='{$file_name}' WHERE id=".$user['id'];
            $db->query($sql);
            $msg = display_msg("Profile photo updated successfully!", "success");
        } else {
            $msg = display_msg("Failed to upload photo.", "danger");
        }
    }

    // Reload user data
    $user = current_user();
}
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
    <div class="panel panel-default">
      <div class="panel-heading" style="background:#6c63ff;color:#fff;">
        <strong><i class="glyphicon glyphicon-cog"></i> Profile Settings</strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4 text-center">
            <img src="uploads/users/<?php echo $user['image'] ?? 'default.png'; ?>" class="img-circle" style="width:150px;height:150px;object-fit:cover;">
            <br><br>
            <form method="POST" enctype="multipart/form-data">
              <input type="file" name="user_image" class="form-control" required>
              <br>
              <button type="submit" name="update_photo" class="btn btn-primary btn-block">Change Photo</button>
            </form>
          </div>

          <div class="col-md-8">
            <form method="POST">
              <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo remove_junk($user['name']); ?>" required>
              </div>
              <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" value="<?php echo remove_junk($user['username']); ?>" required>
              </div>
              <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo remove_junk($user['email']); ?>" required>
              </div>
              <div class="form-group">
                <label>Change Password:</label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password (optional)">
              </div>
              <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  setTimeout(function(){
    $(".alert").fadeOut();
  }, 5000);
});
</script>

<style>
.panel { border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }
.form-control { border-radius: 5px; }
.btn { border-radius: 5px; }
</style>

<?php include_once('layouts/footer.php'); ?>

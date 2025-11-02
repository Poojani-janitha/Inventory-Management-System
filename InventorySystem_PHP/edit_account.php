<?php
  $page_title = 'Edit Account';
  require_once('includes/load.php');
  page_require_level(3);
?>
<?php
  // Fetch user levels from database
  $user_levels = find_all('user_groups');
?>
<?php
  // Update user image
  if(isset($_POST['submit'])) {
    $photo = new Media();
    $user_id = (int)$_POST['user_id'];
    $photo->upload($_FILES['file_upload']);
    if($photo->process_user($user_id)){
      $session->msg('s','Profile photo has been updated successfully.');
      redirect('edit_account.php');
    } else {
      $session->msg('d',join($photo->errors));
      redirect('edit_account.php');
    }
  }
?>
<?php
  // Update user other info
  if(isset($_POST['update'])){
    $req_fields = array('name','username');
    validate_fields($req_fields);
    if(empty($errors)){
      $id = (int)$_SESSION['user_id'];
      $name = remove_junk($db->escape($_POST['name']));
      $username = remove_junk($db->escape($_POST['username']));
      $user_level = (int)$db->escape($_POST['user_level']);
      
      $sql = "UPDATE users SET name ='{$name}', username ='{$username}', user_level ='{$user_level}' WHERE id='{$id}'";
      $result = $db->query($sql);
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Account updated successfully");
        redirect('edit_account.php', false);
      } else {
        $session->msg('d','Sorry, failed to update account!');
        redirect('edit_account.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_account.php',false);
    }
  }
?>
<?php
  // Update password
  if(isset($_POST['change_password'])){
    $req_fields = array('current_password','new_password','confirm_password');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $id = (int)$_SESSION['user_id'];
      $current_password = remove_junk($db->escape($_POST['current_password']));
      $new_password = remove_junk($db->escape($_POST['new_password']));
      $confirm_password = remove_junk($db->escape($_POST['confirm_password']));
      
      // Verify current password
      $current_user = find_by_id('users', $id);
      if(sha1($current_password) !== $current_user['password']){
        $session->msg('d','Current password is incorrect!');
        redirect('edit_account.php', false);
      } else if($new_password !== $confirm_password){
        $session->msg('d','New password and confirm password do not match!');
        redirect('edit_account.php', false);
      } else if(strlen($new_password) < 5){
        $session->msg('d','Password must be at least 8 characters long!');
        redirect('edit_account.php', false);
      } else if(strlen($new_password) > 10){
        $session->msg('d','Password must not exceed 16 characters!');
        redirect('edit_account.php', false);
      } else if(!preg_match('/[A-Z]/', $new_password)){
        $session->msg('d','Password must contain at least one uppercase letter!');
        redirect('edit_account.php', false);
      } else if(!preg_match('/[a-z]/', $new_password)){
        $session->msg('d','Password must contain at least one lowercase letter!');
        redirect('edit_account.php', false);
      } 
      // else if(!preg_match('/[0-9]/', $new_password)){
      //   $session->msg('d','Password must contain at least one number!');
      //   redirect('edit_account.php', false);
      // } 
      
      else if(preg_match('/[#@$%]/', $new_password)){
        $session->msg('d','Password cannot contain special characters (#@$%)!');
        redirect('edit_account.php', false);
      } else if(preg_match('/[^a-zA-Z0-9]/', $new_password)){
        $session->msg('d','Password can only contain letters and numbers!');
        redirect('edit_account.php', false);
      } else {
        $hashed_password = sha1($new_password);
        $sql = "UPDATE users SET password ='{$hashed_password}' WHERE id='{$id}'";
        $result = $db->query($sql);
        if($result && $db->affected_rows() === 1){
          $session->msg('s',"Password changed successfully");
          redirect('edit_account.php', false);
        } else {
          $session->msg('d','Sorry, failed to change password!');
          redirect('edit_account.php', false);
        }
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_account.php',false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>

<style>
  .edit-account-container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 20px;
  }
  
  .page-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
    border-radius: 20px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
  }
  
  .page-header-modern h2 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .page-header-modern h2 i {
    font-size: 36px;
  }
  
  .edit-grid {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 30px;
    align-items: start;
  }
  
  @media (max-width: 992px) {
    .edit-grid {
      grid-template-columns: 1fr;
    }
  }
  
  .profile-photo-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    text-align: center;
    position: sticky;
    top: 20px;
  }
  
  .photo-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 30px;
  }
  
  .current-photo {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    border: 6px solid #f8f9fa;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
  }
  
  .photo-wrapper:hover .current-photo {
    transform: scale(1.05);
    border-color: #667eea;
  }
  
  .photo-overlay-badge {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    font-size: 20px;
  }
  
  .upload-section {
    margin-top: 20px;
  }
  
  .file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
    margin-bottom: 20px;
  }
  
  .file-input-wrapper input[type=file] {
    position: absolute;
    left: -9999px;
  }
  
  .file-input-label {
    display: block;
    padding: 20px;
    background: #f8f9fa;
    border: 3px dashed #dee2e6;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
  }
  
  .file-input-label:hover {
    background: #e9ecef;
    border-color: #667eea;
  }
  
  .file-input-label.has-file {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-color: #667eea;
  }
  
  .file-input-label i {
    font-size: 32px;
    color: #667eea;
    display: block;
    margin-bottom: 10px;
  }
  
  .file-name {
    color: #495057;
    font-weight: 600;
    margin-top: 10px;
  }
  
  .btn-upload {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  
  .btn-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
  }
  
  .btn-upload:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
  }
  
  .info-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }
  
  .card-title {
    font-size: 24px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .card-title i {
    color: #667eea;
    font-size: 28px;
  }
  
  .form-group-modern {
    margin-bottom: 25px;
  }
  
  .form-label-modern {
    display: block;
    color: #495057;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .form-input-modern {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #f8f9fa;
  }
  
  .form-input-modern:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  }
  
  .form-select-modern {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    cursor: pointer;
  }
  
  .form-select-modern:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  }
  
  .button-group {
    display: flex;
    gap: 15px;
    margin-top: 30px;
  }
  
  .btn-modern {
    flex: 1;
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  
  .btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  
  .btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    color: white;
  }
  
  .btn-danger-modern {
    background: #dc3545;
    color: white;
  }
  
  .btn-danger-modern:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
    color: white;
  }
  
  .password-toggle {
    position: relative;
  }
  
  .password-toggle-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    transition: color 0.3s ease;
  }
  
  .password-toggle-icon:hover {
    color: #667eea;
  }
  
  .password-requirements {
    margin-top: 8px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.8;
  }
  
  .password-requirements ul {
    margin: 0;
    padding-left: 20px;
    color: #6c757d;
  }
  
  .password-requirements li {
    margin-bottom: 4px;
  }
</style>

<div class="edit-account-container">
  <div class="page-header-modern">
    <h2>
      <i class="glyphicon glyphicon-user"></i>
      Edit Account Settings
    </h2>
  </div>
  
  <?php echo display_msg($session->msg()); ?>
  
  <div class="edit-grid">
    <!-- Profile Photo Section -->
    <div class="profile-photo-card">
      <div class="photo-wrapper">
        <img class="current-photo" src="uploads/users/<?php echo $user['image'];?>" alt="Profile Photo" id="previewImage">
        <div class="photo-overlay-badge">
          <i class="glyphicon glyphicon-camera"></i>
        </div>
      </div>
      
      <form action="edit_account.php" method="POST" enctype="multipart/form-data" id="photoForm">
        <div class="upload-section">
          <div class="file-input-wrapper">
            <input type="file" name="file_upload" id="fileUpload" accept="image/*" onchange="previewFile()">
            <label for="fileUpload" class="file-input-label" id="fileLabel">
              <i class="glyphicon glyphicon-cloud-upload"></i>
              <strong>Choose New Photo</strong>
              <div class="file-name" id="fileName" style="display: none;"></div>
              <small style="color: #6c757d; margin-top: 8px; display: block;">JPG, PNG, or GIF</small>
            </label>
          </div>
          
          <input type="hidden" name="user_id" value="<?php echo $user['id'];?>">
          <button type="submit" name="submit" class="btn-upload" id="uploadBtn" disabled>
            <i class="glyphicon glyphicon-upload"></i>
            Upload Photo
          </button>
        </div>
      </form>
    </div>
    
    <!-- Right Column -->
    <div>
      <!-- Account Info Section -->
      <div class="info-card">
        <h3 class="card-title">
          <i class="glyphicon glyphicon-edit"></i>
          Account Information
        </h3>
        
        <form method="post" action="edit_account.php?id=<?php echo (int)$user['id'];?>">
          <div class="form-group-modern">
            <label for="name" class="form-label-modern">Full Name</label>
            <input type="text" class="form-input-modern" id="name" name="name" value="<?php echo remove_junk(ucwords($user['name'])); ?>" required>
          </div>
          
          <div class="form-group-modern">
            <label for="username" class="form-label-modern">Username</label>
            <input type="text" class="form-input-modern" id="username" name="username" value="<?php echo remove_junk(ucwords($user['username'])); ?>" required>
          </div>
          
          <div class="form-group-modern">
            <label for="user_level" class="form-label-modern">User Level</label>
            <select class="form-select-modern" id="user_level" name="user_level" required>
              <?php foreach($user_levels as $level): ?>
                <option value="<?php echo $level['group_level']; ?>" 
                  <?php if($level['group_level'] === $user['user_level']) echo 'selected'; ?>>
                  <?php echo remove_junk(ucwords($level['group_name'])); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="button-group">
            <button type="submit" name="update" class="btn-modern btn-primary-modern">
              <i class="glyphicon glyphicon-ok"></i>
              Update Account
            </button>
          </div>
        </form>
      </div>
      
      <!-- Change Password Section -->
      <div class="info-card">
        <h3 class="card-title">
          <i class="glyphicon glyphicon-lock"></i>
          Change Password
        </h3>
        
        <form method="post" action="edit_account.php?id=<?php echo (int)$user['id'];?>">
          <div class="form-group-modern">
            <label for="current_password" class="form-label-modern">Current Password</label>
            <div class="password-toggle">
              <input type="password" class="form-input-modern" id="current_password" name="current_password" required>
              <i class="glyphicon glyphicon-eye-open password-toggle-icon" onclick="togglePassword('current_password')"></i>
            </div>
          </div>
          
          <div class="form-group-modern">
            <label for="new_password" class="form-label-modern">New Password</label>
            <div class="password-toggle">
              <input type="password" class="form-input-modern" id="new_password" name="new_password" required>
              <i class="glyphicon glyphicon-eye-open password-toggle-icon" onclick="togglePassword('new_password')"></i>
            </div>
            <div class="password-requirements">
              <ul>
                <li>5-10 characters long</li>
                <li>At least one uppercase letter (A-Z)</li>
                <li>At least one lowercase letter (a-z)</li>
                <li>Allow numbers not must (0-9)</li>
                <li>No special characters (#@$%) allowed</li>
              </ul>
            </div>
          </div>
          
          <div class="form-group-modern">
            <label for="confirm_password" class="form-label-modern">Confirm New Password</label>
            <div class="password-toggle">
              <input type="password" class="form-input-modern" id="confirm_password" name="confirm_password" required>
              <i class="glyphicon glyphicon-eye-open password-toggle-icon" onclick="togglePassword('confirm_password')"></i>
            </div>
          </div>
          
          <div class="button-group">
            <button type="submit" name="change_password" class="btn-modern btn-danger-modern">
              <i class="glyphicon glyphicon-lock"></i>
              Change Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function previewFile() {
    const fileInput = document.getElementById('fileUpload');
    const preview = document.getElementById('previewImage');
    const fileLabel = document.getElementById('fileLabel');
    const fileName = document.getElementById('fileName');
    const uploadBtn = document.getElementById('uploadBtn');
    
    const file = fileInput.files[0];
    
    if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        preview.src = e.target.result;
      }
      
      reader.readAsDataURL(file);
      
      // Update UI
      fileLabel.classList.add('has-file');
      fileName.style.display = 'block';
      fileName.textContent = file.name;
      uploadBtn.disabled = false;
      
      // Update label content
      fileLabel.querySelector('strong').textContent = 'Photo Selected';
      fileLabel.querySelector('i').className = 'glyphicon glyphicon-ok';
    }
  }
  
  function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling;
    
    if (field.type === 'password') {
      field.type = 'text';
      icon.classList.remove('glyphicon-eye-open');
      icon.classList.add('glyphicon-eye-close');
    } else {
      field.type = 'password';
      icon.classList.remove('glyphicon-eye-close');
      icon.classList.add('glyphicon-eye-open');
    }
  }
  
  // Drag and drop functionality
  const fileLabel = document.getElementById('fileLabel');
  
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    fileLabel.addEventListener(eventName, preventDefaults, false);
  });
  
  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }
  
  ['dragenter', 'dragover'].forEach(eventName => {
    fileLabel.addEventListener(eventName, function() {
      fileLabel.style.borderColor = '#667eea';
      fileLabel.style.background = '#e9ecef';
    }, false);
  });
  
  ['dragleave', 'drop'].forEach(eventName => {
    fileLabel.addEventListener(eventName, function() {
      fileLabel.style.borderColor = '#dee2e6';
      fileLabel.style.background = '#f8f9fa';
    }, false);
  });
  
  fileLabel.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('fileUpload').files = files;
    previewFile();
  }, false);
  
  // Password match indicator
  const newPassword = document.getElementById('new_password');
  const confirmPassword = document.getElementById('confirm_password');
  
  confirmPassword.addEventListener('input', function() {
    if (newPassword.value !== confirmPassword.value && confirmPassword.value !== '') {
      confirmPassword.style.borderColor = '#dc3545';
    } else if (confirmPassword.value !== '') {
      confirmPassword.style.borderColor = '#28a745';
    } else {
      confirmPassword.style.borderColor = '#e9ecef';
    }
  });
</script>

<?php include_once('layouts/footer.php'); ?>
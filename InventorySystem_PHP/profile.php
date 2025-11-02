<?php
  $page_title = 'My Profile';
  require_once('includes/load.php');
  // Check user permission level
  page_require_level(3);
  
  $user_id = (int)$_GET['id'];
  if(empty($user_id)):
    redirect('home.php',false);
  else:
    $user_p = find_by_id('users',$user_id);
  endif;
  
  // Initialize message variable
  $msg = '';
  
  // Handle image upload
  if(isset($_POST['upload_image'])){
    $upload_dir = 'uploads/users/';
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    
    if(!empty($_FILES['profile_image']['name'])){
      $photo = $_FILES['profile_image'];
      $photo_name = $photo['name'];
      $photo_tmp = $photo['tmp_name'];
      $photo_size = $photo['size'];
      $photo_error = $photo['error'];
      
      $photo_ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));
      
      if($photo_error === 0){
        if(in_array($photo_ext, $allowed_types)){
          if($photo_size <= 5000000){ // 5MB max
            $photo_new_name = uniqid('profile_', true) . '.' . $photo_ext;
            $photo_destination = $upload_dir . $photo_new_name;
            
            if(move_uploaded_file($photo_tmp, $photo_destination)){
              // Delete old image if exists and not default
              if($user_p['image'] !== 'no_image.png' && file_exists($upload_dir . $user_p['image'])){
                unlink($upload_dir . $user_p['image']);
              }
              
              // Update database
              $db = new mysqli('localhost', 'root', '', 'inventory_system');
              $stmt = $db->prepare("UPDATE users SET image = ? WHERE id = ?");
              $stmt->bind_param("si", $photo_new_name, $user_id);
              
              if($stmt->execute()){
                $session->msg('s', 'Profile image updated successfully!');
                redirect('profile.php?id='.$user_id, false);
              } else {
                $session->msg('d', 'Failed to update profile image in database.');
              }
              $stmt->close();
              $db->close();
            } else {
              $session->msg('d', 'Failed to upload image.');
            }
          } else {
            $session->msg('d', 'Image size is too large. Maximum 5MB allowed.');
          }
        } else {
          $session->msg('d', 'Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.');
        }
      } else {
        $session->msg('d', 'Error uploading file.');
      }
    }
  }
  
  // Handle account deletion
  if(isset($_POST['delete_account'])){
    $delete_user_id = (int)$_POST['delete_user_id'];
    $confirm_password = remove_junk($db->escape($_POST['confirm_password']));
    
    // Verify it's the logged-in user's account
    if($delete_user_id === $user['id']){
      // Verify password
      $current_user = find_by_id('users', $delete_user_id);
      if(sha1($confirm_password) === $current_user['password']){
        // Delete user image if exists
        $upload_dir = 'uploads/users/';
        if($current_user['image'] !== 'no_image.png' && file_exists($upload_dir . $current_user['image'])){
          unlink($upload_dir . $current_user['image']);
        }
        
        // Delete user from database
        $sql = "DELETE FROM users WHERE id = '{$delete_user_id}'";
        $result = $db->query($sql);
        
        if($result && $db->affected_rows() === 1){
          $session->msg('s', 'Account deleted successfully!');
          // Logout and redirect to login page
          $session->logout();
          redirect('index.php', false);
        } else {
          $session->msg('d', 'Failed to delete account!');
          redirect('profile.php?id='.$delete_user_id, false);
        }
      } else {
        $session->msg('d', 'Incorrect password! Account deletion cancelled.');
        redirect('profile.php?id='.$delete_user_id, false);
      }
    } else {
      $session->msg('d', 'Unauthorized action!');
      redirect('home.php', false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>

<style>
  .profile-container {
    max-width: 1200px;
    margin: 30px auto;
  }
  
  .profile-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
    transition: transform 0.3s ease;
  }
  
  .profile-card:hover {
    transform: translateY(-5px);
  }
  
  .profile-header {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    padding: 40px 20px;
    text-align: center;
    position: relative;
  }
  
  .profile-image-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
  }
  
  .profile-image {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    border: 6px solid rgba(255,255,255,0.3);
    object-fit: cover;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
  }
  
  .profile-image:hover {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.6);
  }
  
  .upload-overlay {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #fff;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
  }
  
  .upload-overlay:hover {
    transform: scale(1.1);
    background: #667eea;
  }
  
  .upload-overlay:hover i {
    color: white;
  }
  
  .upload-overlay i {
    color: #667eea;
    font-size: 20px;
  }
  
  .profile-name {
    color: white;
    font-size: 32px;
    font-weight: 700;
    margin: 15px 0 5px 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
  }
  
  .profile-role {
    color: rgba(255,255,255,0.9);
    font-size: 16px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 2px;
  }
  
  .profile-info {
    background: white;
    padding: 30px;
  }
  
  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .info-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
  }
  
  .info-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
  }
  
  .info-label {
    color: #6c757d;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
  }
  
  .info-value {
    color: #212529;
    font-size: 16px;
    font-weight: 600;
  }
  
  .action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
  }
  
  .btn-modern {
    flex: 1;
    min-width: 200px;
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  
  .btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  
  .btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
  }
  
  .btn-secondary-modern {
    background: #f8f9fa;
    color: #495057;
    border: 2px solid #dee2e6;
  }
  
  .btn-secondary-modern:hover {
    background: #e9ecef;
    border-color: #667eea;
    color: #667eea;
  }
  
  .btn-danger-modern {
    background: #dc3545;
    color: white;
    border: none;
  }
  
  .btn-danger-modern:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
  }
  
  #imageUploadForm {
    display: none;
  }
  
  .modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  
  .modal-backdrop.active {
    display: flex;
  }
  
  .upload-modal {
    background: white;
    padding: 40px;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  }
  
  .upload-modal h3 {
    margin: 0 0 25px 0;
    color: #212529;
    font-size: 24px;
  }
  
  .file-upload-wrapper {
    position: relative;
    margin: 25px 0;
  }
  
  .file-upload-input {
    opacity: 0;
    position: absolute;
    z-index: -1;
  }
  
  .file-upload-label {
    display: block;
    padding: 40px;
    background: #f8f9fa;
    border: 3px dashed #dee2e6;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .file-upload-label:hover {
    background: #e9ecef;
    border-color: #667eea;
  }
  
  .file-upload-label.active {
    background: #667eea;
    border-color: #667eea;
    color: white;
  }
  
  .modal-buttons {
    display: flex;
    gap: 15px;
    margin-top: 25px;
  }
  
  .warning-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .warning-box h4 {
    color: #856404;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .warning-box p {
    color: #856404;
    margin: 0;
    font-size: 14px;
  }
  
  .password-input-wrapper {
    margin: 20px 0;
  }
  
  .password-input-wrapper label {
    display: block;
    margin-bottom: 8px;
    color: #495057;
    font-weight: 600;
  }
  
  .password-input-wrapper input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 16px;
  }
  
  .password-input-wrapper input:focus {
    outline: none;
    border-color: #dc3545;
  }
</style>

<div class="profile-container">
  <?php echo display_msg($session->msg()); ?>
  
  <div class="profile-card">
    <div class="profile-header">
      <div class="profile-image-wrapper">
        <img class="profile-image" src="uploads/users/<?php echo $user_p['image'];?>" alt="Profile Picture">
        <?php if($user_p['id'] === $user['id']): ?>
          <div class="upload-overlay" onclick="openUploadModal()">
            <i class="glyphicon glyphicon-camera"></i>
          </div>
        <?php endif; ?>
      </div>
      <h3 class="profile-name"><?php echo remove_junk(ucwords($user_p['name'])); ?></h3>
      <p class="profile-role">
        <?php 
          $level_names = array(1 => 'Admin', 2 => 'Special User', 3 => 'User');
          echo $level_names[$user_p['user_level']] ?? 'User';
        ?>
      </p>
    </div>
    
    <div class="profile-info">
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Username</div>
          <div class="info-value"><?php echo remove_junk(ucwords($user_p['username'])); ?></div>
        </div>
        
        <div class="info-item">
          <div class="info-label">User Level</div>
          <div class="info-value">
            <?php echo $level_names[$user_p['user_level']] ?? 'User'; ?>
          </div>
        </div>
        
        <div class="info-item">
          <div class="info-label">Status</div>
          <div class="info-value">
            <?php echo ($user_p['status'] == 1) ? 'Active' : 'Inactive'; ?>
          </div>
        </div>
        
        <div class="info-item">
          <div class="info-label">Last Login</div>
          <div class="info-value"><?php echo read_date($user_p['last_login']); ?></div>
        </div>
      </div>
      
      <?php if($user_p['id'] === $user['id']): ?>
        <div class="action-buttons">
          <a href="edit_account.php" class="btn-modern btn-primary-modern">
            <i class="glyphicon glyphicon-edit"></i>
            Edit Profile
          </a>
          <button onclick="openDeleteModal()" class="btn-modern btn-danger-modern">
            <i class="glyphicon glyphicon-trash"></i>
            Delete Account
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal-backdrop">
  <div class="upload-modal">
    <h3>Update Profile Picture</h3>
    <form method="post" enctype="multipart/form-data">
      <div class="file-upload-wrapper">
        <input type="file" name="profile_image" id="profile_image" class="file-upload-input" accept="image/*" required>
        <label for="profile_image" class="file-upload-label" id="fileLabel">
          <i class="glyphicon glyphicon-cloud-upload" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
          <strong>Choose an image</strong> or drag it here
          <br><small style="color: #6c757d; margin-top: 10px; display: block;">JPG, PNG, or GIF (Max 5MB)</small>
        </label>
      </div>
      <div class="modal-buttons">
        <button type="submit" name="upload_image" class="btn-modern btn-primary-modern" style="flex: 1;">
          <i class="glyphicon glyphicon-upload"></i>
          Upload
        </button>
        <button type="button" onclick="closeUploadModal()" class="btn-modern btn-secondary-modern" style="flex: 1;">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="modal-backdrop">
  <div class="upload-modal">
    <h3 style="color: #dc3545;">Delete Account</h3>
    <div class="warning-box">
      <h4>
        <i class="glyphicon glyphicon-exclamation-sign"></i>
        Warning: This action cannot be undone!
      </h4>
      <p>Deleting your account will permanently remove all your data including profile information and images. Please confirm your password to proceed.</p>
    </div>
    <form method="post">
      <div class="password-input-wrapper">
        <label for="confirm_password">Enter Your Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Enter your password to confirm" required>
      </div>
      <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
      <div class="modal-buttons">
        <button type="submit" name="delete_account" class="btn-modern btn-danger-modern" style="flex: 1;">
          <i class="glyphicon glyphicon-trash"></i>
          Yes, Delete My Account
        </button>
        <button type="button" onclick="closeDeleteModal()" class="btn-modern btn-secondary-modern" style="flex: 1;">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openUploadModal() {
    document.getElementById('uploadModal').classList.add('active');
  }
  
  function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('active');
    document.getElementById('profile_image').value = '';
    updateFileLabel();
  }
  
  function openDeleteModal() {
    document.getElementById('deleteModal').classList.add('active');
  }
  
  function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.getElementById('confirm_password').value = '';
  }
  
  // Close modals when clicking outside
  document.getElementById('uploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeUploadModal();
    }
  });
  
  document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeDeleteModal();
    }
  });
  
  // Update label when file is selected
  document.getElementById('profile_image').addEventListener('change', function(e) {
    updateFileLabel();
  });
  
  function updateFileLabel() {
    const input = document.getElementById('profile_image');
    const label = document.getElementById('fileLabel');
    
    if (input.files && input.files[0]) {
      const fileName = input.files[0].name;
      label.innerHTML = `
        <i class="glyphicon glyphicon-ok" style="font-size: 48px; margin-bottom: 15px; display: block; color: #28a745;"></i>
        <strong>${fileName}</strong>
        <br><small style="color: #6c757d; margin-top: 10px; display: block;">Click to change</small>
      `;
      label.classList.add('active');
    } else {
      label.innerHTML = `
        <i class="glyphicon glyphicon-cloud-upload" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
        <strong>Choose an image</strong> or drag it here
        <br><small style="color: #6c757d; margin-top: 10px; display: block;">JPG, PNG, or GIF (Max 5MB)</small>
      `;
      label.classList.remove('active');
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
    fileLabel.addEventListener(eventName, highlight, false);
  });
  
  ['dragleave', 'drop'].forEach(eventName => {
    fileLabel.addEventListener(eventName, unhighlight, false);
  });
  
  function highlight(e) {
    fileLabel.style.borderColor = '#667eea';
    fileLabel.style.background = '#e9ecef';
  }
  
  function unhighlight(e) {
    fileLabel.style.borderColor = '#dee2e6';
    fileLabel.style.background = '#f8f9fa';
  }
  
  fileLabel.addEventListener('drop', handleDrop, false);
  
  function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('profile_image').files = files;
    updateFileLabel();
  }
</script>

<?php include_once('layouts/footer.php'); ?>
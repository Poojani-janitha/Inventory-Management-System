<?php
  $page_title = 'All User';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  // PROCESS FORM FIRST - before any HTML output
  // Include form processing logic
  $groups = find_all('user_groups');
  
  // Handle edit user form submission
  if(isset($_POST['update_user'])) {
    $req_fields = array('name','username','level');
    validate_fields($req_fields);
    if(empty($errors)){
             $id = (int)$_POST['user_id'];
           $name = remove_junk($db->escape($_POST['name']));
       $username = remove_junk($db->escape($_POST['username']));
          $level = (int)$db->escape($_POST['level']);
       $status   = remove_junk($db->escape($_POST['status']));
       
       // Check if username already exists (but not for the current user)
       $username_check = "SELECT id FROM users WHERE username = '{$username}' AND id != '{$id}' LIMIT 1";
       $username_result = $db->query($username_check);
       if($username_result && $db->num_rows($username_result) > 0) {
           $errors[] = "Username '{$username}' already exists. Please choose a different username.";
       }
       
       // Check if new password is provided
       $password_update = "";
       if(!empty($_POST['password'])) {
         $password = remove_junk($db->escape($_POST['password']));
         // Validate password
         if (strlen($password) < 6 || strlen($password) > 10) {
             $errors[] = 'Password must be between 6 and 10 characters';
         }
         if (!preg_match('/[A-Z]/', $password)) {
             $errors[] = 'Password must contain at least one capital letter';
         }
         if (!preg_match('/[0-9]/', $password)) {
             $errors[] = 'Password must contain at least one number';
         }
         if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
             $errors[] = 'Password must contain at least one special character';
         }
         
         if(empty($errors)) {
           $password_hash = sha1($password);
           $password_update = ", password='{$password_hash}'";
         }
       }
       
       if(empty($errors)) {
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}',user_level='{$level}',status='{$status}'{$password_update} WHERE id='{$db->escape($id)}'";
         $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"User account updated successfully!");
            redirect('users.php', false);
            exit();
          } else {
            $session->msg('d',' Sorry failed to update user!');
            redirect('users.php', false);
            exit();
          }
       } else {
         $session->msg("d", $errors);
         redirect('users.php', false);
         exit();
       }
    } else {
      $session->msg("d", $errors);
      redirect('users.php',false);
      exit();
    }
  }
  
  if(isset($_POST['add_user'])){
    // Clear any output buffers to ensure clean redirect
    if (ob_get_level()) {
        ob_end_clean();
    }

    $req_fields = array('full-name','username','password','level' );
    validate_fields($req_fields);

    // Validate password
    $password = $_POST['password'];
    if (strlen($password) < 6 || strlen($password) > 10) {
        $errors[] = 'Password must be between 6 and 10 characters';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one capital letter';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = 'Password must contain at least one special character';
    }

    if(empty($errors)){
            $name   = remove_junk($db->escape($_POST['full-name']));
        $username   = remove_junk($db->escape($_POST['username']));
        $password   = remove_junk($db->escape($_POST['password']));
        $user_level = (int)$db->escape($_POST['level']);
        
        // Check if username already exists
        $username_check = "SELECT id FROM users WHERE username = '{$username}' LIMIT 1";
        $username_result = $db->query($username_check);
        if($username_result && $db->num_rows($username_result) > 0) {
            $errors[] = "Username '{$username}' already exists. Please choose a different username.";
        }
        
        if(empty($errors)) {
            $password = sha1($password);

     // Get the group name for the selected user level
     $user_group = find_by_groupLevel($user_level);
     if (!$user_group) {
       $session->msg('d', 'Invalid user role selected.');
       redirect('users.php', false);
       exit();
     }

         $query = "INSERT INTO users (";
         $query .="name,username,password,user_level,status";
         $query .=") VALUES (";
         $query .=" '{$name}', '{$username}', '{$password}', '{$user_level}','1'";
         $query .=")";
         if($db->query($query)){
           // Simple success flash message
           $session->msg('s', 'User account has been created successfully!');
           redirect('users.php', false);
           exit();
         } else {
           // Simple error flash message
           $session->msg('d', 'Sorry! Failed to create user account.');
           redirect('users.php', false);
           exit();
         }
        } else {
          // Username already exists error
          $session->msg('d', $errors);
          redirect('users.php', false);
          exit();
        }
    } else {
      // Set session flash for validation errors and redirect
      $session->msg('d', 'Please fix the validation errors and try again.');
      redirect('users.php', false);
      exit();
    }
  }
  
  //pull out all user form database
  $all_users = find_all_user();
  //extra add prabashi 
  $msg = $session->msg();
?>
<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/users.css">
<style>
/* Modal Styling */
.modal-content {
  border-radius: 8px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  border: none;
}

.modal-header {
  border-radius: 8px 8px 0 0;
  border-bottom: none;
  padding: 20px;
}

.modal-title {
  font-size: 18px;
  font-weight: 600;
}

.modal-body {
  padding: 25px;
}

.modal-footer {
  border-top: 1px solid #e5e5e5;
  padding: 15px 25px;
  background-color: #f8f9fa;
  border-radius: 0 0 8px 8px;
}

.modal-footer .btn {
  min-width: 100px;
  font-weight: 600;
}

.modal-footer .btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.modal-footer .btn-primary:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
  transform: translateY(-1px);
}

/* Form styling inside modal */
.modal-body .form-group {
  margin-bottom: 20px;
}

.modal-body .form-control {
  border-radius: 5px;
  border: 1px solid #ddd;
  padding: 10px 15px;
  font-size: 14px;
  width: 100% !important;
  box-sizing: border-box !important;
  min-width: 0 !important;
  height: auto !important;
  min-height: 40px !important;
}

/* Specific styling for select dropdowns to fix text truncation */
.modal-body select.form-control {
  height: 42px !important;
  line-height: 1.5 !important;
  padding: 8px 30px 8px 15px !important;
  appearance: none !important;
  -webkit-appearance: none !important;
  -moz-appearance: none !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
  background-position: right 10px center !important;
  background-repeat: no-repeat !important;
  background-size: 16px 12px !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  overflow: hidden !important;
}

/* Ensure select options are properly displayed */
.modal-body select.form-control option {
  padding: 8px 15px !important;
  font-size: 14px !important;
  line-height: 1.5 !important;
  white-space: normal !important;
}

.modal-body .form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.modal-body label {
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

/* Backdrop styling */
.modal-backdrop.in {
  opacity: 0.6;
}
</style>
<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>
<!-- include add user form at top of the users page -->
<?php include_once('add_user_form.php'); ?>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="editUserModalLabel">
          <i class="glyphicon glyphicon-edit"></i> Edit User
        </h4>
      </div>
      <div class="modal-body">
        <form method="post" action="users.php" id="editUserForm">
          <input type="hidden" name="user_id" id="edit_user_id">
          
          <div class="form-group">
            <label for="edit_name">Name</label>
            <input type="text" class="form-control" name="name" id="edit_name" required>
          </div>
          
          <div class="form-group">
            <label for="edit_username">Username</label>
            <input type="text" class="form-control" name="username" id="edit_username" required>
          </div>
          
          <div class="form-group">
            <label for="edit_level">User Role</label>
            <select class="form-control" name="level" id="edit_level" required>
              <?php foreach ($groups as $group ):?>
               <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
              <?php endforeach;?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="edit_status">Status</label>
            <select class="form-control" name="status" id="edit_status" required>
              <option value="1">Active</option>
              <option value="0">Deactive</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="edit_password">New Password <small class="text-muted">(Optional - leave blank to keep current)</small></label>
            <input type="password" class="form-control" name="password" id="edit_password" placeholder="Enter new password or leave blank">
            <small class="form-text text-muted">
              If changing password: 6-10 characters, 1 uppercase, 1 number, 1 special character
            </small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" form="editUserForm" name="update_user" class="btn btn-primary">
          <i class="glyphicon glyphicon-floppy-disk"></i> Update User
        </button>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Users</span>
       </strong>
      </div>
     <div class="panel-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center" style="width: 50px;">#</th>
            <th>Name </th>
            <th>Username</th>
            <th class="text-center" style="width: 15%;">User Role</th>
            <th class="text-center" style="width: 10%;">Status</th>
            <th style="width: 20%;">Last Login</th>
            <th class="text-center" style="width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($all_users as $a_user): ?>
          <tr>
           <td class="text-center"><?php echo count_id();?></td>
           <td><?php echo remove_junk($a_user['name'])?></td>
           <td><?php echo remove_junk($a_user['username'])?></td>
           <td class="text-center"><?php echo remove_junk($a_user['group_name'])?></td>
           <td class="text-center">
           <?php if($a_user['status'] === '1'): ?>
            <span class="label label-success"><?php echo "Active"; ?></span>
          <?php else: ?>
            <span class="label label-danger"><?php echo "Deactive"; ?></span>
          <?php endif;?>
           </td>
           <td><?php echo read_date($a_user['last_login'])?></td>
           <td class="text-center">
             <div class="btn-group">
                <button type="button" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" 
                        onclick="showEditForm(<?php echo (int)$a_user['id'];?>, '<?php echo addslashes($a_user['name']);?>', '<?php echo addslashes($a_user['username']);?>', <?php echo (int)$a_user['user_level'];?>, <?php echo (int)$a_user['status'];?>)">
                  <i class="glyphicon glyphicon-pencil"></i>
                </button>
                <a href="delete_user.php?id=<?php echo (int)$a_user['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                  <i class="glyphicon glyphicon-remove"></i>
                </a>
                </div>
           </td>
          </tr>
        <?php endforeach;?>
       </tbody>
     </table>
     </div>
    </div>
  </div>
</div>
<script>
function showEditForm(userId, name, username, level, status) {
  // Populate the form fields
  document.getElementById('edit_user_id').value = userId;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_username').value = username;
  document.getElementById('edit_level').value = level;
  document.getElementById('edit_status').value = status;
  document.getElementById('edit_password').value = ''; // Clear password field
  
  // Update the modal title
  document.getElementById('editUserModalLabel').innerHTML = '<i class="glyphicon glyphicon-edit"></i> Edit User: ' + name;
  
  // Show the modal
  $('#editUserModal').modal('show');
}

// Initialize tooltips
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<?php include_once('layouts/footer.php'); ?>

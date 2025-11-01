<?php
  /**
   * add_user_form.php
   * This file contains only the form and processing logic for adding a user.
   * It is intended to be included by pages that have already loaded
   * `includes/load.php` and set permission checks (so it does NOT include
   * header/footer or re-require the environment).
   */

  // Make sure the environment is present; if not, bail early.
  if(!function_exists('find_all')){
    return;
  }

  // Retrieve groups for the role select
  $groups = find_all('user_groups');

  // Form processing: use current script name for redirects so that this
  // include works both when loaded from add_user.php and when loaded from users.php
  $self = basename($_SERVER['PHP_SELF']);

  if(isset($_POST['add_user'])){

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
       $password = sha1($password);

    // Get the group name for the selected user level
    $user_group = find_by_groupLevel($user_level);
    if (!$user_group) {
      $session->msg('d', 'Invalid user role selected.');
      redirect($self, false);
      return;
    }
    $group_name = ucwords($user_group['group_name']);

        $query = "INSERT INTO users (";
        $query .="name,username,password,user_level,status";
        $query .=") VALUES (";
        $query .=" '{$name}', '{$username}', '{$password}', '{$user_level}','1'";
        $query .=")";
        if($db->query($query)){
          // Simple success flash message
          $session->msg('s', 'User added successfully');
          redirect($self, false);
        } else {
          // Simple error flash message
          $session->msg('d', 'Failed to create user account.');
          redirect($self, false);
        }
   } else {
     // Set session flash for validation errors and redirect
     $session->msg('d', 'Validation error: please check the form fields.');
     redirect($self,false);
   }
 }
?>

  

  <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-plus"></span>
          <span>Add New User</span>
       </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-6">
          <form method="post" action="<?php echo basename($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="full-name" placeholder="Full Name">
            </div>
            <style>
                .validation-message {
                    margin-top: 5px;
                    font-size: 0.875rem;
                    transition: all 0.3s ease;
                }
                .success-message {
                    color: #28a745;
                }
                .error-message {
                    color: #dc3545;
                }
                .form-control.is-valid {
                    border-color: #28a745;
                    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
                    background-repeat: no-repeat;
                    background-position: right calc(0.375em + 0.1875rem) center;
                    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                }
                .form-control.is-invalid {
                    border-color: #dc3545;
                    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23dc3545' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E");
                    background-repeat: no-repeat;
                    background-position: right calc(0.375em + 0.1875rem) center;
                    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                }
            </style>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username">
                <div id="usernameMessage" class="validation-message"></div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                <small class="form-text text-muted mb-2">
                    Password requirements:
                    <ul class="mt-1 mb-0">
                        <li>6-10 characters long</li>
                        <li>At least one capital letter</li>
                        <li>At least one number</li>
                        <li>At least one special character</li>
                    </ul>
                </small>
                <div id="passwordMessage" class="validation-message"></div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const usernameInput = document.getElementById('username');
                const passwordInput = document.getElementById('password');
                const usernameError = document.getElementById('usernameError');
                const passwordError = document.getElementById('passwordError');
                const submitButton = document.querySelector('button[type="submit"]');
                let usernameValid = false;
                let passwordValid = false;

                const usernameMessage = document.getElementById('usernameMessage');
                const passwordMessage = document.getElementById('passwordMessage');

                // Username validation
                let usernameTimeout = null;
                usernameInput.addEventListener('input', function() {
                    clearTimeout(usernameTimeout);
                    const username = this.value.trim();
                    
                    if (username.length === 0) {
                        this.classList.remove('is-valid', 'is-invalid');
                        usernameMessage.textContent = '';
                        usernameMessage.className = 'validation-message';
                        usernameValid = false;
                        updateSubmitButton();
                        return;
                    }

                    usernameTimeout = setTimeout(() => {
                        const formData = new FormData();
                        formData.append('username', username);

                        fetch('check_username.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                usernameInput.classList.remove('is-valid');
                                usernameInput.classList.add('is-invalid');
                                usernameMessage.textContent = '❌ Username already exists';
                                usernameMessage.className = 'validation-message error-message';
                                usernameValid = false;
                            } else {
                                usernameInput.classList.remove('is-invalid');
                                usernameInput.classList.add('is-valid');
                                usernameMessage.textContent = '✓ Username is available';
                                usernameMessage.className = 'validation-message success-message';
                                usernameValid = true;
                            }
                            updateSubmitButton();
                        });
                    }, 300); // Debounce delay
                });

                // Password validation
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const errors = [];
                    let requirements = [];
                    
                    requirements.push({
                        met: password.length >= 6 && password.length <= 10,
                        text: '6-10 characters'
                    });
                    requirements.push({
                        met: /[A-Z]/.test(password),
                        text: 'capital letter'
                    });
                    requirements.push({
                        met: /[0-9]/.test(password),
                        text: 'number'
                    });
                    requirements.push({
                        met: /[!@#$%^&*(),.?":{}|<>]/.test(password),
                        text: 'special character'
                    });

                    const failedRequirements = requirements.filter(req => !req.met);
                    
                    if (failedRequirements.length > 0) {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                        passwordMessage.textContent = '❌ Missing: ' + failedRequirements.map(r => r.text).join(', ');
                        passwordMessage.className = 'validation-message error-message';
                        passwordValid = false;
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                        passwordMessage.textContent = '✓ Password meets all requirements';
                        passwordMessage.className = 'validation-message success-message';
                        passwordValid = true;
                    }
                    updateSubmitButton();
                });

                function updateSubmitButton() {
                    submitButton.disabled = !(usernameValid && passwordValid);
                }
            });
            </script>
                        <div class="form-group">
                            <label for="level">User Role</label>
                                <select class="form-control" name="level" id="level">
                                    <?php foreach ($groups as $group ):?>
                                     <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                                <?php endforeach;?>
                                </select>
                                <div id="roleAccess" class="validation-message" style="margin-top:8px;"></div>
                        </div>

                        <script>
                        (function(){
                            var levelSelect = document.getElementById('level');
                            var roleAccess = document.getElementById('roleAccess');

                            function renderAccess(data){
                                if(!data || !Array.isArray(data.access) || data.access.length === 0){
                                    roleAccess.innerHTML = '<span class="error-message">No pages assigned to this role.</span>';
                                    return;
                                }
                                var html = '<div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">';
                                data.access.forEach(function(item){
                                    html += '<a href="'+item.url+'" class="btn btn-xs" style="background:#e9f7ef;color:#155724;border:1px solid #c3eed1;border-radius:12px;padding:6px 10px;text-decoration:none;">'+
                                                    '<i class="glyphicon glyphicon-ok-circle" style="margin-right:6px;color:#28a745"></i>'+item.label+'</a>';
                                });
                                html += '</div>';
                                roleAccess.innerHTML = html;
                            }

                            function fetchAccess(level){
                                var params = new URLSearchParams();
                                params.append('level', level);
                                fetch('role_access.php?'+params.toString(), {method: 'GET'})
                                    .then(function(r){ return r.json(); })
                                    .then(function(d){ renderAccess(d); })
                                    .catch(function(){ roleAccess.innerHTML = '<span class="error-message">Unable to load role access.</span>'; });
                            }

                            if(levelSelect){
                                // fetch initially for the default selected option
                                fetchAccess(levelSelect.value);
                                levelSelect.addEventListener('change', function(){
                                    fetchAccess(this.value);
                                });
                            }
                        })();
                        </script>
            <div class="form-group clearfix">
              <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </div>
        </form>
        </div>

      </div>

    </div>
  </div>

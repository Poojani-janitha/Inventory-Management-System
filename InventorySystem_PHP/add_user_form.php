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

  // Note: Form processing has been moved to users.php to avoid header issues
  // This file now only contains the form HTML and JavaScript
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
            // Simple form validation without AJAX calls
            document.addEventListener('DOMContentLoaded', function() {
                const submitButton = document.getElementById('submitBtn');
                
                // Ensure submit button is always enabled
                if (submitButton) {
                    submitButton.disabled = false;
                }
                
                // Simple client-side validation feedback (optional)
                const usernameInput = document.getElementById('username');
                const passwordInput = document.getElementById('password');
                const usernameMessage = document.getElementById('usernameMessage');
                const passwordMessage = document.getElementById('passwordMessage');

                if (usernameInput && usernameMessage) {
                    usernameInput.addEventListener('input', function() {
                        const username = this.value.trim();
                        if (username.length >= 3) {
                            usernameMessage.textContent = '✓ Username looks good';
                            usernameMessage.className = 'validation-message success-message';
                        } else if (username.length > 0) {
                            usernameMessage.textContent = 'Username should be at least 3 characters';
                            usernameMessage.className = 'validation-message error-message';
                        } else {
                            usernameMessage.textContent = '';
                        }
                    });
                }

                if (passwordInput && passwordMessage) {
                    passwordInput.addEventListener('input', function() {
                        const password = this.value;
                        if (password.length >= 6 && password.length <= 10 && 
                            /[A-Z]/.test(password) && /[0-9]/.test(password) && 
                            /[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                            passwordMessage.textContent = '✓ Password meets requirements';
                            passwordMessage.className = 'validation-message success-message';
                        } else if (password.length > 0) {
                            passwordMessage.textContent = 'Check password requirements below';
                            passwordMessage.className = 'validation-message error-message';
                        } else {
                            passwordMessage.textContent = '';
                        }
                    });
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
                        // Simple role description without AJAX
                        document.addEventListener('DOMContentLoaded', function(){
                            var levelSelect = document.getElementById('level');
                            var roleAccess = document.getElementById('roleAccess');

                            function showRoleInfo(level) {
                                var info = '';
                                switch(level) {
                                    case '1':
                                        info = '<span class="success-message">✓ Admin - Full system access</span>';
                                        break;
                                    case '2':
                                        info = '<span class="success-message">✓ Staff - Limited access to products and categories</span>';
                                        break;
                                    case '3':
                                        info = '<span class="success-message">✓ Finance - Access to sales and reports</span>';
                                        break;
                                    default:
                                        info = '<span class="error-message">Select a role</span>';
                                }
                                roleAccess.innerHTML = info;
                            }

                            if(levelSelect && roleAccess){
                                showRoleInfo(levelSelect.value);
                                levelSelect.addEventListener('change', function(){
                                    showRoleInfo(this.value);
                                });
                            }
                        });
                        </script>
            <div class="form-group clearfix">
              <button type="submit" name="add_user" class="btn btn-primary" id="submitBtn">Add User</button>
            </div>
        </form>
        </div>

      </div>

    </div>
  </div>

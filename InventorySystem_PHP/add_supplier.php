<?php
$page_title = 'Add Supplier';
require_once('includes/load.php');
page_require_level(1);

// Function to get next supplier ID
function get_next_supplier_id() {
  global $db;
  $result = $db->query("SELECT s_id FROM supplier_info ORDER BY LENGTH(s_id), s_id DESC LIMIT 1");
  if ($db->num_rows($result) > 0) {
    $last_id = $db->fetch_assoc($result)['s_id'];
    $num = intval(substr($last_id, 1)); // remove 's' and convert to number
    $next_id = 's' . ($num + 1);
  } else {
    $next_id = 's1';
  }
  return $next_id;
}

$next_id = get_next_supplier_id();
$message = "";

// Handle add supplier form submission
if (isset($_POST['add_supplier'])) {
  $s_id = remove_junk($db->escape($_POST['s_id']));
  $s_name = trim(remove_junk($db->escape($_POST['s_name'])));
  $address = trim(remove_junk($db->escape($_POST['address'])));
  $contact_number = trim(remove_junk($db->escape($_POST['contact_number'])));
  $email = trim(remove_junk($db->escape($_POST['email'])));

  // Validation
  if (empty($s_name) || empty($address) || empty($contact_number) || empty($email)) {
    $message = "<div class='alert alert-danger'>Please fill all fields.</div>";
  } elseif (!preg_match("/^[a-zA-Z\s]{2,50}$/", $s_name)) {
    $message = "<div class='alert alert-danger'>Supplier name must be 2-50 letters only.</div>";
  } elseif (strlen($address) > 255) {
    $message = "<div class='alert alert-danger'>Address is too long (max 255 characters).</div>";
  } elseif (!preg_match("/^\+94\d{9}$/", $contact_number)) {
    $message = "<div class='alert alert-danger'>Contact number must start with +94 followed by 9 digits (e.g., +94771234567).</div>";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "<div class='alert alert-danger'>Enter a valid email address.</div>";
  } else {
    // Check if email already exists
    $check_email = $db->query("SELECT * FROM supplier_info WHERE email='{$email}' LIMIT 1");
    if ($db->num_rows($check_email) > 0) {
      $message = "<div class='alert alert-danger'>Email already exists.</div>";
    } else {
      $query = "INSERT INTO supplier_info (s_id, s_name, address, contact_number, email)
                VALUES ('{$s_id}', '{$s_name}', '{$address}', '{$contact_number}', '{$email}')";
      if ($db->query($query)) {
        $message = "<div class='alert alert-success' id='auto-hide'>Supplier added successfully!</div>";
        $next_id = get_next_supplier_id();
      } else {
        $message = "<div class='alert alert-danger'>Failed to add supplier.</div>";
      }
    }
  }
}

// Handle supplier update
if (isset($_POST['update_supplier'])) {
  $s_id = remove_junk($db->escape($_POST['s_id']));
  $s_name = trim(remove_junk($db->escape($_POST['s_name'])));
  $address = trim(remove_junk($db->escape($_POST['address'])));
  $contact_number = trim(remove_junk($db->escape($_POST['contact_number'])));
  $email = trim(remove_junk($db->escape($_POST['email'])));

  $update_sql = "UPDATE supplier_info SET 
                  s_name='{$s_name}', 
                  address='{$address}', 
                  contact_number='{$contact_number}', 
                  email='{$email}' 
                 WHERE s_id='{$s_id}'";

  if ($db->query($update_sql)) {
    $message = "<div class='alert alert-success' id='auto-hide'>Supplier updated successfully!</div>";
  } else {
    $message = "<div class='alert alert-danger'>Failed to update supplier.</div>";
  }
}

// Get all suppliers to display
$suppliers = find_all('supplier_info');
?>

<?php include_once('layouts/header.php'); ?>

<!-- Professional CSS Styling -->
<style>
/* Panel Heading Gradient */
.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
  padding: 15px 20px;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

.panel-heading strong {
  font-size: 18px;
}

/* Panel Shadow */
.panel {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
  border: none;
}

.panel-body {
  padding: 20px;
}

/* Table Header Gradient */
.table thead tr {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.table thead th {
  color: white !important;
  border-bottom: 2px solid #dee2e6;
  vertical-align: middle;
  font-size: 13px;
  padding: 12px 8px;
  border: none;
}

/* Table Hover Effect */
.table-hover tbody tr:hover {
  background-color: #f1f8ff;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(0,0,0,.02);
}

.table tbody td {
  vertical-align: middle;
  padding: 12px 8px;
}

/* Button Styling with Gradients */
.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  padding: 10px 20px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.btn-info {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
  border: none;
  transition: all 0.3s ease;
}

.btn-info:hover {
  background: linear-gradient(135deg, #44a08d 0%, #4ecdc4 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(78, 205, 196, 0.3);
}

.btn-success {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
  border: none;
}

.btn-success:hover {
  background: linear-gradient(135deg, #a8e063 0%, #56ab2f 100%);
}

.btn-default {
  background: #e0e0e0;
  border: none;
}

.btn-default:hover {
  background: #d0d0d0;
}

/* Form Control Focus */
.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-control {
  border-radius: 5px;
  border: 1px solid #ddd;
  padding: 10px;
  transition: all 0.3s ease;
}

/* Label Styling */
label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 8px;
}

/* Alert Styling */
.alert {
  border-radius: 5px;
  border: none;
  padding: 15px;
  margin-bottom: 20px;
}

.alert-success {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
  color: white;
}

.alert-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  color: white;
}

/* Modal Header Gradient */
.modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
}

.modal-header .close {
  color: white;
  opacity: 0.8;
}

.modal-header .close:hover {
  opacity: 1;
}

.modal-header h4 {
  color: white;
  margin: 0;
}

.modal-title {
  font-weight: 600;
}

/* Scrollbar styling */
::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #667eea;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #764ba2;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .panel-body {
    padding: 15px;
  }
}
</style>

<div class="row">
  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-plus"></span> Add New Supplier</strong>
      </div>
      <div class="panel-body">
        <?php echo $message; ?>
        <form method="POST" action="add_supplier.php" id="supplierForm">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier ID</label>
                <input type="text" name="s_id" class="form-control" value="<?php echo $next_id; ?>" readonly>
              </div>
              <div class="form-group">
                <label>Supplier Name</label>
                <input type="text" name="s_name" class="form-control" placeholder="Enter supplier name" required>
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" placeholder="Enter address" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="+94771234567" required>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="example@example.com" required>
              </div>
            </div>
          </div>
          <button type="submit" name="add_supplier" class="btn btn-primary">
            <span class="glyphicon glyphicon-plus"></span> Add Supplier
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Supplier table with Edit button -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-list"></span> Supplier List</strong>
      </div>
      <div class="panel-body" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Address</th>
              <th>Contact</th>
              <th>Email</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($suppliers as $supp): ?>
            <tr>
              <td><?php echo remove_junk($supp['s_id']); ?></td>
              <td><?php echo remove_junk($supp['s_name']); ?></td>
              <td><?php echo remove_junk($supp['address']); ?></td>
              <td><?php echo remove_junk($supp['contact_number']); ?></td>
              <td><?php echo remove_junk($supp['email']); ?></td>
              <td class="text-center">
                <button class="btn btn-info btn-sm edit-btn" 
                        data-id="<?php echo $supp['s_id']; ?>"
                        data-name="<?php echo $supp['s_name']; ?>"
                        data-address="<?php echo $supp['address']; ?>"
                        data-contact="<?php echo $supp['contact_number']; ?>"
                        data-email="<?php echo $supp['email']; ?>"
                        title="Edit Supplier">
                  <span class="glyphicon glyphicon-edit"></span> Edit
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="add_supplier.php" id="editSupplierForm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="editSupplierLabel">Edit Supplier</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="s_id" id="edit_s_id">
          <div class="form-group">
            <label>Supplier Name</label>
            <input type="text" name="s_name" class="form-control" id="edit_s_name" required>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" id="edit_address" required>
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number" class="form-control" id="edit_contact_number" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" id="edit_email" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update_supplier" class="btn btn-success">
            <span class="glyphicon glyphicon-ok"></span> Update Supplier
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const contactInput = document.getElementById("contact_number");

  // Add supplier form validation
  document.getElementById("supplierForm").addEventListener("submit", function(e) {
    const contactValue = contactInput.value.trim();
    if (!/^\+94\d{9}$/.test(contactValue)) {
      e.preventDefault();
      alert("Contact number must start with +94 followed by 9 digits (e.g., +94771234567).");
      contactInput.focus();
    }
  });

  // Auto-hide messages
  const alertBox = document.getElementById('auto-hide');
  if (alertBox && alertBox.classList.contains('alert-success')) {
    setTimeout(() => {
      alertBox.style.transition = "opacity 0.5s ease";
      alertBox.style.opacity = "0";
      setTimeout(() => alertBox.remove(), 500);
    }, 3000);
  }

  // Edit button click
  const editButtons = document.querySelectorAll('.edit-btn');
  editButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      document.getElementById('edit_s_id').value = this.dataset.id;
      document.getElementById('edit_s_name').value = this.dataset.name;
      document.getElementById('edit_address').value = this.dataset.address;
      document.getElementById('edit_contact_number').value = this.dataset.contact;
      document.getElementById('edit_email').value = this.dataset.email;
      $('#editSupplierModal').modal('show');
    });
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>
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

// Handle form submission
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
  } elseif (!preg_match("/^\+94\d{9}$/", $contact_number)) { // <-- +94 format
    $message = "<div class='alert alert-danger'>Contact number must start with +94 followed by 9 digits (e.g., +94771234567).</div>";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "<div class='alert alert-danger'>Enter a valid email address.</div>";
  } else {
    // Check if email already exists
    $check_email = $db->query("SELECT * FROM supplier_info WHERE email='{$email}' LIMIT 1");
    if ($db->num_rows($check_email) > 0) {
      $message = "<div class='alert alert-danger'>Email already exists.</div>";
    } else {
      // Insert into database
      $query = "INSERT INTO supplier_info (s_id, s_name, address, contact_number, email)
                VALUES ('{$s_id}', '{$s_name}', '{$address}', '{$contact_number}', '{$email}')";
      if ($db->query($query)) {
        $message = "<div class='alert alert-success' id='auto-hide'>Supplier added successfully!</div>";
        $next_id = get_next_supplier_id(); // update ID after adding
      } else {
        $message = "<div class='alert alert-danger'>Failed to add supplier.</div>";
      }
    }
  }
}

// Get all suppliers to display
$suppliers = find_all('supplier_info');
?>

<?php include_once('layouts/header.php'); ?>

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
                <input type="text" name="s_name" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" required>
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
          <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Supplier table below form -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-list"></span> Supplier List</strong>
      </div>
      <div class="panel-body" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Address</th>
              <th>Contact</th>
              <th>Email</th>
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
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Client-side validation and auto-hide message -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("supplierForm");
  const contactInput = document.getElementById("contact_number");

  form.addEventListener("submit", function(e) {
    const contactValue = contactInput.value.trim();
    if (!/^\+94\d{9}$/.test(contactValue)) {
      e.preventDefault();
      alert("Contact number must start with +94 followed by 9 digits (e.g., +94771234567).");
      contactInput.focus();
    }
  });

  // Auto-hide success message
  const alertBox = document.getElementById('auto-hide');
  if (alertBox && alertBox.classList.contains('alert-success')) {
    setTimeout(() => {
      alertBox.style.transition = "opacity 0.5s ease";
      alertBox.style.opacity = "0";
      setTimeout(() => alertBox.remove(), 500);
    }, 3000);
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>

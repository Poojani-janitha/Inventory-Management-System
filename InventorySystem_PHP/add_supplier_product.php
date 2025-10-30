<?php
$page_title = 'Add Supplier Product';
require_once('includes/load.php');
page_require_level(2);

// Get all suppliers and categories
$all_suppliers = find_all('supplier_info');
$all_categories = find_all('categories');
$message = "";

// Handle form submission
if (isset($_POST['add_product'])) {
    $supplier_id = remove_junk($db->escape($_POST['supplier']));
    $category_name = remove_junk($db->escape($_POST['category']));
    $product_name = remove_junk($db->escape($_POST['product_name']));
    $price = remove_junk($db->escape($_POST['price']));

    if (empty($supplier_id) || empty($category_name) || empty($product_name) || empty($price)) {
        $message = "<div class='alert alert-danger'>Please fill all fields.</div>";
    } else {
        $query = "INSERT INTO supplier_product (s_id, category_name, product_name, price) 
                  VALUES ('{$supplier_id}', '{$category_name}', '{$product_name}', '{$price}')";
        if ($db->query($query)) {
            $message = "<div class='alert alert-success' id='auto-hide'>Product added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add product. Maybe it already exists for this supplier.</div>";
        }
    }
}

// Fetch all supplier products for table
$supplier_products = $db->query("SELECT sp.*, si.s_name FROM supplier_product sp 
                                 JOIN supplier_info si ON sp.s_id = si.s_id
                                 ORDER BY sp.s_id, sp.product_name");
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
}

.btn-info:hover {
  background: linear-gradient(135deg, #44a08d 0%, #4ecdc4 100%);
}

.btn-success {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
  border: none;
}

.btn-success:hover {
  background: linear-gradient(135deg, #a8e063 0%, #56ab2f 100%);
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
  height: auto;
  line-height: 1.5;
}

select.form-control {
  padding: 8px 10px;
  height: 42px;
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
  <div class="col-md-12">
    <?php echo $message; ?>
  </div>
</div>

<!-- Form -->
<div class="row">
  <div class="col-md-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-plus"></span> Add Supplier Product</strong>
      </div>
      <div class="panel-body">
        <form method="POST" action="add_supplier_product.php">
          <div class="form-group">
            <label>Supplier</label>
            <select name="supplier" class="form-control" required>
              <option value="">Select Supplier</option>
              <?php foreach ($all_suppliers as $supplier): ?>
                <option value="<?php echo $supplier['s_id']; ?>">
                  <?php echo $supplier['s_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control" required>
              <option value="">Select Category</option>
              <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo $cat['category_name']; ?>">
                  <?php echo $cat['category_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" placeholder="Enter product name" required>
          </div>

          <div class="form-group">
            <label>Unit Price</label>
            <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price" required>
          </div>

          <button type="submit" name="add_product" class="btn btn-primary btn-block">
            <span class="glyphicon glyphicon-plus"></span> Add Product
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Supplier Products Table -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-list"></span> Supplier Product List</strong>
      </div>
      <div class="panel-body" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th>Supplier</th>
              <th>Category</th>
              <th>Product Name</th>
              <th>Unit Price</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $db->fetch_assoc($supplier_products)): ?>
            <tr>
              <td><?php echo remove_junk($row['s_name']); ?></td>
              <td><?php echo remove_junk($row['category_name']); ?></td>
              <td><?php echo remove_junk($row['product_name']); ?></td>
              <td>Rs. <?php echo number_format($row['price'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Auto-hide success message -->
<script>
document.addEventListener("DOMContentLoaded", function() {
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
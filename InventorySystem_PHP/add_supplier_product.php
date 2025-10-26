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
            <input type="text" name="product_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Unit Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
          </div>

          <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
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
          <thead class="thead-dark">
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
              <td><?php echo number_format($row['price'], 2); ?></td>
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

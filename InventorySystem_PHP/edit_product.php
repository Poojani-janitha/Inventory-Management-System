<?php
  $page_title = 'Edit Product';
  require_once('includes/load.php');
  page_require_level(2);
  //extra add prabashi 
    $msg = $session->msg();

  // Get product by ID
  $p_id = $_GET['id'] ?? '';
  $product = find_by_id('product', $p_id);

  if (!$product) {
    $session->msg("d", "Missing or invalid product ID.");
    redirect('product.php');
  }

  $all_categories = find_all('categories');
  $all_suppliers = find_all('supplier_info');

  // ===== When Update button clicked =====
  if (isset($_POST['update_product'])) {
    $req_fields = array('product_name', 'product_categorie', 'product_supplier', 'product_quantity', 'buying_price', 'selling_price');
    validate_fields($req_fields);

    if (empty($errors)) {
      $p_name     = remove_junk($db->escape($_POST['product_name']));
      $p_cat      = remove_junk($db->escape($_POST['product_categorie']));
      $p_supplier = remove_junk($db->escape($_POST['product_supplier']));
      $p_qty      = remove_junk($db->escape($_POST['product_quantity']));
      $p_buy      = remove_junk($db->escape($_POST['buying_price']));
      $p_sale     = remove_junk($db->escape($_POST['selling_price']));
      $p_expire   = remove_junk($db->escape($_POST['expire_date']));

      $query  = "UPDATE product SET ";
      $query .= "product_name='{$p_name}', ";
      $query .= "category_name='{$p_cat}', ";
      $query .= "s_id='{$p_supplier}', ";
      $query .= "quantity='{$p_qty}', ";
      $query .= "buying_price='{$p_buy}', ";
      $query .= "selling_price='{$p_sale}', ";
      $query .= "expire_date='{$p_expire}' ";
      $query .= "WHERE p_id='{$p_id}'";

      if ($db->query($query)) {
        $session->msg("s", "Product updated successfully!");
        redirect("edit_product.php?id={$p_id}", false); // reload same page
      } else {
        $session->msg("d", "Failed to update product!");
        redirect("edit_product.php?id={$p_id}", false);
      }
    } else {
      $session->msg("d", $errors);
      redirect("edit_product.php?id={$p_id}", false);
    }
  }
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong><span class="glyphicon glyphicon-edit"></span> Update Product</strong>
    </div>
    <div class="panel-body">
      <div class="col-md-8">
        <form method="post" action="edit_product.php?id=<?php echo $product['p_id']; ?>">

          <!-- Product Name -->
          <div class="form-group">
            <label>Product Name</label>
            <input type="text" class="form-control" name="product_name"
              value="<?php echo remove_junk($product['product_name']); ?>" required>
          </div>

          <!-- Category -->
          <div class="form-group">
            <label>Category</label>
            <select class="form-control" name="product_categorie" required>
              <option value="">Select a category</option>
              <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo $cat['category_name']; ?>"
                  <?php if ($product['category_name'] === $cat['category_name']) echo "selected"; ?>>
                  <?php echo remove_junk($cat['category_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Supplier -->
          <div class="form-group">
            <label>Supplier</label>
            <select class="form-control" name="product_supplier" required>
              <option value="">Select a supplier</option>
              <?php foreach ($all_suppliers as $sup): ?>
                <option value="<?php echo $sup['s_id']; ?>"
                  <?php if ($product['s_id'] === $sup['s_id']) echo "selected"; ?>>
                  <?php echo remove_junk($sup['s_id']); ?> 
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Quantity -->
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" class="form-control" name="product_quantity"
              value="<?php echo remove_junk($product['quantity']); ?>" min="0" required>
          </div>

          <!-- Buying & Selling Price -->
          <div class="row">
            <div class="col-md-6">
              <label>Buying Price (LKR)</label>
              <input type="number" step="0.01" class="form-control" name="buying_price"
                value="<?php echo remove_junk($product['buying_price']); ?>" required>
            </div>
            <div class="col-md-6">
              <label>Selling Price (LKR)</label>
              <input type="number" step="0.01" class="form-control" name="selling_price"
                value="<?php echo remove_junk($product['selling_price']); ?>" required>
            </div>
          </div>

          <!-- Expire Date -->
          <div class="form-group" style="margin-top:10px;">
            <label>Expire Date</label>
            <input type="date" class="form-control" name="expire_date"
              value="<?php echo remove_junk($product['expire_date']); ?>">
          </div>

          <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
          <a href="product.php" class="btn btn-default">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

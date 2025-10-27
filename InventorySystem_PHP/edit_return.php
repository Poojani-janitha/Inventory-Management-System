<?php
  $page_title = 'Edit Return';
  require_once('includes/load.php');
  page_require_level(1);
?>
<?php
  // Check if return ID is provided
  if(isset($_GET['id'])){
    $return_id = (int)$_GET['id'];
    
    // Get return details
    $return = find_by_id('return_details', $return_id);
    
    if(!$return){
      $session->msg('d', "Return not found.");
      redirect('returns.php', false);
    }
  }
?>

<?php
  if(isset($_POST['update_return'])){
    $req_fields = array('return_quantity', 'product_id');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $return_id = (int)$_POST['return_id'];
      $old_quantity = (int)$_POST['old_quantity'];
      $new_quantity = (int)$_POST['return_quantity'];
      $old_p_id = remove_junk($db->escape($_POST['old_p_id']));
      $new_p_id = remove_junk($db->escape($_POST['product_id']));
      
      // Get new product details
      $new_product = find_by_id('product', $new_p_id);
      
      if(!$new_product){
        $session->msg('d','Invalid product.');
        redirect('returns.php', false);
      }
      
      // Get old product details
      $old_product = find_by_id('product', $old_p_id);
      
      // Check if quantity is valid for new product
      if($new_quantity > $new_product['quantity']){
        $session->msg('d','Insufficient stock. Only ' . $new_product['quantity'] . ' units available.');
        redirect('edit_return.php?id=' . $return_id, false);
      }
      
      // Update return_details with new product info
      $query = "UPDATE return_details SET p_id = '{$new_p_id}', product_name = '{$new_product['product_name']}', 
                buying_price = '{$new_product['buying_price']}', return_quantity = '{$new_quantity}', 
                s_id = '{$new_product['s_id']}' WHERE return_id = '{$return_id}' LIMIT 1";
      
      if($db->query($query)){
        
        // Restore stock to old product
        if($old_product){
          $old_stock_restored = $old_product['quantity'] + $old_quantity;
          $restore_query = "UPDATE product SET quantity = '{$old_stock_restored}' WHERE p_id = '{$old_p_id}'";
          $db->query($restore_query);
        }
        
        // Reduce stock from new product
        $new_stock = $new_product['quantity'] - $new_quantity;
        $update_query = "UPDATE product SET quantity = '{$new_stock}' WHERE p_id = '{$new_p_id}'";
        $db->query($update_query);
        
        $session->msg('s', "Return updated successfully.");
        redirect('returns.php', false);
      } else {
        $session->msg('d',' Sorry failed to update!');
        redirect('returns.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_return.php?id=' . (int)$return_id, false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>
<?php echo display_msg($msg); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-edit"></span>
          <span>Edit Return #<?php echo $return['return_id']; ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_return.php?id=<?php echo (int)$return['return_id']; ?>" class="clearfix">
          
          <input type="hidden" name="return_id" value="<?php echo (int)$return['return_id']; ?>">
          <input type="hidden" name="old_quantity" value="<?php echo (int)$return['return_quantity']; ?>">
          <input type="hidden" name="old_p_id" value="<?php echo htmlspecialchars($return['p_id']); ?>">
          
          <div class="form-group">
            <label class="control-label">Select Product <span class="text-danger">*</span></label>
            <select class="form-control" name="product_id" id="product_dropdown" required>
              <option value="">-- Select Product --</option>
              <?php
              $all_products = find_all('product');
              if($all_products):
                foreach($all_products as $product):
                  $selected = ($product['p_id'] == $return['p_id']) ? 'selected' : '';
              ?>
              <option value="<?php echo $product['p_id']; ?>" 
                      data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                      data-buying-price="<?php echo $product['buying_price']; ?>"
                      data-quantity="<?php echo $product['quantity']; ?>"
                      data-s-id="<?php echo $product['s_id']; ?>"
                      <?php echo $selected; ?>>
                <?php echo $product['product_name']; ?> (ID: <?php echo $product['p_id']; ?>) - Stock: <?php echo $product['quantity']; ?> units
              </option>
              <?php 
                endforeach;
              endif;
              ?>
            </select>
            <small class="help-block">Select a product to change the return assignment</small>
          </div>
          
          <div class="form-group" id="product_details_display" style="background: #f0f0f0; padding: 15px; border-radius: 5px; display: none;">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Selected Product Name</label>
                  <input type="text" class="form-control" id="product_name_display" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Buying Price (per unit)</label>
                  <input type="text" class="form-control" id="buying_price_display" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Available Stock</label>
                  <input type="text" class="form-control" id="stock_display" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Supplier ID</label>
                  <input type="text" class="form-control" id="supplier_display" readonly>
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <label class="control-label">Return Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="return_quantity" id="return_quantity_input" min="1" value="<?php echo (int)$return['return_quantity']; ?>" required>
                <small class="help-block">Enter the number of units to return</small>
              </div>
              <div class="col-md-6">
                <label class="control-label">Return Date</label>
                <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i:s', strtotime($return['return_date'])); ?>" readonly>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <button type="submit" name="update_return" class="btn btn-primary">
              <span class="glyphicon glyphicon-floppy-disk"></span> Update Return
            </button>
            <a href="returns.php" class="btn btn-default">
              <span class="glyphicon glyphicon-remove"></span> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-info-sign"></span>
          <span>Return Information</span>
        </strong>
      </div>
      <div class="panel-body">
        <p><strong>Return ID:</strong> <?php echo (int)$return['return_id']; ?></p>
        <p><strong>Product:</strong> <?php echo htmlspecialchars($return['product_name']); ?></p>
        <p><strong>Supplier:</strong> <?php echo htmlspecialchars($return['s_id']); ?></p>
        <p><strong>Current Quantity:</strong> <?php echo (int)$return['return_quantity']; ?> units</p>
        <p><strong>Buying Price:</strong> Rs. <?php echo number_format($return['buying_price'], 2); ?></p>
        <p><strong>Return Date:</strong> <?php echo date('d-M-Y H:i', strtotime($return['return_date'])); ?></p>
        <hr>
        <p><strong>Total Return Amount:</strong> <span class="text-danger" style="font-size: 18px; font-weight: bold;">Rs. <?php echo number_format($return['return_quantity'] * $return['buying_price'], 2); ?></span></p>
      </div>
    </div>
  </div>
</div>

<style>
.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5568d3 0%, #654e8f 100%);
}

.panel-info {
  border-color: #5bc0de;
}

.panel-info > .panel-heading {
  background: linear-gradient(135deg, #5bc0de 0%, #46b8da 100%);
  color: white;
}

.help-block {
  color: #6c757d;
  font-size: 0.875em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const productDropdown = document.getElementById('product_dropdown');
  const productDetailsDisplay = document.getElementById('product_details_display');
  const productNameDisplay = document.getElementById('product_name_display');
  const buyingPriceDisplay = document.getElementById('buying_price_display');
  const stockDisplay = document.getElementById('stock_display');
  const supplierDisplay = document.getElementById('supplier_display');
  const returnQuantityInput = document.getElementById('return_quantity_input');
  
  // Initial load - show current product details
  if(productDropdown.value) {
    updateProductDetails();
    productDetailsDisplay.style.display = 'block';
  }
  
  // Handle product dropdown change
  productDropdown.addEventListener('change', function() {
    if(this.value) {
      updateProductDetails();
      productDetailsDisplay.style.display = 'block';
    } else {
      productDetailsDisplay.style.display = 'none';
    }
  });
  
  // Update product details display
  function updateProductDetails() {
    const selectedOption = productDropdown.options[productDropdown.selectedIndex];
    if(selectedOption && selectedOption.value) {
      const productName = selectedOption.dataset.name;
      const buyingPrice = parseFloat(selectedOption.dataset.buyingPrice) || 0;
      const stock = parseInt(selectedOption.dataset.quantity) || 0;
      const sId = selectedOption.dataset.sId || '';
      
      // Update display fields
      productNameDisplay.value = productName;
      buyingPriceDisplay.value = 'Rs. ' + buyingPrice.toFixed(2);
      stockDisplay.value = stock + ' units';
      supplierDisplay.value = sId;
      
      // Validate quantity
      const currentQty = parseInt(returnQuantityInput.value) || 0;
      if(currentQty > stock) {
        alert('Warning: Return quantity (' + currentQty + ') exceeds available stock (' + stock + ' units)');
        returnQuantityInput.value = stock;
      }
    }
  }
  
  // Validate return quantity against stock
  returnQuantityInput.addEventListener('input', function() {
    const selectedOption = productDropdown.options[productDropdown.selectedIndex];
    if(selectedOption && selectedOption.value) {
      const stock = parseInt(selectedOption.dataset.quantity) || 0;
      const qty = parseInt(this.value) || 0;
      
      if(qty > stock) {
        alert('Return quantity cannot exceed available stock (' + stock + ' units)');
        this.value = stock;
      }
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>


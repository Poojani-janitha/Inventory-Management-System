<?php
  $page_title = 'Edit Return';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  $return = find_return_by_id((int)$_GET['id']);
  if(!$return){
    $session->msg("d","Missing Return ID.");
    redirect('returns.php');
  }
?>

<?php
if(isset($_POST['edit_return'])){
  $req_fields = array('product_id','quantity','product_name','buying_price');
  validate_fields($req_fields);
  
  if(empty($errors)){
    $return_id     = (int)$_POST['return_id'];
    $p_id          = remove_junk($db->escape($_POST['product_id']));
    $qty           = (int)remove_junk($db->escape($_POST['quantity']));
    $product_name  = remove_junk($db->escape($_POST['product_name']));
    $buying_price  = (float)remove_junk($db->escape($_POST['buying_price']));
    $s_id          = remove_junk($db->escape($_POST['s_id']));
    
    // Get original return quantity to adjust stock
    $original_return = find_return_by_id($return_id);
    $original_qty = $original_return['return_quantity'];
    
    // Get product details
    $product = find_by_id('product', $p_id);
    if(!$product){
      $session->msg('d','Invalid product ID.');
      redirect('edit_return.php?id='.$return_id, false);
    }
    
    // Calculate stock adjustment
    // First, restore the original return quantity
    $adjusted_stock = $product['quantity'] + $original_qty;
    // Then, subtract the new return quantity
    $new_stock = $adjusted_stock - $qty;
    
    if($new_stock < 0){
      $session->msg('d','Return quantity would result in negative stock.');
      redirect('edit_return.php?id='.$return_id, false);
    }
    
    // Update return details
    $query  = "UPDATE return_details SET ";
    $query .= "p_id = '{$p_id}', ";
    $query .= "s_id = '{$s_id}', ";
    $query .= "product_name = '{$product_name}', ";
    $query .= "buying_price = '{$buying_price}', ";
    $query .= "return_quantity = '{$qty}' ";
    $query .= "WHERE return_id = '{$return_id}'";
    
    if($db->query($query)){
      // Update product stock
      $update_query = "UPDATE product SET quantity = '{$new_stock}' WHERE p_id = '{$p_id}'";
      $db->query($update_query);
      
      $session->msg('s',"Return updated successfully.");
      redirect('returns.php', false);
    } else {
      $session->msg('d','Failed to update return!');
      redirect('edit_return.php?id='.$return_id, false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_return.php?id='.(int)$_GET['id'], false);
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
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-edit"></span>
          <span>Edit Return #<?php echo str_pad($return['return_id'], 4, '0', STR_PAD_LEFT); ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_return.php?id=<?php echo (int)$return['return_id']; ?>">
          <input type="hidden" name="return_id" value="<?php echo (int)$return['return_id']; ?>">
          
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <label for="product_name" class="control-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" 
                       value="<?php echo remove_junk($return['product_name']); ?>" readonly>
                <input type="hidden" name="product_id" value="<?php echo $return['p_id']; ?>">
              </div>
              <div class="col-md-6">
                <label for="buying_price" class="control-label">Buying Price (per unit)</label>
                <input type="number" step="0.01" class="form-control" id="buying_price" name="buying_price" 
                       value="<?php echo $return['buying_price']; ?>" required>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <label for="quantity" class="control-label">Return Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" 
                       value="<?php echo $return['return_quantity']; ?>" min="1" required>
              </div>
              <div class="col-md-4">
                <label for="supplier_id" class="control-label">Supplier ID</label>
                <input type="text" class="form-control" id="supplier_id" name="s_id" 
                       value="<?php echo $return['s_id']; ?>" readonly>
              </div>
              <div class="col-md-4">
                <label for="return_amount" class="control-label">Total Return Amount</label>
                <input type="text" class="form-control" id="return_amount" 
                       value="Rs. <?php echo number_format($return['return_quantity'] * $return['buying_price'], 2); ?>" 
                       readonly style="font-weight: bold; color: #d9534f;">
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <label class="control-label">Category</label>
                <input type="text" class="form-control" 
                       value="<?php echo $return['category_name']; ?>" readonly>
              </div>
              <div class="col-md-6">
                <label class="control-label">Current Stock (After Original Return)</label>
                <input type="text" class="form-control" 
                       value="<?php echo $return['current_stock']; ?> units" readonly>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-info">
                  <strong><i class="glyphicon glyphicon-info-sign"></i> Note:</strong>
                  Changing the return quantity will automatically adjust the product stock accordingly.
                  Original return quantity: <strong><?php echo $return['return_quantity']; ?> units</strong>
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group clearfix">
            <button type="submit" name="edit_return" class="btn btn-primary">
              <span class="glyphicon glyphicon-floppy-disk"></span> Update Return
            </button>
            <a href="returns.php" class="btn btn-danger">
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
        <table class="table table-bordered">
          <tr>
            <td><strong>Return ID:</strong></td>
            <td>#<?php echo str_pad($return['return_id'], 4, '0', STR_PAD_LEFT); ?></td>
          </tr>
          <tr>
            <td><strong>Product ID:</strong></td>
            <td><?php echo $return['p_id']; ?></td>
          </tr>
          <tr>
            <td><strong>Supplier:</strong></td>
            <td><?php echo $return['supplier_name']; ?></td>
          </tr>
          <tr>
            <td><strong>Return Date:</strong></td>
            <td><?php echo date('Y-m-d H:i:s', strtotime($return['return_date'])); ?></td>
          </tr>
        </table>
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

.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  padding: 10px 30px;
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  border: none;
  padding: 10px 30px;
}

.alert-info {
  background-color: #d1ecf1;
  border-color: #bee5eb;
  color: #0c5460;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const quantityInput = document.getElementById('quantity');
  const buyingPriceInput = document.getElementById('buying_price');
  const returnAmountInput = document.getElementById('return_amount');
  
  function calculateReturnAmount() {
    const qty = parseInt(quantityInput.value) || 0;
    const price = parseFloat(buyingPriceInput.value) || 0;
    const total = qty * price;
    returnAmountInput.value = 'Rs. ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }
  
  quantityInput.addEventListener('input', calculateReturnAmount);
  buyingPriceInput.addEventListener('input', calculateReturnAmount);
});
</script>

<?php include_once('layouts/footer.php'); ?>

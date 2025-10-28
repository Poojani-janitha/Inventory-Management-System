
<?php
  $page_title = 'Edit Invoice';
  require_once('includes/load.php');
  page_require_level(3);

  // Get invoice number from URL
  $invoice_number = isset($_GET['invoice_number']) ? $db->escape($_GET['invoice_number']) : '';
  if(empty($invoice_number)){
    $session->msg("d","Missing invoice number.");
    redirect('invoice_list.php');
    exit;
  }

  // Fetch invoice items joined to product (sales.sale_product_id => product.p_id)
  $invoice_sql = "SELECT s.sales_id, s.sale_product_id, s.quantity, s.sale_selling_price, s.total, s.discount, s.name, s.pNumber, s.email,
                         p.product_name, p.quantity AS product_quantity, p.selling_price AS product_selling_price
                  FROM sales s
                  LEFT JOIN product p ON s.sale_product_id = p.p_id
                  WHERE s.invoice_number = '{$invoice_number}'
                  ORDER BY s.sales_id ASC";
  $invoice_items_result = $db->query($invoice_sql);

  if(!$invoice_items_result || $db->num_rows($invoice_items_result) == 0){
    $session->msg("d","Invoice not found.");
    redirect('invoice_list.php');
    exit;
  }

  $invoice_items = [];
  while($row = $db->fetch_assoc($invoice_items_result)) {
      $invoice_items[] = $row;
  }
  $first_item = $invoice_items[0];

// Handle item deletion (returns qty to product.quantity)
if(isset($_GET['delete_item']) && isset($_GET['confirm']) && $_GET['confirm'] == 'yes'){
    $item_id = (int)$_GET['delete_item'];

    // Ensure item belongs to this invoice (use sales_id)
    $item_sql = "SELECT * FROM sales WHERE sales_id = {$item_id} AND invoice_number = '{$invoice_number}' LIMIT 1";
    $item_result = $db->query($item_sql);
    $item = $db->fetch_assoc($item_result);

    if($item){
        // Prevent deleting last item
        $count_sql = "SELECT COUNT(*) as count FROM sales WHERE invoice_number = '{$invoice_number}'";
        $count_result = $db->query($count_sql);
        $count_row = $db->fetch_assoc($count_result);
        if($count_row['count'] <= 1){
            $session->msg("d","Cannot delete the last item. Delete the entire invoice instead.");
            redirect("edit_invoice.php?invoice_number=" . urlencode($invoice_number), false);
            exit;
        }

        // Return stock to product (use sale_product_id => product.p_id)
        $product_id = $db->escape($item['sale_product_id']);
        $qty = (int)$item['quantity'];
        if($product_id !== ''){
            $update_stock_sql = "UPDATE product SET quantity = quantity + {$qty} WHERE p_id = '{$product_id}'";
            $db->query($update_stock_sql);
        }

        // Delete sales row
        $delete_item_sql = "DELETE FROM sales WHERE sales_id = {$item_id}";
        $db->query($delete_item_sql);

        $session->msg("s","Item removed from invoice.");
    }

    redirect("edit_invoice.php?invoice_number=" . urlencode($invoice_number), false);
    exit;
}

// Handle form submission: update customer details, update each sale qty+total, update product stock
if(isset($_POST['update_invoice'])){
    $updated_name = isset($_POST['customer_name']) ? $db->escape($_POST['customer_name']) : '';
    $updated_phone = isset($_POST['customer_phone']) ? $db->escape($_POST['customer_phone']) : '';
    $updated_email = isset($_POST['customer_email']) ? $db->escape($_POST['customer_email']) : '';

    $errors = [];

    if(empty($updated_name)) $errors[] = "Customer name is required.";
    if(empty($updated_phone)) $errors[] = "Phone number is required.";

    if(empty($errors)){
        // Update customer info on all sales rows for this invoice
        // Note: sales table in your schema has name, pNumber, email columns
        $update_sql = "UPDATE sales SET 
                       name = '{$updated_name}',
                       pNumber = '{$updated_phone}',
                       email = '{$updated_email}'
                       WHERE invoice_number = '{$invoice_number}'";
        $update_result = $db->query($update_sql);

        if($update_result){
            // Process quantity updates
            if(isset($_POST['item_qty']) && is_array($_POST['item_qty'])){
                foreach($_POST['item_qty'] as $sale_id_raw => $new_qty_raw){
                    $sale_id = (int)$sale_id_raw;
                    $new_qty = (int)$new_qty_raw;
                    if($sale_id <= 0) { continue; }
                    if($new_qty <= 0) { continue; }

                    // Load current sale row (use sales_id)
                    $item_sql = "SELECT * FROM sales WHERE sales_id = {$sale_id} LIMIT 1";
                    $item_result = $db->query($item_sql);
                    $item = $db->fetch_assoc($item_result);
                    if(!$item) { continue; }

                    $old_qty = (int)$item['quantity'];
                    $product_id = $db->escape($item['sale_product_id']); // VARCHAR p_id
                    $qty_diff = $new_qty - $old_qty; // positive => reduce product.quantity

                    if($qty_diff !== 0 && $product_id !== ''){
                        // Read current product stock
                        $prod_sql = "SELECT quantity FROM product WHERE p_id = '{$product_id}' LIMIT 1";
                        $prod_res = $db->query($prod_sql);
                        $prod = $db->fetch_assoc($prod_res);
                        $current_stock = $prod ? (int)$prod['quantity'] : 0;

                        if($qty_diff > 0){
                            // Need to take items from product stock
                            if($current_stock < $qty_diff){
                                $session->msg('d', "Not enough stock for product {$product_id}. Available: {$current_stock}, needed: {$qty_diff}.");
                                redirect("edit_invoice.php?invoice_number=" . urlencode($invoice_number), false);
                                exit;
                            }
                            $upd_prod_sql = "UPDATE product SET quantity = quantity - {$qty_diff} WHERE p_id = '{$product_id}'";
                        } else {
                            // Return items to stock
                            $add_back = abs($qty_diff);
                            $upd_prod_sql = "UPDATE product SET quantity = quantity + {$add_back} WHERE p_id = '{$product_id}'";
                        }
                        $db->query($upd_prod_sql);
                    }

                    // Determine unit price: sale_selling_price else product_selling_price
                    $unit_price = 0.0;
                    if(isset($item['sale_selling_price']) && $item['sale_selling_price'] !== null){
                        $unit_price = (float)$item['sale_selling_price'];
                    } else {
                        // Try to get product selling price as fallback
                        if($product_id !== ''){
                            $prod_sql2 = "SELECT selling_price FROM product WHERE p_id = '{$product_id}' LIMIT 1";
                            $prod_res2 = $db->query($prod_sql2);
                            $prod2 = $db->fetch_assoc($prod_res2);
                            $unit_price = $prod2 ? (float)$prod2['selling_price'] : 0.0;
                        }
                    }

                    $new_total = $new_qty * $unit_price;

                    // Update sales row (quantity and total) using sales_id
                    $update_item_sql = "UPDATE sales SET quantity = {$new_qty}, total = {$new_total} WHERE sales_id = {$sale_id}";
                    $db->query($update_item_sql);
                }
            }

            $session->msg("s","Invoice updated successfully.");
            redirect('invoice_list.php');
            exit;
        } else {
            $session->msg("d","Failed to update invoice.");
        }
    } else {
        foreach($errors as $error) $session->msg("d", $error);
    }
}

?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12"><?php echo display_msg($msg); ?></div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-edit"></span>
          <span>Edit Invoice: <?php echo htmlspecialchars($invoice_number); ?></span>
        </strong>
        <div class="pull-right">
          <a href="invoice_list.php" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
          <a href="generate_invoice.php?invoice_number=<?php echo urlencode($invoice_number); ?>" class="btn btn-info btn-sm" target="_blank"><span class="glyphicon glyphicon-print"></span> View</a>
        </div>
      </div>

      <div class="panel-body">
        <form method="post" action="edit_invoice.php?invoice_number=<?php echo urlencode($invoice_number); ?>">

          <div class="panel panel-info">
            <div class="panel-heading"><strong>Customer Information</strong></div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-6">
                  <label>Customer Name *</label>
                  <input type="text" name="customer_name" class="form-control" required value="<?php echo htmlspecialchars(isset($first_item['name']) ? $first_item['name'] : ''); ?>">
                </div>
                <div class="col-md-6">
                  <label>Phone Number *</label>
                  <input type="text" name="customer_phone" class="form-control" required value="<?php echo htmlspecialchars(isset($first_item['pNumber']) ? $first_item['pNumber'] : ''); ?>">
                </div>
              </div>
              <div class="row" style="margin-top:10px;">
                <div class="col-md-6">
                  <label>Email</label>
                  <input type="email" name="customer_email" class="form-control" value="<?php echo htmlspecialchars(isset($first_item['email']) ? $first_item['email'] : ''); ?>">
                </div>
                <div class="col-md-6">
                  <label>Address (not saved)</label>
                  <input type="text" name="customer_address" class="form-control" value="">
                  <small class="text-muted">Address field present for UI only — your sales table has no address column.</small>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-warning">
            <div class="panel-heading"><strong>Invoice Items</strong></div>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th class="text-right">Unit Price</th>
                      <th>Quantity</th>
                      <th class="text-right">Total</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
<?php
  $grand_total = 0;
  foreach($invoice_items as $item):
    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
    $unit = isset($item['sale_selling_price']) && $item['sale_selling_price'] !== null ? (float)$item['sale_selling_price'] : (isset($item['product_selling_price']) ? (float)$item['product_selling_price'] : 0.0);
    $item_total = $qty * $unit;
    $grand_total += $item_total;
?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                        <small class="text-muted">Product ID: <?php echo htmlspecialchars($item['sale_product_id']); ?></small>
                      </td>
                      <td class="text-right">LKR <?php echo number_format($unit,2); ?></td>
                      <td>
                        <input type="number"
                               name="item_qty[<?php echo (int)$item['sales_id']; ?>]"
                               class="form-control qty-input"
                               min="1"
                               value="<?php echo $qty; ?>"
                               data-price="<?php echo $unit; ?>"
                               data-id="<?php echo (int)$item['sales_id']; ?>"
                               style="width:120px;">
                      </td>
                      <td class="text-right item-total" data-id="<?php echo (int)$item['sales_id']; ?>">LKR <?php echo number_format($item_total,2); ?></td>
                      <td class="text-center">
                        <a href="edit_invoice.php?invoice_number=<?php echo urlencode($invoice_number); ?>&delete_item=<?php echo (int)$item['sales_id']; ?>&confirm=yes" class="btn btn-danger btn-xs" onclick="return confirm('Remove this item? This will return quantity to stock.');"><span class="glyphicon glyphicon-trash"></span> Remove</a>
                      </td>
                    </tr>
<?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                      <td class="text-right"><strong id="grand-total">LKR <?php echo number_format($grand_total,2); ?></strong></td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <div class="form-group">
            <button type="submit" name="update_invoice" class="btn btn-success">Update Invoice</button>
            <a href="invoice_list.php" class="btn btn-default">Cancel</a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  function fmt(n){ return 'LKR ' + n.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,'$&,'); }
  var qtyInputs = document.querySelectorAll('.qty-input');
  qtyInputs.forEach(function(input){
    input.addEventListener('input', function(){
      var price = parseFloat(this.getAttribute('data-price')) || 0;
      var qty = parseInt(this.value) || 0;
      var id = this.getAttribute('data-id');
      var tot = price * qty;
      var cell = document.querySelector('.item-total[data-id="'+id+'"]');
      if(cell) cell.textContent = fmt(tot);
      // recalc grand total
      var all = document.querySelectorAll('.item-total'), g=0;
      all.forEach(function(c){ var t=c.textContent.replace('LKR ','').replace(/,/g,''); g += parseFloat(t)||0; });
      var gt = document.getElementById('grand-total'); if(gt) gt.textContent = fmt(g);
    });
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>
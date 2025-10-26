<?php
$page_title = 'Edit Order';
require_once('includes/load.php');
page_require_level(1);

// Get order ID from URL
$order_id = (int)$_GET['o_id'];
$order = find_by_id('purchase_order', $order_id, 'o_id');

if (!$order) {
    $session->msg("d", "Order not found.");
    redirect('order.php', false);
}

// Handle form submission
if (isset($_POST['update_order'])) {
    $req_fields = ['quantity', 'status'];
    validate_fields($req_fields);

    if (empty($errors)) {
        $quantity = (int)$db->escape($_POST['quantity']);
        $status   = remove_junk($db->escape($_POST['status']));

        $sql = "UPDATE purchase_order SET quantity='{$quantity}', status='{$status}' WHERE o_id='{$order_id}'";
        if ($db->query($sql)) {
            $session->msg("s", "Order updated successfully.");
            redirect('order.php', false);
        } else {
            $session->msg("d", "Failed to update order. Error: " . $db->error);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
    }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <?php echo display_msg($msg); ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-pencil"></span> Edit Order</strong>
      </div>
      <div class="panel-body">
        <form method="post" action="">
          <div class="form-group">
            <label>Product Name</label>
            <input type="text" class="form-control" value="<?php echo remove_junk($order['product_name']); ?>" disabled>
          </div>

          <div class="form-group">
            <label>Quantity</label>
            <input type="number" class="form-control" name="quantity" value="<?php echo (int)$order['quantity']; ?>" min="1" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" required>
              <option value="Pending" <?php echo ($order['status']=='Pending') ? 'selected' : ''; ?>>Pending</option>
              <option value="Approved" <?php echo ($order['status']=='Approved') ? 'selected' : ''; ?>>Approved</option>
            </select>
          </div>

          <button type="submit" name="update_order" class="btn btn-success btn-block">
            <span class="glyphicon glyphicon-save"></span> Update Order
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

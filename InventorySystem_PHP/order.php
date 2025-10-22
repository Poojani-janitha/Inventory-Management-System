<?php 
$page_title = 'Order Management';
require_once('includes/load.php');
page_require_level(1);

// Get all categories
$all_categories = find_all('categories');

// Get all orders
$sql = "SELECT o.*, p.name as product_name, s.supplier_name, c.name as category_name
        FROM orders o
        LEFT JOIN products p ON o.product_id = p.id
        LEFT JOIN suppliers s ON o.supplier_id = s.id
        LEFT JOIN categories c ON p.categorie_id = c.id
        ORDER BY o.date DESC";
$all_orders = find_by_sql($sql);
?>

<?php
// ======================== PLACE NEW ORDER ========================
if (isset($_POST['place_order'])) {
    $req_fields = array('category-id', 'product-id', 'supplier-id', 'order-quantity');
    validate_fields($req_fields);
    
    $category_id = (int)$db->escape($_POST['category-id']);
    $product_id = (int)$db->escape($_POST['product-id']);
    $supplier_id = (int)$db->escape($_POST['supplier-id']);
    $order_qty = (int)$db->escape($_POST['order-quantity']);
    
    if (empty($errors)) {
        $product = find_by_id('products', $product_id);
        $supplier = find_by_id('suppliers', $supplier_id);

        $sql = "INSERT INTO orders (product_id, supplier_id, quantity, date, status)
                VALUES ('{$product_id}', '{$supplier_id}', '{$order_qty}', NOW(), 'Pending')";
        
        if ($db->query($sql)) {
            // Send email
            $to = $supplier['email'];
            $subject = "New Order Request - " . $product['name'];
            $message = "Dear {$supplier['supplier_name']},\n\n".
                       "We would like to place an order for the following:\n\n".
                       "Product: {$product['name']}\n".
                       "Quantity: {$order_qty} units\n".
                       "Order Date: " . date('Y-m-d H:i:s') . "\n\n".
                       "Please confirm the availability and expected delivery date.\n\n".
                       "Best Regards,\nInventory Management Team";
            $headers = "From: noreply@inventorysystem.com\r\nReply-To: admin@inventorysystem.com\r\n";
            
            if (mail($to, $subject, $message, $headers)) {
                $session->msg("s", "Order placed successfully and email sent to supplier.");
            } else {
                $session->msg("s", "Order placed successfully! (Email could not be sent)");
            }
        } else {
            $session->msg("d", "Failed to place order.");
        }
        redirect('order.php', false);
    } else {
        $session->msg("d", $errors);
        redirect('order.php', false);
    }
}
?>

<?php
// ======================== EDIT ORDER ========================
if (isset($_POST['edit_order'])) {
    $order_id = (int)$db->escape($_POST['order-id']);
    $new_product_id = (int)$db->escape($_POST['edit-product-id']);
    $new_supplier_id = (int)$db->escape($_POST['edit-supplier-id']);
    $new_quantity = (int)$db->escape($_POST['edit-quantity']);
    $new_status = $db->escape($_POST['edit-status']);
    
    $old_order = find_by_id('orders', $order_id);

    if ($old_order) {
        $sql = "UPDATE orders 
                SET product_id='{$new_product_id}', supplier_id='{$new_supplier_id}', 
                    quantity='{$new_quantity}', status='{$new_status}' 
                WHERE id='{$order_id}'";
        
        if ($db->query($sql)) {
            $session->msg("s", "Order updated successfully.");
            
            // Send email if order details changed
            if ($old_order['quantity'] != $new_quantity || 
                $old_order['supplier_id'] != $new_supplier_id || 
                $old_order['product_id'] != $new_product_id) {
                    
                $supplier = find_by_id('suppliers', $new_supplier_id);
                $product = find_by_id('products', $new_product_id);
                
                $to = $supplier['email'];
                $subject = "Order Update Notification - " . $product['name'];
                $message = "Dear {$supplier['supplier_name']},\n\n".
                           "Please note that an existing order has been updated.\n\n".
                           "Updated Details:\n".
                           "Product: {$product['name']}\n".
                           "Quantity: {$new_quantity} units\n".
                           "Status: {$new_status}\n".
                           "Updated Date: " . date('Y-m-d H:i:s') . "\n\n".
                           "If you have already started processing the previous request, please confirm.\n\n".
                           "Best Regards,\nInventory Management Team";
                $headers = "From: noreply@inventorysystem.com\r\nReply-To: admin@inventorysystem.com\r\n";
                
                mail($to, $subject, $message, $headers);
            }
        } else {
            $session->msg("d", "Failed to update order.");
        }
    } else {
        $session->msg("d", "Order not found.");
    }
    redirect('order.php', false);
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12"><?php echo display_msg($msg); ?></div>
</div>

<div class="row">
    <!-- ======= PLACE NEW ORDER ======= -->
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-shopping-cart"></span> Place New Order</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="order.php">
                    <!-- CATEGORY -->
                    <div class="form-group">
                        <label>Select Category</label>
                        <select class="form-control" name="category-id" id="category-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($all_categories as $cat): ?>
                                <option value="<?php echo (int)$cat['id']; ?>">
                                    <?php echo remove_junk(ucfirst($cat['name'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- PRODUCT -->
                    <div class="form-group">
                        <label>Select Product</label>
                        <select class="form-control" name="product-id" id="product-select" required>
                            <option value="">Select category first</option>
                        </select>
                    </div>

                    <!-- SUPPLIER -->
                    <div class="form-group">
                        <label>Select Supplier</label>
                        <select class="form-control" name="supplier-id" id="supplier-select" required>
                            <option value="">Select product first</option>
                        </select>
                    </div>

                    <!-- QUANTITY -->
                    <div class="form-group">
                        <label>Order Quantity</label>
                        <input type="number" class="form-control" name="order-quantity" min="1" required>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-primary">
                        <span class="glyphicon glyphicon-send"></span> Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ======= ALL ORDERS ======= -->
    <div class="col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-th"></span> All Orders</strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_orders as $order): ?>
                        <tr>
                            <td><?php echo count_id(); ?></td>
                            <td><?php echo remove_junk($order['category_name']); ?></td>
                            <td><?php echo remove_junk($order['product_name']); ?></td>
                            <td><?php echo remove_junk($order['supplier_name']); ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($order['date'])); ?></td>
                            <td><span class="label label-warning"><?php echo $order['status']; ?></span></td>
                            <td>
                                <button class="btn btn-xs btn-info editBtn"
                                    data-id="<?php echo $order['id']; ?>"
                                    data-product="<?php echo $order['product_id']; ?>"
                                    data-supplier="<?php echo $order['supplier_id']; ?>"
                                    data-qty="<?php echo $order['quantity']; ?>"
                                    data-status="<?php echo $order['status']; ?>">
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

<!-- ======= EDIT ORDER MODAL ======= -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="order.php">
        <div class="modal-header">
          <h4 class="modal-title">Edit Order</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="order-id" id="edit-order-id">
          
          <div class="form-group">
            <label>Product</label>
            <select class="form-control" name="edit-product-id" id="edit-product-id" required>
              <?php foreach (find_all('products') as $p): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label>Supplier</label>
            <select class="form-control" name="edit-supplier-id" id="edit-supplier-id" required>
              <?php foreach (find_all('suppliers') as $s): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo $s['supplier_name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" class="form-control" name="edit-quantity" id="edit-quantity" min="1" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="edit-status" id="edit-status" required>
              <option>Pending</option>
              <option>Processing</option>
              <option>Completed</option>
              <option>Cancelled</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit_order" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ======= AJAX SCRIPTS ======= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.getElementById('category-select');
    var productSelect = document.getElementById('product-select');
    var supplierSelect = document.getElementById('supplier-select');

    // Category -> Products
    categorySelect.addEventListener('change', function() {
        var categoryId = this.value;
        productSelect.innerHTML = '<option value="">Loading products...</option>';
        supplierSelect.innerHTML = '<option value="">Select product first</option>';
        if (categoryId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_products.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var products = JSON.parse(xhr.responseText);
                        productSelect.innerHTML = '<option value="">Select Product</option>';
                        products.forEach(function(p) {
                            var option = document.createElement('option');
                            option.value = p.id;
                            option.text = p.name;
                            productSelect.appendChild(option);
                        });
                    } catch (e) { productSelect.innerHTML = '<option>Error loading products</option>'; }
                }
            };
            xhr.send('category_id=' + encodeURIComponent(categoryId));
        }
    });

    // Product -> Suppliers
    productSelect.addEventListener('change', function() {
        var productId = this.value;
        supplierSelect.innerHTML = '<option value="">Loading suppliers...</option>';
        if (productId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_suppliers.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var suppliers = JSON.parse(xhr.responseText);
                        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                        suppliers.forEach(function(s) {
                            var option = document.createElement('option');
                            option.value = s.id;
                            option.text = s.supplier_name + ' (' + s.contact_number + ')';
                            supplierSelect.appendChild(option);
                        });
                    } catch (e) { supplierSelect.innerHTML = '<option>Error loading suppliers</option>'; }
                }
            };
            xhr.send('product_id=' + encodeURIComponent(productId));
        }
    });

    // Edit button modal
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-order-id').value = this.dataset.id;
            document.getElementById('edit-product-id').value = this.dataset.product;
            document.getElementById('edit-supplier-id').value = this.dataset.supplier;
            document.getElementById('edit-quantity').value = this.dataset.qty;
            document.getElementById('edit-status').value = this.dataset.status;
            $('#editModal').modal('show');
        });
    });
});
</script>

<?php include_once('layouts/footer.php'); ?>

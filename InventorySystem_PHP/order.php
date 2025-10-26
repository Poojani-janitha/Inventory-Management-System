<?php
$page_title = 'Order Management';
require_once('includes/load.php');
page_require_level(1);

// Get all categories
$all_categories = find_all('categories');

// Get all orders
$sql = "SELECT po.*, si.s_name AS supplier_name
        FROM purchase_order po
        JOIN supplier_info si ON po.s_id = si.s_id
        ORDER BY po.order_date DESC";
$all_orders = find_by_sql($sql);

/*======================== PLACE NEW ORDER ========================*/
if (isset($_POST['place_order'])) {
    $req_fields = ['category_name', 'product_name', 'supplier_id', 'order_quantity'];
    validate_fields($req_fields);

    if (empty($errors)) {
        $category_name = remove_junk($db->escape($_POST['category_name']));
        $product_name  = remove_junk($db->escape($_POST['product_name']));
        $supplier_id   = remove_junk($db->escape($_POST['supplier_id']));
        $order_qty     = (int)$db->escape($_POST['order_quantity']);

        if (empty($supplier_id)) {
            $session->msg("d", "Please select a valid supplier.");
            redirect('order.php', false);
        }

        $supplier_sql = "SELECT * FROM supplier_info WHERE s_id = '{$supplier_id}' LIMIT 1";
        $supplier_result = $db->query($supplier_sql);

        if ($supplier_result && $supplier_result->num_rows > 0) {
            $supplier = $supplier_result->fetch_assoc();
        } else {
            $session->msg("d", "Supplier not found. Please try again.");
            redirect('order.php', false);
        }

        $price_sql = "SELECT price FROM supplier_product 
                      WHERE s_id = '{$supplier_id}' 
                      AND product_name = '{$product_name}' 
                      LIMIT 1";
        $price_result = $db->query($price_sql);

        if ($price_result && $price_result->num_rows > 0) {
            $price_data = $price_result->fetch_assoc();
            $price = $price_data['price'];
        } else {
            $session->msg("d", "Price not found for this product and supplier combination.");
            redirect('order.php', false);
        }

        $insert_sql = "INSERT INTO purchase_order (s_id, product_name, category_name, quantity, price, order_date, status)
                       VALUES ('{$supplier_id}', '{$product_name}', '{$category_name}', '{$order_qty}', '{$price}', NOW(), 'Pending')";

        if ($db->query($insert_sql)) {
            $total_amount = $price * $order_qty;
            $to = $supplier['email'];
            $subject = "🛒 New Purchase Order - {$product_name}";

            $message = "
            <html>
            <head>
              <title>New Purchase Order</title>
              <style>
                body { font-family: Arial, sans-serif; background-color: #1e1e1e; color: #ffffff; }
                .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 15px; text-align: center; font-size: 20px; font-weight: bold; }
                .content { padding: 20px; background-color: #2c2c2c; border-radius: 8px; }
                .order-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .order-table th, .order-table td { border: 1px solid #555; padding: 10px; text-align: left; }
                .order-table th { background-color: #4CAF50; color: #fff; }
                .footer { margin-top: 20px; font-size: 0.9em; color: #bbb; }
                a { color: #4CAF50; text-decoration: none; }
              </style>
            </head>
            <body>
              <div class='container'>
                <div class='header'>New Purchase Order</div>
                <div class='content'>
                  <p>Dear <strong>{$supplier['s_name']}</strong>,</p>
                  <p>We would like to place the following order:</p>
                  <table class='order-table'>
                    <tr><th>Item</th><th>Details</th></tr>
                    <tr><td>Product Name</td><td>{$product_name}</td></tr>
                    <tr><td>Category</td><td>{$category_name}</td></tr>
                    <tr><td>Quantity</td><td>{$order_qty} units</td></tr>
                    <tr><td>Unit Price</td><td>Rs. {$price}</td></tr>
                    <tr><td>Total Amount</td><td>Rs. {$total_amount}</td></tr>
                    <tr><td>Order Date</td><td>" . date('Y-m-d H:i:s') . "</td></tr>
                  </table>
                  <p><strong>Delivery Instructions:</strong></p>
                  <p>Please confirm availability and provide expected delivery date.</p>
                  <p>For any queries, please contact us at:</p>
                  <p>Email: <a href='mailto:admin@inventorysystem.lk'>admin@inventorysystem.lk</a><br>
                  Phone: +94 11 234 5678</p>
                </div>
                <div class='footer'>
                  This is an automated message. Please do not reply to this email.
                </div>
              </div>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: Inventory System <noreply@inventorysystem.com>\r\n";
            $headers .= "Reply-To: admin@inventorysystem.com\r\n";

            if(mail($to, $subject, $message, $headers)){
                $session->msg("s", "Order placed successfully and email sent to supplier.");
            } else {
                $session->msg("w", "Order placed successfully, but email could not be sent.");
            }

        } else {
            $session->msg("d", "Failed to place order. Error: " . $db->error);
        }

    } else {
        $session->msg("d", implode("<br>", $errors));
    }

    redirect('order.php', false);
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>

    <!-- ======= PLACE NEW ORDER FORM ======= -->
    <div class="panel panel-default" style="width: 500px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; margin-bottom: 20px;">
      <div class="panel-heading" style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
        <strong><span class="glyphicon glyphicon-shopping-cart"></span> Place New Order</strong>
      </div>
      <div class="panel-body" style="padding: 20px;">
        <form method="post" action="order.php" id="order-form">
          <div class="form-group">
            <label>Select Category</label>
            <select class="form-control" name="category_name" id="category-select" required>
              <option value="">Select Category</option>
              <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo remove_junk($cat['category_name']); ?>">
                  <?php echo remove_junk($cat['category_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Select Product</label>
            <select class="form-control" name="product_name" id="product-select" required>
              <option value="">Select category first</option>
            </select>
          </div>

          <div class="form-group">
            <label>Select Supplier</label>
            <select class="form-control" name="supplier_id" id="supplier-select" required>
              <option value="">Select product first</option>
            </select>
          </div>

          <div class="form-group">
            <label>Order Quantity</label>
            <input type="number" class="form-control" name="order_quantity" placeholder="Enter quantity" min="1" required>
          </div>

          <button type="submit" name="place_order" class="btn btn-primary btn-block">
            <span class="glyphicon glyphicon-send"></span> Place Order
          </button>
        </form>
      </div>
    </div>

    <!-- ======= PURCHASE ORDERS TABLE ======= -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th"></span> Purchase Orders</strong>
      </div>
      <div class="panel-body" style="overflow-x:auto;">
        <table class="table table-bordered table-striped" style="min-width: 900px;">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Supplier</th>
              <th>Product</th>
              <th>Category</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($all_orders as $order): ?>
              <tr>
                <td class="text-center"><?php echo (int)$order['o_id']; ?></td>
                <td><?php echo remove_junk($order['supplier_name']); ?></td>
                <td><?php echo remove_junk($order['product_name']); ?></td>
                <td><?php echo remove_junk($order['category_name']); ?></td>
                <td><?php echo (int)$order['quantity']; ?></td>
                <td>Rs. <?php echo number_format($order['price'],2); ?></td>
                <td>
                  <?php if($order['status'] == 'Pending'): ?>
                    <span class="label label-warning"><?php echo $order['status']; ?></span>
                  <?php else: ?>
                    <span class="label label-success"><?php echo $order['status']; ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button class="btn btn-info btn-sm edit-btn" 
                          data-id="<?php echo (int)$order['o_id']; ?>" 
                          data-product="<?php echo remove_junk($order['product_name']); ?>"
                          data-quantity="<?php echo (int)$order['quantity']; ?>"
                          data-status="<?php echo $order['status']; ?>"
                          title="Edit Order">
                    <span class="glyphicon glyphicon-edit"></span>
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

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color: #2c2c2c; color: #fff;">
      <div class="modal-header">
        <h5 class="modal-title" id="editOrderModalLabel">Edit Order</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="color:#fff;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editOrderForm">
        <div class="modal-body">
          <input type="hidden" name="o_id" id="edit-order-id">

          <div class="form-group">
            <label>Product Name</label>
            <input type="text" class="form-control" id="edit-product-name" disabled>
          </div>

          <div class="form-group">
            <label>Quantity</label>
            <input type="number" class="form-control" id="edit-quantity" name="quantity" min="1" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" id="edit-status" name="status" required>
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Update Order</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
document.getElementById('category-select').addEventListener('change', function() {
    var category = this.value;
    fetch('get_products.php?category=' + encodeURIComponent(category))
      .then(response => response.json())
      .then(data => {
          var productSelect = document.getElementById('product-select');
          productSelect.innerHTML = '<option value="">Select Product</option>';
          data.forEach(function(product){
              productSelect.innerHTML += '<option value="'+product+'">'+product+'</option>';
          });
      });
});

document.getElementById('product-select').addEventListener('change', function() {
    var product = this.value;
    fetch('get_suppliers.php?product=' + encodeURIComponent(product))
      .then(response => response.json())
      .then(data => {
          var supplierSelect = document.getElementById('supplier-select');
          supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
          data.forEach(function(supplier){
              supplierSelect.innerHTML += '<option value="'+supplier.id+'">'+supplier.name+'</option>';
          });
      });
});

// Edit modal open
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        let id = btn.dataset.id;
        let product = btn.dataset.product;
        let quantity = btn.dataset.quantity;
        let status = btn.dataset.status;

        document.getElementById('edit-order-id').value = id;
        document.getElementById('edit-product-name').value = product;
        document.getElementById('edit-quantity').value = quantity;
        document.getElementById('edit-status').value = status;

        let modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
        modal.show();
    });
});

// AJAX update
document.getElementById('editOrderForm').addEventListener('submit', function(e){
    e.preventDefault();

    let o_id = document.getElementById('edit-order-id').value;
    let quantity = document.getElementById('edit-quantity').value;
    let status = document.getElementById('edit-status').value;

    fetch('update_order.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `o_id=${o_id}&quantity=${quantity}&status=${status}`
    })
    .then(res => res.text())
    .then(data => {
        alert(data);
        location.reload(); // reload page to see changes
    })
    .catch(err => console.error(err));
});

// Open edit modal and populate data
document.querySelectorAll('a.btn-info').forEach(function(btn){
    btn.addEventListener('click', function(e){
        e.preventDefault();
        let row = btn.closest('tr');

        // Get data from table row
        let o_id = row.querySelector('td:first-child').textContent.trim();
        let product_name = row.children[2].textContent.trim();
        let quantity = row.children[4].textContent.trim();
        let status = row.children[6].textContent.trim();

        // Fill modal inputs
        document.getElementById('edit-order-id').value = o_id;
        document.getElementById('edit-product-name').value = product_name;
        document.getElementById('edit-quantity').value = quantity;
        document.getElementById('edit-status').value = status;

        // Show modal (Bootstrap 5)
        var editModal = new bootstrap.Modal(document.getElementById('editOrderModal'));
        editModal.show();
    });
});

document.getElementById('editOrderForm').addEventListener('submit', function(e){
    e.preventDefault();

    let o_id = document.getElementById('edit-order-id').value;
    let quantity = document.getElementById('edit-quantity').value;
    let status = document.getElementById('edit-status').value;

    fetch('update_order.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `o_id=${o_id}&quantity=${quantity}&status=${status}`
    })
    .then(res => res.text())
    .then(data => {
        alert(data); // you can replace with nicer toast notifications
        location.reload(); // reload page to reflect changes
    })
    .catch(err => console.error(err));
});


</script>

<?php include_once('layouts/footer.php'); ?>

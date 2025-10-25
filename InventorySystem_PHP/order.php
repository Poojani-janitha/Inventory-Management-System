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

        // Validate supplier_id is not empty
        if (empty($supplier_id)) {
            $session->msg("d", "Please select a valid supplier.");
            redirect('order.php', false);
        }

        // Get supplier details
        $supplier_sql = "SELECT * FROM supplier_info WHERE s_id = '{$supplier_id}' LIMIT 1";
        $supplier_result = $db->query($supplier_sql);
        
        if ($supplier_result && $supplier_result->num_rows > 0) {
            $supplier = $supplier_result->fetch_assoc();
        } else {
            $session->msg("d", "Supplier not found. Please try again.");
            redirect('order.php', false);
        }

        // Get product price from supplier_product table
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

        // Insert order into purchase_order table
        $insert_sql = "INSERT INTO purchase_order (s_id, product_name, category_name, quantity, price, order_date, status)
                       VALUES ('{$supplier_id}', '{$product_name}', '{$category_name}', '{$order_qty}', '{$price}', NOW(), 'Pending')";

        if ($db->query($insert_sql)) {
            // Prepare and send email to supplier
            if ($supplier && !empty($supplier['email'])) {
                $to = $supplier['email'];
                $subject = "New Purchase Order - {$product_name}";
                
                $total_amount = $price * $order_qty;
                
                $message = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { padding: 20px; max-width: 600px; }
                        .header { background: #4CAF50; color: white; padding: 15px; text-align: center; }
                        .content { padding: 20px; background: #f9f9f9; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                        th { background: #4CAF50; color: white; }
                        .footer { margin-top: 20px; padding: 15px; background: #333; color: white; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>New Purchase Order</h2>
                        </div>
                        <div class='content'>
                            <p>Dear {$supplier['s_name']},</p>
                            <p>We would like to place the following order:</p>
                            
                            <table>
                                <tr>
                                    <th>Item</th>
                                    <th>Details</th>
                                </tr>
                                <tr>
                                    <td><strong>Product Name</strong></td>
                                    <td>{$product_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category</strong></td>
                                    <td>{$category_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Quantity</strong></td>
                                    <td>{$order_qty} units</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit Price</strong></td>
                                    <td>Rs. " . number_format($price, 2) . "</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td><strong>Rs. " . number_format($total_amount, 2) . "</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Order Date</strong></td>
                                    <td>" . date('Y-m-d H:i:s') . "</td>
                                </tr>
                            </table>
                            
                            <p><strong>Delivery Instructions:</strong></p>
                            <p>Please confirm availability and provide expected delivery date.</p>
                            
                            <p>For any queries, please contact us at:</p>
                            <p>Email: admin@inventorysystem.lk<br>
                            Phone: +94 11 234 5678</p>
                        </div>
                        <div class='footer'>
                            <p>Thank you for your service!</p>
                            <p><strong>Inventory Management System</strong></p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                // Send email
                if (send_email($to, $subject, $message)) {
                    $session->msg("s", "Order placed successfully! Email sent to {$supplier['s_name']} ({$to}).");
                } else {
                    $session->msg("w", "Order placed successfully, but email could not be sent. Please check email settings.");
                }
            } else {
                $session->msg("w", "Order placed successfully, but supplier email not available.");
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
        <form method="post" action="order.php" id="order-form">
          <!-- Category -->
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

          <!-- Product -->
          <div class="form-group">
            <label>Select Product</label>
            <select class="form-control" name="product_name" id="product-select" required>
              <option value="">Select category first</option>
            </select>
          </div>

          <!-- Supplier -->
          <div class="form-group">
            <label>Select Supplier</label>
            <select class="form-control" name="supplier_id" id="supplier-select" required>
              <option value="">Select product first</option>
            </select>
          </div>

          <!-- Quantity -->
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
  </div>

  <!-- ======= ALL ORDERS ======= -->
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th"></span> Purchase Orders</strong>
      </div>
      <div class="panel-body">
        <div style="max-height: 500px; overflow-y: auto;">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center" style="width: 40px;">#</th>
                <th>Category</th>
                <th>Product</th>
                <th>Supplier</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Total</th>
                <th class="text-center">Date</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($all_orders)): ?>
              <tr>
                <td colspan="9" class="text-center">No orders placed yet</td>
              </tr>
              <?php else: ?>
                <?php foreach ($all_orders as $order): ?>
                <tr>
                  <td class="text-center"><?php echo count_id(); ?></td>
                  <td><?php echo remove_junk($order['category_name']); ?></td>
                  <td><?php echo remove_junk($order['product_name']); ?></td>
                  <td><?php echo remove_junk($order['supplier_name']); ?></td>
                  <td class="text-center"><?php echo (int)$order['quantity']; ?></td>
                  <td class="text-center">Rs. <?php echo number_format($order['price'], 2); ?></td>
                  <td class="text-center">Rs. <?php echo number_format($order['price'] * $order['quantity'], 2); ?></td>
                  <td class="text-center"><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                  <td class="text-center">
                    <span class="label label-warning"><?php echo remove_junk($order['status']); ?></span>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ======= AJAX LOGIC ======= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const categorySelect = document.getElementById('category-select');
  const productSelect = document.getElementById('product-select');
  const supplierSelect = document.getElementById('supplier-select');

  // Category change - Load products
  categorySelect.addEventListener('change', function() {
    const category = this.value;
    productSelect.innerHTML = '<option value="">Loading products...</option>';
    supplierSelect.innerHTML = '<option value="">Select product first</option>';
    
    if (category) {
      fetch('get_products.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'category_name=' + encodeURIComponent(category)
      })
      .then(res => res.json())
      .then(data => {
        console.log('Products received:', data);
        productSelect.innerHTML = '<option value="">Select Product</option>';
        if (data.length > 0) {
          data.forEach(p => {
            let opt = document.createElement('option');
            opt.value = p.product_name;
            opt.text = p.product_name;
            productSelect.appendChild(opt);
          });
        } else {
          productSelect.innerHTML = '<option value="">No products available</option>';
        }
      })
      .catch(err => {
        console.error('Error loading products:', err);
        productSelect.innerHTML = '<option value="">Error loading products</option>';
      });
    } else {
      productSelect.innerHTML = '<option value="">Select category first</option>';
    }
  });

  // Product change - Load suppliers
  productSelect.addEventListener('change', function() {
    const product = this.value;
    supplierSelect.innerHTML = '<option value="">Loading suppliers...</option>';
    
    if (product) {
      fetch('get_suppliers.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_name=' + encodeURIComponent(product)
      })
      .then(res => res.json())
      .then(data => {
        console.log('Suppliers received:', data);
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        if (data.length > 0) {
          data.forEach(s => {
            let opt = document.createElement('option');
            opt.value = s.s_id;
            opt.text = s.s_name + ' - Rs. ' + parseFloat(s.price).toFixed(2) + ' (' + s.contact_number + ')';
            supplierSelect.appendChild(opt);
          });
        } else {
          supplierSelect.innerHTML = '<option value="">No suppliers available</option>';
        }
      })
      .catch(err => {
        console.error('Error loading suppliers:', err);
        supplierSelect.innerHTML = '<option value="">Error loading suppliers</option>';
      });
    } else {
      supplierSelect.innerHTML = '<option value="">Select product first</option>';
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>
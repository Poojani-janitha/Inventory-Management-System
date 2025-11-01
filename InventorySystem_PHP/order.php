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
            redirect('order.php');
        }

        $supplier_sql = "SELECT * FROM supplier_info WHERE s_id = '{$supplier_id}' LIMIT 1";
        $supplier_result = $db->query($supplier_sql);

        if ($supplier_result && $supplier_result->num_rows > 0) {
            $supplier = $supplier_result->fetch_assoc();
        } else {
            $session->msg("d", "Supplier not found. Please try again.");
            redirect('order.php');
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
            redirect('order.php');
        }

        $insert_sql = "INSERT INTO purchase_order (s_id, product_name, category_name, quantity, price, order_date, status)
                       VALUES ('{$supplier_id}', '{$product_name}', '{$category_name}', '{$order_qty}', '{$price}', NOW(), 'Pending')";

        if ($db->query($insert_sql)) {
            $total_amount = $price * $order_qty;
            $to = $supplier['email'];
            $subject = "🛒 New Purchase Order - {$product_name}";

            $message = "
            <!DOCTYPE html>
            <html>
            <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>New Purchase Order - Inventory Management System</title>
              <style>
                body { 
                  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                  margin: 0; 
                  padding: 20px; 
                  color: #333;
                }
                .email-container { 
                  max-width: 600px; 
                  margin: 0 auto; 
                  background: #ffffff; 
                  border-radius: 15px; 
                  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                  overflow: hidden;
                }
                .header { 
                  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
                  color: white; 
                  padding: 30px 20px; 
                  text-align: center; 
                  position: relative;
                }
                .header::before {
                  content: '';
                  position: absolute;
                  top: 0;
                  left: 0;
                  right: 0;
                  bottom: 0;
                  background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"1\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>') repeat;
                  opacity: 0.3;
                }
                .header h1 { 
                  margin: 0; 
                  font-size: 28px; 
                  font-weight: 600; 
                  position: relative;
                  z-index: 1;
                }
                .header .subtitle {
                  margin: 8px 0 0 0;
                  font-size: 16px;
                  opacity: 0.9;
                  position: relative;
                  z-index: 1;
                }
                .content { 
                  padding: 40px 30px; 
                  background: #ffffff;
                }
                .greeting {
                  font-size: 18px;
                  color: #2c3e50;
                  margin-bottom: 25px;
                  line-height: 1.6;
                }
                .order-card {
                  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                  border-radius: 12px;
                  padding: 25px;
                  margin: 25px 0;
                  border-left: 5px solid #4CAF50;
                  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                }
                .order-table { 
                  width: 100%; 
                  border-collapse: collapse; 
                  margin-top: 15px;
                  background: white;
                  border-radius: 8px;
                  overflow: hidden;
                  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                }
                .order-table th { 
                  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
                  color: #fff; 
                  padding: 15px 12px; 
                  text-align: left; 
                  font-weight: 600;
                  font-size: 14px;
                  text-transform: uppercase;
                  letter-spacing: 0.5px;
                }
                .order-table td { 
                  padding: 15px 12px; 
                  border-bottom: 1px solid #e9ecef;
                  font-size: 15px;
                }
                .order-table tr:last-child td {
                  border-bottom: none;
                }
                .order-table tr:nth-child(even) {
                  background-color: #f8f9fa;
                }
                .highlight {
                  background: linear-gradient(135deg, #4CAF50, #45a049);
                  color: white;
                  padding: 4px 8px;
                  border-radius: 4px;
                  font-weight: 600;
                }
                .total-amount {
                  background: linear-gradient(135deg, #ff6b6b, #ee5a52);
                  color: white;
                  padding: 8px 12px;
                  border-radius: 6px;
                  font-weight: 700;
                  font-size: 16px;
                }
                .instructions {
                  background: #e3f2fd;
                  border-left: 4px solid #2196F3;
                  padding: 20px;
                  margin: 25px 0;
                  border-radius: 0 8px 8px 0;
                }
                .contact-info {
                  background: #f8f9fa;
                  padding: 20px;
                  border-radius: 8px;
                  margin: 25px 0;
                  text-align: center;
                }
                .contact-info h3 {
                  color: #2c3e50;
                  margin: 0 0 15px 0;
                  font-size: 18px;
                }
                .contact-links {
                  display: flex;
                  justify-content: center;
                  gap: 40px;
                  flex-wrap: wrap;
                }
                .contact-links a {
                  color: #4CAF50;
                  text-decoration: none;
                  font-weight: 600;
                  padding: 10px 20px;
                  border: 2px solid #4CAF50;
                  border-radius: 25px;
                  transition: all 0.3s ease;
                  display: inline-block;
                  margin: 10px 15px;
                }
                .contact-links a:hover {
                  background: #4CAF50;
                  color: white;
                }
                .footer { 
                  background: #2c3e50;
                  color: #bdc3c7; 
                  padding: 20px 30px; 
                  text-align: center; 
                  font-size: 14px;
                  line-height: 1.5;
                }
                .footer p {
                  margin: 5px 0;
                }
                .company-logo {
                  width: 40px;
                  height: 40px;
                  background: white;
                  border-radius: 50%;
                  display: inline-flex;
                  align-items: center;
                  justify-content: center;
                  margin-bottom: 10px;
                  font-weight: bold;
                  color: #4CAF50;
                  font-size: 18px;
                }
                @media (max-width: 600px) {
                  .email-container { margin: 10px; }
                  .content { padding: 20px 15px; }
                  .contact-links { flex-direction: column; align-items: center; }
                }
              </style>
            </head>
            <body>
              <div class='email-container'>
                <div class='header'>
                  <div class='company-logo'></div>
                  <h1>🛒 New Purchase Order</h1>
                  <p class='subtitle'>Inventory Management System</p>
                </div>
                <div class='content'>
                  <div class='greeting'>
                    Dear <strong>{$supplier['s_name']}</strong>,<br><br>
                    We are pleased to place a new order with your company. Please find the order details below:
                  </div>
                  
                  <div class='order-card'>
                    <h3 style='margin: 0 0 20px 0; color: #2c3e50; font-size: 20px;'>📋 Order Details</h3>
                    <table class='order-table'>
                      <tr><th>Product Information</th><th>Details</th></tr>
                      <tr><td><strong>Product Name</strong></td><td>{$product_name}</td></tr>
                      <tr><td><strong>Category</strong></td><td>{$category_name}</td></tr>
                      <tr><td><strong>Quantity</strong></td><td><span class='highlight'>{$order_qty} units</span></td></tr>
                      <tr><td><strong>Unit Price</strong></td><td>Rs. " . number_format($price, 2) . "</td></tr>
                      <tr><td><strong>Total Amount</strong></td><td><span class='total-amount'>Rs. " . number_format($total_amount, 2) . "</span></td></tr>
                      <tr><td><strong>Order Date</strong></td><td>" . date('F j, Y \a\t g:i A') . "</td></tr>
                    </table>
                  </div>

                  <div class='instructions'>
                    <h4 style='margin: 0 0 15px 0; color: #1976D2;'>📝 Delivery Instructions</h4>
                    <p style='margin: 0; color: #424242; line-height: 1.6;'>
                      Please confirm the availability of the requested items and provide us with the expected delivery date. 
                      We appreciate your prompt response and look forward to a successful business relationship.
                    </p>
                  </div>

                  <div class='contact-info'>
                    <h3>📞 Need Assistance?</h3>
                    <p style='margin: 0 0 15px 0; color: #666;'>For any queries or clarifications, please don't hesitate to contact us:</p>
                    <div class='contact-links'>
                      <a href='mailto:admin@inventorysystem.com'>📧 Email Support</a>
                      <a href='tel:+94112345678'>📞 Call Us</a>
                    </div>
                  </div>
                </div>
                <div class='footer'>
                  <p><strong>Inventory Management System</strong></p>
                  <p>This is an automated message. Please do not reply to this email.</p>
                  <p>© " . date('Y') . " All rights reserved.</p>
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

    redirect('order.php');
}

$msg = $session->msg();
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
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="editOrderModalLabel">Edit Order</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Update Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Professional CSS Styling -->
<style>
/* Panel Heading Gradient - Like your friend's page */
.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
  padding: 15px 20px;
}

.panel-heading strong {
  font-size: 18px;
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
}

/* Table Hover Effect */
.table-hover tbody tr:hover {
  background-color: #f1f8ff;
  cursor: pointer;
}

/* Button Styling with Gradients */
.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.btn-info {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
  border: none;
}

.btn-info:hover {
  background: linear-gradient(135deg, #44a08d 0%, #4ecdc4 100%);
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  border: none;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #ee5a52 0%, #ff6b6b 100%);
}

.btn-success {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
  border: none;
}

.btn-success:hover {
  background: linear-gradient(135deg, #a8e063 0%, #56ab2f 100%);
}

.btn-warning {
  background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
  border: none;
}

/* Form Control Focus */
.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Label Styling */
label {
  font-weight: 600;
  color: #2c3e50;
}

/* Panel Shadow */
.panel {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
}

/* Modal Header Gradient */
.modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.modal-header .close {
  color: white;
  opacity: 0.8;
}

.modal-header h4 {
  color: white;
}

/* Label Badges */
.label {
  font-size: 12px;
  padding: 5px 10px;
  border-radius: 3px;
  font-weight: 600;
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
</style>

<script>
document.getElementById('category-select').addEventListener('change', function() {
    var category = this.value;
    
    // Reset product and supplier dropdowns
    var productSelect = document.getElementById('product-select');
    var supplierSelect = document.getElementById('supplier-select');
    productSelect.innerHTML = '<option value="">Select Product</option>';
    supplierSelect.innerHTML = '<option value="">Select product first</option>';
    
    if (category) {
        fetch('get_products.php?category=' + encodeURIComponent(category))
          .then(response => {
              if (!response.ok) {
                  throw new Error('Network response was not ok');
              }
              return response.json();
          })
          .then(data => {
              productSelect.innerHTML = '<option value="">Select Product</option>';
              if (data && data.length > 0) {
                  data.forEach(function(product){
                      productSelect.innerHTML += '<option value="'+product+'">'+product+'</option>';
                  });
              } else {
                  productSelect.innerHTML += '<option value="" disabled>No products found for this category</option>';
              }
          })
          .catch(error => {
              console.error('Error fetching products:', error);
              productSelect.innerHTML = '<option value="" disabled>Error loading products</option>';
          });
    }
});

document.getElementById('product-select').addEventListener('change', function() {
    var product = this.value;
    
    // Reset supplier dropdown
    var supplierSelect = document.getElementById('supplier-select');
    supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
    
    if (product) {
        fetch('get_suppliers.php?product=' + encodeURIComponent(product))
          .then(response => {
              if (!response.ok) {
                  throw new Error('Network response was not ok');
              }
              return response.json();
          })
          .then(data => {
              supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
              if (data && data.length > 0) {
                  data.forEach(function(supplier){
                      supplierSelect.innerHTML += '<option value="'+supplier.id+'">'+supplier.name+'</option>';
                  });
              } else {
                  supplierSelect.innerHTML += '<option value="" disabled>No suppliers found for this product</option>';
              }
          })
          .catch(error => {
              console.error('Error fetching suppliers:', error);
              supplierSelect.innerHTML = '<option value="" disabled>Error loading suppliers</option>';
          });
    }
});

// Edit modal functionality
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        let id = this.dataset.id;
        let product = this.dataset.product;
        let quantity = this.dataset.quantity;
        let status = this.dataset.status;

        // Populate modal fields
        document.getElementById('edit-order-id').value = id;
        document.getElementById('edit-product-name').value = product;
        document.getElementById('edit-quantity').value = quantity;
        document.getElementById('edit-status').value = status;

        // Show modal (using jQuery for Bootstrap 3 compatibility)
        $('#editOrderModal').modal('show');
    });
});

// Handle form submission
document.getElementById('editOrderForm').addEventListener('submit', function(e){
    e.preventDefault();

    let o_id = document.getElementById('edit-order-id').value;
    let quantity = document.getElementById('edit-quantity').value;
    let status = document.getElementById('edit-status').value;

    // Show loading state
    let submitBtn = this.querySelector('button[type="submit"]');
    let originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Updating...';
    submitBtn.disabled = true;

    fetch('update_order.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `o_id=${o_id}&quantity=${quantity}&status=${status}`
    })
    .then(res => res.text())
    .then(data => {
        alert(data);
        $('#editOrderModal').modal('hide');
        location.reload(); // reload page to see changes
    })
    .catch(err => {
        console.error(err);
        alert('Error updating order. Please try again.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<?php include_once('layouts/footer.php'); ?>
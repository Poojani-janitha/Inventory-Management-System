<?php
  $page_title = 'Add Return';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  page_require_level(2);
  //extra add prabashi 
  $msg = $session->msg();
?>
<?php
 if(isset($_POST['add_return'])){
   $req_fields = array('product_id','quantity','return_reason','product_name','buying_price');
   validate_fields($req_fields);
   if(empty($errors)){
     $p_id          = remove_junk($db->escape($_POST['product_id']));
     $qty           = remove_junk($db->escape($_POST['quantity']));
     $reason        = remove_junk($db->escape($_POST['return_reason']));
     $notes         = remove_junk($db->escape($_POST['notes']));
     $product_name  = remove_junk($db->escape($_POST['product_name']));
     $buying_price  = remove_junk($db->escape($_POST['buying_price']));
     $s_id          = remove_junk($db->escape($_POST['s_id']));
     $date          = make_date();
     
     // Validate product exists and has sufficient quantity
     $product = find_by_id('product', $p_id);
     if(!$product){
       $session->msg('d','Invalid product ID.');
       redirect('add_return.php', false);
     }
     
     if($qty > $product['quantity']){
       $session->msg('d','Return quantity cannot exceed available stock.');
       redirect('add_return.php', false);
     }
     
     // Calculate return price (return quantity × buying price)
     $return_price = $qty * $buying_price;
     
     $query  = "INSERT INTO return_details (";
     $query .=" p_id,s_id,product_name,buying_price,return_quantity,return_date";
     $query .=") VALUES (";
     $query .=" '{$p_id}', '{$s_id}', '{$product_name}', '{$buying_price}', '{$qty}', '{$date}'";
     $query .=")";
     
     if($db->query($query)){
       // Update product quantity
       $new_qty = $product['quantity'] - $qty;
       $update_query = "UPDATE product SET quantity = '{$new_qty}' WHERE p_id = '{$p_id}'";
       $db->query($update_query);
       
       // Get supplier information for email notification using direct SQL query (more reliable)
       $supplier_sql = "SELECT s_id, s_name, email, address, contact_number FROM supplier_info WHERE s_id = '{$s_id}' LIMIT 1";
       $supplier_result = $db->query($supplier_sql);
       
       $email_sent = false;
       $email_error = '';
       
       // Validate and send email to supplier
       if($supplier_result && $db->num_rows($supplier_result) > 0){
         $supplier = $db->fetch_assoc($supplier_result);
         
         // Validate email address exists and is valid format
         if(!empty($supplier['email']) && filter_var($supplier['email'], FILTER_VALIDATE_EMAIL)){
           $category_name = isset($product['category_name']) ? $product['category_name'] : 'N/A';
           $supplier_name = htmlspecialchars($supplier['s_name'], ENT_QUOTES, 'UTF-8');
           $to = trim($supplier['email']);
           $subject = "📦 Product Return Notification - " . htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8');
           
           // Build HTML email message
           $return_date = date('F j, Y');
           $return_time = date('g:i A');
           $formatted_product_name = htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8');
           $formatted_category = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
           $formatted_reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');
           $formatted_notes = !empty($notes) ? htmlspecialchars($notes, ENT_QUOTES, 'UTF-8') : '';
           
           $message = "
           <!DOCTYPE html>
           <html>
           <head>
             <meta charset='UTF-8'>
             <meta name='viewport' content='width=device-width, initial-scale=1.0'>
             <title>Product Return Notification - Inventory Management System</title>
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
                 background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                 color: white; 
                 padding: 30px 20px; 
                 text-align: center; 
                 position: relative;
               }
               .header h1 { 
                 margin: 0; 
                 font-size: 28px; 
                 font-weight: 600; 
                 position: relative;
                 z-index: 1;
               }
               .content { padding: 40px 30px; background: #ffffff; }
               .return-table { 
                 width: 100%; 
                 border-collapse: collapse; 
                 margin-top: 15px;
                 background: white;
                 border-radius: 8px;
                 overflow: hidden;
               }
               .return-table th { 
                 background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                 color: #fff; 
                 padding: 15px 12px; 
                 text-align: left; 
                 font-weight: 600;
               }
               .return-table td { 
                 padding: 15px 12px; 
                 border-bottom: 1px solid #e9ecef; 
                 background: #f8f9fa;
               }
               .return-table tr:last-child td {
                 border-bottom: none;
               }
               .footer { 
                 background: #2c3e50;
                 color: #bdc3c7; 
                 padding: 20px 30px; 
                 text-align: center; 
               }
               .notice {
                 background: #fff3cd;
                 border-left: 4px solid #ffc107;
                 padding: 15px;
                 margin: 20px 0;
                 border-radius: 5px;
                 color: #856404;
               }
               .highlight {
                 color: #dc3545;
                 font-weight: 600;
               }
             </style>
           </head>
           <body>
             <div class='email-container'>
               <div class='header'>
                 <h1>📦 Product Return Notification</h1>
               </div>
               <div class='content'>
                 <p>Dear <strong>{$supplier_name}</strong>,</p>
                 <p>We are writing to inform you about a product return from our inventory system.</p>
                 <table class='return-table'>
                   <tr><th>Product Name</th><td>{$formatted_product_name}</td></tr>
                   <tr><th>Category</th><td>{$formatted_category}</td></tr>
                   <tr><th>Return Quantity</th><td><span class='highlight'>{$qty} units</span></td></tr>
                   <tr><th>Buying Price (per unit)</th><td>Rs. " . number_format($buying_price, 2) . "</td></tr>
                   <tr><th>Total Refund Amount</th><td><span class='highlight'>Rs. " . number_format($return_price, 2) . "</span></td></tr>
                   <tr><th>Return Reason</th><td>{$formatted_reason}</td></tr>
                   <tr><th>Return Date</th><td>{$return_date} at {$return_time}</td></tr>
                 </table>
                 " . (!empty($formatted_notes) ? "<div class='notice'><strong>Additional Notes:</strong><br>{$formatted_notes}</div>" : "") . "
                 <p><strong>Important:</strong> Please review this return and contact us if you have any questions or concerns regarding this return.</p>
                 <p>Thank you for your attention to this matter.</p>
                 <p style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;'>
                   <small style='color: #666;'>This is an automated email from the Inventory Management System. Please do not reply to this email.</small>
                 </p>
               </div>
               <div class='footer'>
                 <p><strong>Inventory Management System</strong></p>
                 <p>© " . date('Y') . " All rights reserved.</p>
               </div>
             </div>
           </body>
           </html>
           ";

           // Use send_return_email() function specifically for return notifications
           // This uses nimharachalana12@gmail.com as configured in php.ini
           if(function_exists('send_return_email')){
             $email_sent = send_return_email($to, $subject, $message);
           } else {
             // Fallback: Direct mail with nimharachalana12@gmail.com
             $headers = "MIME-Version: 1.0\r\n";
             $headers .= "Content-type:text/html;charset=UTF-8\r\n";
             $headers .= "From: Inventory Management System <nimharachalana12@gmail.com>\r\n";
             $headers .= "Reply-To: nimharachalana12@gmail.com\r\n";
             $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
             
             // Send email
             $email_sent = @mail($to, $subject, $message, $headers);
           }
           
           if($email_sent){
             $session->msg('s', "Return processed successfully! Email notification sent to supplier ({$to}). Return amount: Rs. " . number_format($return_price, 2));
           } else {
             // Get detailed error information
             $error_msg = error_get_last();
             $error_detail = isset($error_msg['message']) ? $error_msg['message'] : 'Unknown error';
             
             // Check php.ini configuration
             $smtp = ini_get('SMTP');
             $sendmail_from = ini_get('sendmail_from');
             
             $debug_info = "";
             if(empty($smtp)){
               $debug_info = " (SMTP not configured in php.ini. Please configure SMTP in php.ini file)";
             } elseif(empty($sendmail_from)){
               $debug_info = " (sendmail_from not set in php.ini. Please set sendmail_from in php.ini)";
             }
             
             $session->msg('w', "Return processed successfully. Return amount: Rs. " . number_format($return_price, 2) . ". However, email could not be sent to supplier ({$to})." . $debug_info . " Check error logs for details.");
           }
         } else {
           $session->msg('w', "Return processed successfully. Return amount: Rs. " . number_format($return_price, 2) . ". Supplier email address is invalid or missing (Supplier ID: {$s_id})");
         }
       } else {
         $session->msg('w', "Return processed successfully. Return amount: Rs. " . number_format($return_price, 2) . ". Supplier not found in database (Supplier ID: {$s_id})");
       }
       
       redirect('returns.php', false);
     } else {
       $session->msg('d',' Sorry failed to add return! Error: ' . $db->error);
       redirect('add_return.php', false);
     }
   } else{
     $session->msg("d", $errors);
     redirect('add_return.php',false);
   }
 }
?>
<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/add_return.css">
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
            <span class="glyphicon glyphicon-plus"></span>
            <span>Add New Return</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_return.php" class="clearfix">
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="product_name_dropdown" class="control-label">Select Product</label>
                    <select class="form-control" name="product_name_dropdown" id="product_name_dropdown" required>
                      <option value="">-- Select Product --</option>
                      <?php
                      // Debug: Check database connection
                      if(!$db) {
                        echo "<option value=''>Database connection failed</option>";
                      } else {
                        // Fetch all products from database using product table
                        $all_products = find_all('product');
                        
                        if($all_products && count($all_products) > 0):
                          foreach($all_products as $product):
                      ?>
                      <option value="<?php echo $product['p_id']; ?>" 
                              data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                              data-selling-price="<?php echo $product['selling_price']; ?>"
                              data-buying-price="<?php echo $product['buying_price']; ?>"
                              data-quantity="<?php echo $product['quantity']; ?>"
                              data-s-id="<?php echo $product['s_id']; ?>"
                              data-category-name="<?php echo $product['category_name']; ?>"
                              data-expiry-date="<?php echo $product['expire_date'] ?? ''; ?>">
                        <?php echo $product['product_name']; ?> (ID: <?php echo $product['p_id']; ?>)
                      </option>
                      <?php 
                          endforeach;
                        else:
                          echo "<option value=''>No products found in database</option>";
                        endif;
                      }
                      ?>
                    </select>
                    <small class="help-block">Select product from dropdown to auto-fill details</small>
                  </div>
                  <div class="col-md-6">
                    <label for="quantity" class="control-label">Return Quantity</label>
                    <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
                  </div>
                </div>
              </div>
              
              <!-- Hidden field for product ID -->
              <input type="hidden" name="product_id" id="product_id" required>
              <input type="hidden" name="s_id" id="s_id" required>
              <input type="hidden" name="buying_price" id="buying_price_hidden">
              
              <div class="form-group">
                <div class="row">
                  <div class="col-md-3">
                    <label for="product_name" class="control-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" readonly>
                  </div>
                  <div class="col-md-3">
                    <label for="buying_price" class="control-label">Buying Price (per unit)</label>
                    <input type="text" class="form-control" id="buying_price" name="buying_price_display" readonly>
                  </div>
                  <div class="col-md-3">
                    <label for="return_quantity" class="control-label">Return Quantity</label>
                    <input type="number" class="form-control" id="return_quantity_display" readonly>
                  </div>
                  <div class="col-md-3">
                    <label for="return_price" class="control-label">Return Price (Total)</label>
                    <input type="text" class="form-control" id="return_price" name="return_price_display" readonly style="font-weight: bold; color: #d9534f; font-size: 18px;">
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <div class="row">
                  <div class="col-md-4">
                    <label for="supplier_id" class="control-label">Supplier ID</label>
                    <input type="text" class="form-control" id="supplier_id" name="supplier_id" readonly>
                  </div>
                  <div class="col-md-4">
                    <label for="current_stock" class="control-label">Current Stock</label>
                    <input type="text" class="form-control" id="current_stock" name="current_stock" readonly>
                  </div>
                  <div class="col-md-4">
                    <label for="category_name" class="control-label">Category</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" readonly>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="return_reason" class="control-label">Return Reason</label>
                    <select class="form-control" name="return_reason" id="return_reason" required>
                      <option value="">Select Reason</option>
                      <option value="Expired">Expired</option>
                      <option value="Damaged">Damaged</option>
                      <option value="Customer Mistake">Customer Mistake</option>
                      <option value="Defective">Defective</option>
                      <option value="Wrong Item">Wrong Item</option>
                      <option value="Quality Issue">Quality Issue</option>
                      <option value="Recall">Recall</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="refund_amount" class="control-label">Refund Amount</label>
                    <input type="text" class="form-control" id="refund_amount" readonly>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label for="notes" class="control-label">Notes</label>
                <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Additional notes about the return..."></textarea>
              </div>
              
              <div class="form-group">
                <button type="submit" name="add_return" class="btn btn-danger">
                  <span class="glyphicon glyphicon-arrow-left"></span> Process Return
                </button>
              </div>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<style>
.return-form {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
}

.form-group label {
  font-weight: 600;
  color: #495057;
}

.form-control:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;
  padding: 10px 30px;
  font-weight: 600;
}

.btn-danger:hover {
  background-color: #c82333;
  border-color: #bd2130;
}

.help-block {
  color: #6c757d;
  font-size: 0.875em;
}

.alert-info {
  background-color: #d1ecf1;
  border-color: #bee5eb;
  color: #0c5460;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.product-info {
  background: #e9ecef;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
  border-left: 4px solid #dc3545;
}

/* Product dropdown styling */
.product-dropdown {
  position: relative;
}

.product-dropdown select {
  background: white;
  border: 1px solid #ced4da;
  border-radius: 5px;
  padding: 10px 15px;
  font-size: 14px;
  width: 100%;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.product-dropdown select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  outline: none;
}

.product-dropdown select option {
  padding: 10px;
  font-size: 14px;
}

.product-dropdown select option:hover {
  background-color: #f8f9fa;
}

/* Enhanced dropdown styling */
.form-control select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

/* Form group relative positioning for dropdown */
.form-group {
  position: relative;
}

/* Remove old search styles and add dropdown styles */
.suggestions-dropdown {
  display: none !important;
}

.suggestion-item {
  display: none !important;
}

/* Enhanced dropdown styling */
#product_name_dropdown {
  background: white;
  border: 1px solid #ced4da;
  border-radius: 5px;
  padding: 10px 15px;
  font-size: 14px;
  width: 100%;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

#product_name_dropdown:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  outline: none;
}

#product_name_dropdown option {
  padding: 10px;
  font-size: 14px;
}

#product_name_dropdown option:hover {
  background-color: #f8f9fa;
}

/* Enhanced product info display */
.product-info {
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  border: 1px solid #e1f5fe;
  border-radius: 8px;
  padding: 20px;
  margin-top: 15px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-info h5 {
  color: #1976d2;
  margin-bottom: 15px;
  font-weight: 600;
  display: flex;
  align-items: center;
}

.product-info h5 i {
  margin-right: 8px;
  color: #1976d2;
}

.product-info p {
  margin-bottom: 8px;
  color: #424242;
  font-size: 14px;
}

.product-info strong {
  color: #1976d2;
  font-weight: 600;
}

/* Loading indicator */
.loading {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #dc3545;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* No results message */
.no-results {
  padding: 15px;
  text-align: center;
  color: #6c757d;
  font-style: italic;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const productDropdown = document.getElementById('product_name_dropdown');
  const productIdInput = document.getElementById('product_id');
  const sIdInput = document.getElementById('s_id');
  const productNameInput = document.getElementById('product_name');
  const buyingPriceInput = document.getElementById('buying_price');
  const buyingPriceHidden = document.getElementById('buying_price_hidden');
  const returnQuantityDisplay = document.getElementById('return_quantity_display');
  const returnPriceDisplay = document.getElementById('return_price');
  const quantityInput = document.getElementById('quantity');
  const refundAmountInput = document.getElementById('refund_amount');
  const returnReasonSelect = document.getElementById('return_reason');
  
  // Product dropdown selection functionality
  productDropdown.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (selectedOption.value) {
      // Get product data from data attributes
      const productData = {
        id: selectedOption.value,
        name: selectedOption.dataset.name,
        buying_price: selectedOption.dataset.buyingPrice,
        quantity: selectedOption.dataset.quantity,
        s_id: selectedOption.dataset.sId,
        category_name: selectedOption.dataset.categoryName,
        expiry_date: selectedOption.dataset.expiryDate
      };
      
      // Auto-fill all form fields
      fillProductDetails(productData);
    } else {
      // Clear all fields if no product selected
      clearProductDetails();
    }
  });
  
  // Calculate return price when quantity changes
  quantityInput.addEventListener('input', calculateReturnPrice);
  
  // Auto-suggest supplier email for expired products
  returnReasonSelect.addEventListener('change', function() {
    if (this.value === 'Expired') {
      suggestSupplierEmail();
    }
  });
  
  function fillProductDetails(product) {
    // Fill basic product information
    productIdInput.value = product.id;
    sIdInput.value = product.s_id;
    productNameInput.value = product.name;
    buyingPriceInput.value = 'Rs. ' + parseFloat(product.buying_price).toFixed(2);
    
    // Set hidden values for form submission
    buyingPriceHidden.value = product.buying_price;
    
    // Set supplier ID and current stock
    document.getElementById('supplier_id').value = product.s_id;
    document.getElementById('current_stock').value = product.quantity + ' units';
    document.getElementById('category_name').value = product.category_name || '';
    
    // Calculate return price
    calculateReturnPrice();
    
    // Show product information
    showProductInfo(product);
  }
  
  function clearProductDetails() {
    // Clear all form fields
    productIdInput.value = '';
    sIdInput.value = '';
    productNameInput.value = '';
    buyingPriceInput.value = '';
    buyingPriceHidden.value = '';
    returnQuantityDisplay.value = '';
    returnPriceDisplay.value = '';
    document.getElementById('supplier_id').value = '';
    document.getElementById('current_stock').value = '';
    document.getElementById('category_name').value = '';
    refundAmountInput.value = '';
    
    // Hide product info
    const infoDiv = document.getElementById('product-info-display');
    if (infoDiv) {
      infoDiv.remove();
    }
  }
  
  function calculateReturnPrice() {
    const buyingPrice = parseFloat(buyingPriceHidden.value) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    
    // Calculate return price (quantity × buying price)
    const returnPrice = buyingPrice * quantity;
    
    // Display in both fields
    returnQuantityDisplay.value = quantity;
    returnPriceDisplay.value = 'Rs. ' + returnPrice.toFixed(2);
    refundAmountInput.value = 'Rs. ' + returnPrice.toFixed(2);
  }
  
  function showProductInfo(product) {
    // Create or update product info display
    let infoDiv = document.getElementById('product-info-display');
    if (!infoDiv) {
      infoDiv = document.createElement('div');
      infoDiv.id = 'product-info-display';
      infoDiv.className = 'product-info';
      productDropdown.parentNode.appendChild(infoDiv);
    }
    
    infoDiv.innerHTML = `
      <h5><i class="glyphicon glyphicon-info-sign"></i> Complete Product Information</h5>
      <div class="row">
        <div class="col-md-6">
          <p><strong>Product ID:</strong> ${product.id}</p>
          <p><strong>Product Name:</strong> ${product.name}</p>
          <p><strong>Available Stock:</strong> ${product.quantity} units</p>
          <p><strong>Category:</strong> ${product.category_name || 'N/A'}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Buying Price:</strong> Rs. ${parseFloat(product.buying_price).toFixed(2)}</p>
          <p><strong>Supplier ID:</strong> ${product.s_id || 'No Supplier'}</p>
          ${product.expiry_date ? `<p><strong>Expiry Date:</strong> ${product.expiry_date}</p>` : ''}
        </div>
      </div>
    `;
  }
  
  function suggestSupplierEmail() {
    // This would typically fetch supplier information
    // For now, we'll show a placeholder
    const notesTextarea = document.getElementById('notes');
    if (notesTextarea.value === '') {
      notesTextarea.value = 'Consider contacting supplier for expired product return.';
    }
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>

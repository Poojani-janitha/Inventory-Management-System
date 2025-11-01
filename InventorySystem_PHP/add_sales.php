<?php
  $page_title = 'Add Sale';
  require_once('includes/load.php');
  //Checkin What level user has permission to view this page
   page_require_level(3);
   //extra add prabashi 
  $msg = $session->msg();
   
// Function to generate unique invoice number
function generateInvoiceNumber() {
    global $db;
    
    // Get current date components
    $year = date('Y');
    $month = date('m');
    $day = date('d');
    
    // Create prefix: INV-YYYY-MM-DD-
    $prefix = "INV-{$year}-{$month}-{$day}-";
    
    try {
        // Check if invoice_number column exists
        $check_sql = "SHOW COLUMNS FROM sales LIKE 'invoice_number'";
        $check_result = $db->query($check_sql);
        
        if ($check_result && $db->num_rows($check_result) > 0) {
            // Column exists, get the last invoice number for today
            $sql = "SELECT invoice_number FROM sales WHERE invoice_number LIKE '{$prefix}%' ORDER BY invoice_number DESC LIMIT 1";
            $result = $db->query($sql);
            
            if ($result && $db->num_rows($result) > 0) {
                $last_invoice = $db->fetch_assoc($result);
                $last_number = intval(substr($last_invoice['invoice_number'], -4)); // Get last 4 digits
                $new_number = $last_number + 1;
            } else {
                $new_number = 1;
            }
        } else {
            // Column doesn't exist, start from 1
            $new_number = 1;
        }
    } catch (Exception $e) {
        // If there's any error, start from 1
        $new_number = 1;
    }
    
    // Format the number with leading zeros (4 digits)
    $invoice_number = $prefix . str_pad($new_number, 4, '0', STR_PAD_LEFT);
    
    return $invoice_number;
}

  if(isset($_POST['add_sale'])){
    $req_fields = array('name','pNumber','email');
    validate_fields($req_fields);
    
    // Generate unique invoice number
    $invoice_number = generateInvoiceNumber();
    
    // Additional validation for phone number and email
    $pNumber = $_POST['pNumber'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    // Validate customer name (only letters and spaces)
    if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
        $errors[] = "Customer name should contain only letters and spaces.";
    }
    
    // Validate phone number format (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $pNumber)) {
        $errors[] = "Phone number must be exactly 10 digits";
    }
    
    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    // Validate products array
    if (!isset($_POST['products']) || empty($_POST['products'])) {
        $errors[] = "At least one product is required";
    }
    
    // Validate each product
    $products = $_POST['products'];
    $subtotal = 0;
    $sales_data = [];
    
    foreach ($products as $index => $product) {
        $sale_product_id = $product['sale_product_id'];
        $category_name = $product['category_name'];
        $sale_selling_price = (float)$product['sale_selling_price'];
        $quantity = (int)$product['quantity'];
        $discount_type = $product['discount_type'];
        $discount = (float)$product['discount'];
        
        if (empty($sale_product_id) || empty($category_name)) {
            $errors[] = "Product " . ($index + 1) . " is incomplete";
            continue;
        }
        
        if ($quantity <= 0) {
            $errors[] = "Quantity for product " . ($index + 1) . " must be greater than 0";
        }
        
        if ($sale_selling_price <= 0) {
            $errors[] = "Price for product " . ($index + 1) . " must be greater than 0";
        }
        
        // Calculate product total with discount
        $product_subtotal = $sale_selling_price * $quantity;
        $product_total = $product_subtotal;
        $discount_amount = 0; // Store the actual discount amount in LKR
        
        if ($discount > 0) {
            if ($discount_type === 'percentage') {
                if ($discount <= 100) {
                    $discount_amount = $product_subtotal * $discount / 100;
                    $product_total = $product_subtotal - $discount_amount;
                } else {
                    $errors[] = "Discount percentage for product " . ($index + 1) . " cannot be more than 100%";
                }
            } else if ($discount_type === 'fixed') {
                if ($discount < $product_subtotal) {
                    $discount_amount = $discount;
                    $product_total = $product_subtotal - $discount_amount;
                } else {
                    $errors[] = "Discount amount for product " . ($index + 1) . " cannot be greater than or equal to subtotal";
                }
            }
        }
        
        $subtotal += $product_total;
        
        // Store sales data with actual discount amount (not percentage)
        $sales_data[] = [
            'sale_product_id' => $db->escape($sale_product_id),
            'category_name' => $db->escape($category_name),
            'sale_selling_price' => $db->escape($sale_selling_price),
            'quantity' => $db->escape($quantity),
            'discount' => $db->escape($discount_amount), // Store actual discount amount in LKR
            'total' => $db->escape($product_total),
            'name' => $db->escape($name),
            'pNumber' => $db->escape($pNumber),
            'email' => $db->escape($email),
            'invoice_number' => $db->escape($invoice_number)
        ];
    }
    
    // Calculate grand total
    $grand_total = $subtotal;
    
    if(empty($errors)){
        $success_count = 0;
        
        // Insert each product sale
        foreach ($sales_data as $sale_data) {
            // Check if invoice_number column exists
            $check_sql = "SHOW COLUMNS FROM sales LIKE 'invoice_number'";
            $check_result = $db->query($check_sql);
            
            if ($check_result && $db->num_rows($check_result) > 0) {
                // Column exists, include invoice_number
                $sql = "INSERT INTO sales (sale_product_id, category_name, sale_selling_price, quantity, discount, name, pNumber, email, total, invoice_number) VALUES (";
                $sql .= "'{$sale_data['sale_product_id']}','{$sale_data['category_name']}','{$sale_data['sale_selling_price']}','{$sale_data['quantity']}','{$sale_data['discount']}','{$sale_data['name']}','{$sale_data['pNumber']}','{$sale_data['email']}','{$sale_data['total']}','{$sale_data['invoice_number']}'";
                $sql .= ")";
            } else {
                // Column doesn't exist, exclude invoice_number
                $sql = "INSERT INTO sales (sale_product_id, category_name, sale_selling_price, quantity, discount, name, pNumber, email, total) VALUES (";
                $sql .= "'{$sale_data['sale_product_id']}','{$sale_data['category_name']}','{$sale_data['sale_selling_price']}','{$sale_data['quantity']}','{$sale_data['discount']}','{$sale_data['name']}','{$sale_data['pNumber']}','{$sale_data['email']}','{$sale_data['total']}'";
                $sql .= ")";
            }
            
            if ($db->query($sql)) {
                // Check if product has enough quantity before updating
                $check_qty_sql = "SELECT quantity FROM product WHERE p_id = '{$sale_data['sale_product_id']}'";
                $qty_result = $db->query($check_qty_sql);
                
                if ($qty_result && $db->num_rows($qty_result) > 0) {
                    $qty_row = $db->fetch_assoc($qty_result);
                    $current_qty = (int)$qty_row['quantity'];
                    $sale_qty = (int)$sale_data['quantity'];
                    
                    if ($current_qty >= $sale_qty) {
                        // Update product quantity only if sufficient stock
                        $update_qty_sql = "UPDATE product SET quantity = quantity - {$sale_data['quantity']} WHERE p_id = '{$sale_data['sale_product_id']}'";
                        if ($db->query($update_qty_sql)) {
                            $success_count++;
                        } else {
                            $errors[] = "Failed to update quantity for product " . $sale_data['sale_product_id'];
                        }
                    } else {
                        $errors[] = "Insufficient stock for product " . $sale_data['sale_product_id'] . ". Available: {$current_qty}, Requested: {$sale_qty}";
                    }
                } else {
                    $errors[] = "Product not found: " . $sale_data['sale_product_id'];
                }
            } else {
                $errors[] = "Failed to add sale for product " . $sale_data['sale_product_id'];
            }
        }
        
        if ($success_count > 0) {
            $session->msg('s', "Sale added successfully! Invoice #{$invoice_number}. {$success_count} product(s) sold. Grand Total: LKR " . number_format($grand_total, 2) . ". <a href='generate_invoice.php?invoice_number=" . urlencode($invoice_number) . "' target='_blank'>View Invoice</a>");
            // Redirect back to add sales page with success message
            redirect('add_sales.php?success=1', false);
        } else {
            $session->msg('d', 'Sorry, failed to add sale!');
            redirect('add_sales.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_sales.php', false);
    }
  }

?>
<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/add_sales.css">

<!-- Page Header -->
<div class="page-header">
  <div class="container-fluid">
    <h1><span class="glyphicon glyphicon-plus"></span> Add New Sale</h1>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-shopping-cart"></span>
          <span>Sales Transaction</span>
       </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="clearfix" id="salesForm">
          <!-- Invoice Information -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="invoice_number" class="control-label">Invoice Number</label>
                <input type="text" class="form-control" name="invoice_number" id="invoice_number" value="<?php echo 'INV-' . date('Y-m-d') . '-0001'; ?>" readonly style="background-color: #f5f5f5; font-weight: bold;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="sale_date" class="control-label">Sale Date</label>
                <input type="text" class="form-control" name="sale_date" id="sale_date" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly style="background-color: #f5f5f5;">
              </div>
            </div>
          </div>
          
          <!-- Customer Information -->
          <div class="customer-info">
            <h4><span class="glyphicon glyphicon-user"></span> Customer Information</h4>
            
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="name" class="control-label">Customer Name</label>
                  <input type="text" class="form-control" name="name" id="name" required pattern="[a-zA-Z ]+" title="Only letters and spaces are allowed">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="pNumber" class="control-label">Phone Number</label>
                  <input type="text" class="form-control" name="pNumber" id="pNumber" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
                  <small class="text-muted">Enter 10 digits (e.g., 0712345678)</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="email" class="control-label">Email</label>
                  <input type="email" class="form-control" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address">
                  <small class="text-muted">Optional - Enter valid email address</small>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Products Section -->
          <div class="card">
            <div class="card-header">
              <span class="glyphicon glyphicon-list"></span> Products & Services
              <button type="button" class="btn btn-success btn-sm pull-right" id="addProductBtn">
                <span class="glyphicon glyphicon-plus"></span> Add Product
              </button>
            </div>
            
            <div id="productsContainer">
              <!-- Product rows will be added here dynamically -->
            </div>
          </div>
          
          <!-- Order Summary -->
          <div class="order-summary">
            <h4><span class="glyphicon glyphicon-calculator"></span> Order Summary</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="subtotal" class="control-label">Subtotal (LKR)</label>
                <input type="number" class="form-control" name="subtotal" id="subtotal" step="0.01" readonly style="background-color: #f5f5f5;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="grand_total" class="control-label">Grand Total (LKR)</label>
                <input type="number" class="form-control grand-total" name="grand_total" id="grand_total" step="0.01" readonly>
              </div>
            </div>
          </div>
          
          <div class="row">
            <!-- <div class="col-md-4">
              <a href="sales_report.php" class="btn btn-info">
                <span class="glyphicon glyphicon-list"></span> View Sales Report
              </a>
            </div> -->
            <div class="col-md-4">
              <button type="button" class="btn btn-warning" onclick="clearForm()">
                <span class="glyphicon glyphicon-refresh"></span> Clear Form
              </button>
            </div>
            <div class="col-md-4">
              <button type="submit" name="add_sale" class="btn btn-danger pull-right">
                <span class="glyphicon glyphicon-plus"></span> Add Sale
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Global variables
var productRowCount = 0;
var categories = <?php 
    // Get categories that have products with available stock
    $category_sql = "SELECT DISTINCT c.category_name 
                     FROM categories c 
                     INNER JOIN product p ON c.category_name = p.category_name 
                     WHERE p.quantity > 0 
                     ORDER BY c.category_name";
    $category_result = $db->query($category_sql);
    $categories_array = [];
    if($category_result && $db->num_rows($category_result) > 0) {
        while($category_row = $db->fetch_assoc($category_result)) {
            $categories_array[] = ['category_name' => $category_row['category_name']];
        }
    }
    echo json_encode($categories_array);
    ?>;

// Load categories on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Categories loaded:', categories);
    console.log('Categories count:', categories.length);
    
    // Check if categories loaded properly
    if (!categories || categories.length === 0) {
        console.error('No categories loaded!');
        alert('Error: No categories found. Please check the database connection.');
        return;
    }
    
    // Check if this is a redirect after successful sale
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        // Clear all form fields
        clearForm();
    }
    
    addProductRow(); // Add first product row
});

// Add a new product row
function addProductRow() {
    productRowCount++;
    var container = document.getElementById('productsContainer');
    
    var productRow = document.createElement('div');
    productRow.className = 'product-row';
    productRow.id = 'productRow_' + productRowCount;
    
    productRow.innerHTML = `
        <div class="panel panel-default" style="margin-top: 10px;">
            <div class="panel-heading">
                <strong>Product ${productRowCount}</strong>
                <button type="button" class="btn btn-danger btn-xs pull-right remove-product" data-row="${productRowCount}">
                    <span class="glyphicon glyphicon-remove"></span> Remove
                </button>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Category</label>
                            <select class="form-control category-select" name="products[${productRowCount}][category_name]" data-row="${productRowCount}">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Product</label>
                            <select class="form-control product-select" name="products[${productRowCount}][sale_product_id]" data-row="${productRowCount}" disabled>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Unit Price (LKR)</label>
                            <input type="number" class="form-control unit-price" name="products[${productRowCount}][sale_selling_price]" data-row="${productRowCount}" step="0.01" min="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label class="control-label">Qty</label>
                            <input type="number" class="form-control quantity" name="products[${productRowCount}][quantity]" data-row="${productRowCount}" min="1" value="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Total (LKR)</label>
                            <input type="number" class="form-control product-total" data-row="${productRowCount}" step="0.01" readonly style="background-color: #f5f5f5;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Discount Type</label>
                            <select class="form-control discount-type" name="products[${productRowCount}][discount_type]" data-row="${productRowCount}">
                                <option value="none">None</option>
                                <option value="fixed">Fixed (LKR)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Discount</label>
                            <input type="number" class="form-control discount" name="products[${productRowCount}][discount]" data-row="${productRowCount}" step="0.01" min="0" value="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Final Total (LKR)</label>
                            <input type="number" class="form-control final-total" data-row="${productRowCount}" step="0.01" readonly style="background-color: #f5f5f5;">
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(productRow);
    
    // Populate categories dropdown
    var categorySelect = productRow.querySelector('.category-select');
    console.log('Populating categories for row', productRowCount, 'Categories available:', categories.length);
    
    if (categories && categories.length > 0) {
        categories.forEach(function(category) {
            var option = document.createElement('option');
            option.value = category.category_name;
            option.textContent = category.category_name;
            categorySelect.appendChild(option);
        });
    } else {
        console.error('No categories available for dropdown');
        var option = document.createElement('option');
        option.value = '';
        option.textContent = 'No categories available';
        categorySelect.appendChild(option);
    }
    
    // Add event listeners for this row
    addProductRowEventListeners(productRowCount);
}

// Clear all form fields
function clearForm() {
    // Clear customer information
    document.getElementById('name').value = '';
    document.getElementById('pNumber').value = '';
    document.getElementById('email').value = '';
    
    // Clear all product rows
    var productsContainer = document.getElementById('productsContainer');
    productsContainer.innerHTML = '';
    
    // Reset product row count
    productRowCount = 0;
    
    // Clear totals
    document.getElementById('subtotal').value = '';
    document.getElementById('grand_total').value = '';
    
    // Add a new product row
    addProductRow();
    
    // Clear any validation classes
    var inputs = document.querySelectorAll('input, select');
    inputs.forEach(function(input) {
        input.classList.remove('is-valid', 'is-invalid');
        input.setCustomValidity('');
    });
    
    // Remove any warning messages
    var warnings = document.querySelectorAll('.alert-warning, .stock-warning');
    warnings.forEach(function(warning) {
        warning.remove();
    });
}

// Show stock warning message
function showStockWarning(row, message) {
    // Remove existing warning
    var existingWarning = row.querySelector('.stock-warning');
    if (existingWarning) {
        existingWarning.remove();
    }
    
    // Create new warning
    var warningDiv = document.createElement('div');
    warningDiv.className = 'alert alert-danger stock-warning';
    warningDiv.style.marginTop = '10px';
    warningDiv.innerHTML = '<strong>Stock Warning:</strong> ' + message;
    
    row.querySelector('.panel-body').appendChild(warningDiv);
}


// Add event listeners for a product row
function addProductRowEventListeners(rowNumber) {
    var row = document.getElementById('productRow_' + rowNumber);
    
    // Category change event
    row.querySelector('.category-select').addEventListener('change', function() {
        var categoryName = this.value;
        var productSelect = row.querySelector('.product-select');
        var priceInput = row.querySelector('.unit-price');
        
        console.log('Category changed to:', categoryName);
        
        // Clear validation errors
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
        productSelect.setCustomValidity('');
        productSelect.classList.remove('is-invalid');
        
        // Clear existing options
        productSelect.innerHTML = '<option value="">Loading products...</option>';
        productSelect.disabled = true;
        priceInput.value = '';
        
        if (categoryName) {
            fetch('get_products_by_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'category_name=' + encodeURIComponent(categoryName)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(products => {
                console.log('Products loaded:', products);
                productSelect.innerHTML = '<option value="">Select Product</option>';
                
                if (products.length === 0) {
                    productSelect.innerHTML = '<option value="">No products available in this category</option>';
                    productSelect.disabled = true;
                    
                    // Show warning message
                    var warningDiv = document.createElement('div');
                    warningDiv.className = 'alert alert-warning';
                    warningDiv.style.marginTop = '10px';
                    warningDiv.innerHTML = '<strong>Warning:</strong> No products are available in the selected category. Please select a different category.';
                    
                    // Remove any existing warning
                    var existingWarning = row.querySelector('.alert-warning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }
                    
                    row.querySelector('.panel-body').appendChild(warningDiv);
                } else {
                    products.forEach(function(product) {
                        var option = document.createElement('option');
                        option.value = product.p_id;
                        option.textContent = product.product_name + ' (Stock: ' + product.quantity + ')';
                        option.dataset.sellingPrice = product.selling_price;
                        option.dataset.quantity = product.quantity;
                        productSelect.appendChild(option);
                    });
                    productSelect.disabled = false;
                    
                    // Remove any existing warning
                    var existingWarning = row.querySelector('.alert-warning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error loading products:', error);
                productSelect.innerHTML = '<option value="">Error loading products</option>';
                productSelect.disabled = false;
            });
        } else {
            productSelect.innerHTML = '<option value="">Select Product</option>';
            productSelect.disabled = false;
        }
    });
    
    // Product change event
    row.querySelector('.product-select').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var priceInput = row.querySelector('.unit-price');
        var quantityInput = row.querySelector('.quantity');
        
        // Clear validation errors
        quantityInput.setCustomValidity('');
        quantityInput.classList.remove('is-invalid');
        
        // Remove any existing stock warnings
        var existingWarning = row.querySelector('.stock-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        if (selectedOption.dataset.sellingPrice) {
            priceInput.value = selectedOption.dataset.sellingPrice;
            
            // Set max quantity to available stock
            if (selectedOption.dataset.quantity) {
                quantityInput.setAttribute('max', selectedOption.dataset.quantity);
                quantityInput.setAttribute('title', 'Maximum available stock: ' + selectedOption.dataset.quantity);
                
                // Reset quantity if it exceeds available stock
                var currentQuantity = parseInt(quantityInput.value) || 0;
                var availableStock = parseInt(selectedOption.dataset.quantity);
                if (currentQuantity > availableStock) {
                    quantityInput.value = availableStock;
                    showStockWarning(row, 'Quantity adjusted to available stock: ' + availableStock);
                }
            }
            
            calculateProductTotal(rowNumber);
        }
    });
    
    // Quantity change event
    row.querySelector('.quantity').addEventListener('input', function() {
        var productSelect = row.querySelector('.product-select');
        var selectedOption = productSelect.options[productSelect.selectedIndex];
        var quantity = parseInt(this.value) || 0;
        
        // Clear previous validation
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
        
        // Remove any existing stock warnings
        var existingWarning = row.querySelector('.stock-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        // Validate quantity against available stock (but don't update stock yet)
        if (selectedOption && selectedOption.dataset.quantity && quantity > 0) {
            var availableStock = parseInt(selectedOption.dataset.quantity);
            
            if (quantity > availableStock) {
                this.setCustomValidity('Quantity cannot exceed available stock: ' + availableStock);
                this.classList.add('is-invalid');
                
                // Show warning message
                showStockWarning(row, 'Insufficient stock! Available: ' + availableStock + ', Requested: ' + quantity);
            }
        }
        
        calculateProductTotal(rowNumber);
    });
    
    // Discount type change event
    row.querySelector('.discount-type').addEventListener('change', function() {
        calculateProductTotal(rowNumber);
    });
    
    // Discount change event
    row.querySelector('.discount').addEventListener('input', function() {
        calculateProductTotal(rowNumber);
    });
    
    // Calculate button event
    row.querySelector('.calculate-product').addEventListener('click', function() {
        calculateProductTotal(rowNumber);
    });
    
    // Remove product event
    row.querySelector('.remove-product').addEventListener('click', function() {
        if (productRowCount > 1) {
            row.remove();
            productRowCount--;
            calculateGrandTotal();
        } else {
            alert('At least one product is required.');
        }
    });
}

// Calculate total for a specific product row
function calculateProductTotal(rowNumber) {
    var row = document.getElementById('productRow_' + rowNumber);
    var unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
    var quantity = parseInt(row.querySelector('.quantity').value) || 0;
    var discount = parseFloat(row.querySelector('.discount').value) || 0;
    var discountType = row.querySelector('.discount-type').value;
    var totalInput = row.querySelector('.product-total');
    var finalTotalInput = row.querySelector('.final-total');
    
    // Calculate subtotal
    var subtotal = unitPrice * quantity;
    totalInput.value = subtotal.toFixed(2);
    
    // Calculate final total with discount
    var finalTotal = subtotal;
    
    if (discount > 0) {
        if (discountType === 'percentage') {
            if (discount <= 100) {
                finalTotal = subtotal - (subtotal * discount / 100);
            }
        } else if (discountType === 'fixed') {
            if (discount < subtotal) {
                finalTotal = subtotal - discount;
            }
        }
    }
    
    finalTotalInput.value = finalTotal.toFixed(2);
    calculateGrandTotal();
}

// Calculate grand total for all products
function calculateGrandTotal() {
    var grandTotal = 0;
    var subtotal = 0;
    var productRows = document.querySelectorAll('.product-row');
    
    productRows.forEach(function(row) {
        var totalInput = row.querySelector('.product-total');
        var finalTotalInput = row.querySelector('.final-total');
        var total = parseFloat(totalInput.value) || 0;
        var finalTotal = parseFloat(finalTotalInput.value) || 0;
        subtotal += total;
        grandTotal += finalTotal;
    });
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('grand_total').value = grandTotal.toFixed(2);
}

// Add product button event
document.getElementById('addProductBtn').addEventListener('click', function() {
    addProductRow();
});

// Phone number validation
document.getElementById('pNumber').addEventListener('input', function() {
    var phoneInput = this;
    var phoneValue = phoneInput.value.replace(/\D/g, ''); // Remove non-digits
    
    // Limit to 10 digits
    if (phoneValue.length > 10) {
        phoneValue = phoneValue.substring(0, 10);
    }
    
    phoneInput.value = phoneValue;
    
    // Validate format
    if (phoneValue.length === 10) {
        phoneInput.setCustomValidity('');
        phoneInput.classList.remove('is-invalid');
        phoneInput.classList.add('is-valid');
    } else if (phoneValue.length > 0) {
        phoneInput.setCustomValidity('Phone number must be exactly 10 digits');
        phoneInput.classList.remove('is-valid');
        phoneInput.classList.add('is-invalid');
    } else {
        phoneInput.setCustomValidity('');
        phoneInput.classList.remove('is-invalid', 'is-valid');
    }
});

// Email validation
document.getElementById('email').addEventListener('input', function() {
    var emailInput = this;
    var emailValue = emailInput.value.trim();
    
    if (emailValue === '') {
        emailInput.setCustomValidity('');
        emailInput.classList.remove('is-invalid', 'is-valid');
        return;
    }
    
    var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (emailPattern.test(emailValue)) {
        emailInput.setCustomValidity('');
        emailInput.classList.remove('is-invalid');
        emailInput.classList.add('is-valid');
    } else {
        emailInput.setCustomValidity('Please enter a valid email address');
        emailInput.classList.remove('is-valid');
        emailInput.classList.add('is-invalid');
    }
});

// Prevent numbers from being entered in the customer name field
const nameInput = document.getElementById('name');
if (nameInput) {
  // Block numbers on keypress
  nameInput.addEventListener('keypress', function(e) {
    if (e.key.match(/[0-9]/)) {
      e.preventDefault();
    }
  });
  // Block numbers on paste
  nameInput.addEventListener('paste', function(e) {
    const paste = (e.clipboardData || window.clipboardData).getData('text');
    if (/[^a-zA-Z ]/.test(paste)) {
      e.preventDefault();
    }
  });
  // Remove numbers on input (for autofill, drag, etc.)
  nameInput.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^a-zA-Z ]/g, '');
  });
}
const saleForm = nameInput ? nameInput.form : null;
if (saleForm) {
  saleForm.addEventListener('submit', function(e) {
    const value = nameInput.value;
    if (!/^[a-zA-Z ]+$/.test(value)) {
      alert('Customer name should contain only letters and spaces.');
      nameInput.focus();
      e.preventDefault();
    }
  });
}

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    var phoneInput = document.getElementById('pNumber');
    var emailInput = document.getElementById('email');
    var quantityInput = document.getElementById('quantity');
    var unitPriceInput = document.getElementById('sale_selling_price');
    var discountInput = document.getElementById('discount');
    var discountType = document.getElementById('discount_type').value;
    var isValid = true;
    
    // Validate phone number
    if (phoneInput.value.length !== 10) {
        phoneInput.setCustomValidity('Phone number must be exactly 10 digits');
        phoneInput.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate email if provided
    if (emailInput.value.trim() !== '') {
        var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(emailInput.value.trim())) {
            emailInput.setCustomValidity('Please enter a valid email address');
            emailInput.classList.add('is-invalid');
            isValid = false;
        }
    }
    
    // Validate quantity
    if (quantityInput.value <= 0) {
        quantityInput.setCustomValidity('Quantity must be greater than 0');
        quantityInput.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate unit price
    if (unitPriceInput.value <= 0) {
        unitPriceInput.setCustomValidity('Unit price must be greater than 0');
        unitPriceInput.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate products and categories
    var productRows = document.querySelectorAll('.product-row');
    productRows.forEach(function(row) {
        var categorySelect = row.querySelector('.category-select');
        var productSelect = row.querySelector('.product-select');
        var quantityInput = row.querySelector('.quantity');
        var selectedOption = productSelect.options[productSelect.selectedIndex];
        
        // Check if category is selected but no product is available
        if (categorySelect.value && productSelect.disabled) {
            categorySelect.setCustomValidity('No products available in this category. Please select a different category.');
            categorySelect.classList.add('is-invalid');
            isValid = false;
        }
        
        // Check if product is selected but no valid option
        if (categorySelect.value && !productSelect.disabled && selectedOption && selectedOption.value === '') {
            productSelect.setCustomValidity('Please select a product from the available options.');
            productSelect.classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate quantity against available stock
        if (selectedOption && selectedOption.dataset.quantity) {
            var availableStock = parseInt(selectedOption.dataset.quantity);
            var requestedQuantity = parseInt(quantityInput.value);
            
            if (requestedQuantity > availableStock) {
                quantityInput.setCustomValidity('Quantity cannot exceed available stock: ' + availableStock);
                quantityInput.classList.add('is-invalid');
                isValid = false;
            }
        }
        
        // Validate quantity is greater than 0
        if (quantityInput.value <= 0) {
            quantityInput.setCustomValidity('Quantity must be greater than 0');
            quantityInput.classList.add('is-invalid');
            isValid = false;
        }
    });
    
    // Validate discount
    var subtotal = parseFloat(unitPriceInput.value) * parseInt(quantityInput.value);
    var discount = parseFloat(discountInput.value);
    
    if (discount > 0) {
        if (discountType === 'percentage') {
            if (discount > 100) {
                discountInput.setCustomValidity('Discount percentage cannot be more than 100%');
                discountInput.classList.add('is-invalid');
                isValid = false;
            }
        } else {
            if (discount >= subtotal) {
                discountInput.setCustomValidity('Discount amount cannot be greater than or equal to subtotal');
                discountInput.classList.add('is-invalid');
                isValid = false;
            }
        }
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fix the validation errors before submitting the form.');
    }
});
</script>

<?php include_once('layouts/footer.php'); ?>

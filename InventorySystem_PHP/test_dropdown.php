<?php
// Test page for dropdown functionality
require_once('includes/load.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Product Dropdown</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Product Dropdown</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product_dropdown" class="form-label">Select Product</label>
                    <select class="form-control" id="product_dropdown" name="product_dropdown">
                        <option value="">-- Select Product --</option>
                        <?php
                        $all_products = find_all('products');
                        if($all_products):
                            foreach($all_products as $product):
                        ?>
                        <option value="<?php echo $product['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-sale-price="<?php echo $product['sale_price']; ?>"
                                data-buy-price="<?php echo $product['buy_price']; ?>"
                                data-quantity="<?php echo $product['quantity']; ?>"
                                data-supplier-id="<?php echo $product['supplier_id'] ?? ''; ?>"
                                data-category-id="<?php echo $product['categorie_id']; ?>"
                                data-expiry-date="<?php echo $product['expiry_date'] ?? ''; ?>">
                            <?php echo $product['name']; ?> (ID: <?php echo $product['id']; ?>)
                        </option>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div id="product_details" class="card" style="display: none;">
                    <div class="card-header">
                        <h5>Product Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Product ID:</strong> <span id="product_id"></span></p>
                                <p><strong>Product Name:</strong> <span id="product_name"></span></p>
                                <p><strong>Available Stock:</strong> <span id="current_stock"></span></p>
                                <p><strong>Category ID:</strong> <span id="category_id"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Buy Price:</strong> <span id="buy_price"></span></p>
                                <p><strong>Sale Price:</strong> <span id="sale_price"></span></p>
                                <p><strong>Supplier ID:</strong> <span id="supplier_id"></span></p>
                                <p><strong>Expiry Date:</strong> <span id="expiry_date"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>All Products in Database:</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Sale Price</th>
                            <th>Buy Price</th>
                            <th>Supplier ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($all_products):
                            foreach($all_products as $product):
                        ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>$<?php echo number_format($product['sale_price'], 2); ?></td>
                            <td>$<?php echo number_format($product['buy_price'], 2); ?></td>
                            <td><?php echo $product['supplier_id'] ?? 'No Supplier'; ?></td>
                        </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">No products found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('product_dropdown');
        const detailsDiv = document.getElementById('product_details');
        
        dropdown.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                // Show product details
                detailsDiv.style.display = 'block';
                
                // Fill in the details
                document.getElementById('product_id').textContent = selectedOption.value;
                document.getElementById('product_name').textContent = selectedOption.dataset.name;
                document.getElementById('current_stock').textContent = selectedOption.dataset.quantity + ' units';
                document.getElementById('category_id').textContent = selectedOption.dataset.categoryId;
                document.getElementById('buy_price').textContent = '$' + parseFloat(selectedOption.dataset.buyPrice).toFixed(2);
                document.getElementById('sale_price').textContent = '$' + parseFloat(selectedOption.dataset.salePrice).toFixed(2);
                document.getElementById('supplier_id').textContent = selectedOption.dataset.supplierId || 'No Supplier';
                document.getElementById('expiry_date').textContent = selectedOption.dataset.expiryDate || 'No Expiry Date';
            } else {
                // Hide product details
                detailsDiv.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>

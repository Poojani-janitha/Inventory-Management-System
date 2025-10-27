<?php
/**
 * Add Sample Products Script
 * This script adds sample products to the database if none exist
 */

require_once('includes/load.php');

echo "<!DOCTYPE html>";
echo "<html><head><title>Add Sample Products</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; }
</style></head><body>";

echo "<h1>📦 Add Sample Products</h1>";

// Check if products exist
$existing_products = find_all('products');
$product_count = $existing_products ? count($existing_products) : 0;

echo "<p>Current products in database: <strong>$product_count</strong></p>";

if($product_count > 0) {
    echo "<p class='info'>Products already exist in database. No need to add samples.</p>";
    echo "<h3>Existing Products:</h3>";
    echo "<ul>";
    foreach($existing_products as $product) {
        echo "<li>" . htmlspecialchars($product['name']) . " (ID: " . $product['id'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='info'>No products found. Adding sample products...</p>";
    
    // Sample products data
    $sample_products = [
        [
            'name' => 'Demo Product 1',
            'quantity' => '100',
            'buy_price' => '10.00',
            'sale_price' => '15.00',
            'categorie_id' => 1
        ],
        [
            'name' => 'Demo Product 2',
            'quantity' => '50',
            'buy_price' => '20.00',
            'sale_price' => '30.00',
            'categorie_id' => 1
        ],
        [
            'name' => 'Demo Product 3',
            'quantity' => '75',
            'buy_price' => '5.00',
            'sale_price' => '8.00',
            'categorie_id' => 1
        ],
        [
            'name' => 'Sample Medicine 1',
            'quantity' => '200',
            'buy_price' => '25.00',
            'sale_price' => '35.00',
            'categorie_id' => 1,
            'expiry_date' => '2024-12-31'
        ],
        [
            'name' => 'Sample Medicine 2',
            'quantity' => '150',
            'buy_price' => '15.00',
            'sale_price' => '22.00',
            'categorie_id' => 1,
            'expiry_date' => '2025-06-30'
        ]
    ];
    
    $success_count = 0;
    $error_count = 0;
    
    foreach($sample_products as $product_data) {
        try {
            $sql = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, date";
            $values = "VALUES ('" . $product_data['name'] . "', '" . $product_data['quantity'] . "', '" . $product_data['buy_price'] . "', '" . $product_data['sale_price'] . "', '" . $product_data['categorie_id'] . "', NOW()";
            
            if(isset($product_data['expiry_date'])) {
                $sql .= ", expiry_date";
                $values .= ", '" . $product_data['expiry_date'] . "'";
            }
            
            $sql .= ") " . $values . ")";
            
            if($db->query($sql)) {
                echo "<p class='success'>✅ Added: " . $product_data['name'] . "</p>";
                $success_count++;
            } else {
                echo "<p class='error'>❌ Failed to add: " . $product_data['name'] . "</p>";
                $error_count++;
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error adding " . $product_data['name'] . ": " . $e->getMessage() . "</p>";
            $error_count++;
        }
    }
    
    echo "<h3>Summary:</h3>";
    echo "<p class='success'>✅ Successfully added: $success_count products</p>";
    if($error_count > 0) {
        echo "<p class='error'>❌ Failed to add: $error_count products</p>";
    }
}

// Test the dropdown after adding products
echo "<h2>Test Dropdown Generation</h2>";
$all_products = find_all('products');
if($all_products && count($all_products) > 0) {
    echo "<p class='success'>✅ Dropdown will now show " . count($all_products) . " products</p>";
    
    echo "<h3>Generated Dropdown Preview:</h3>";
    echo "<select style='width: 100%; padding: 10px; font-size: 14px;'>";
    echo "<option value=''>-- Select Product --</option>";
    
    foreach($all_products as $product) {
        echo "<option value='" . $product['id'] . "' ";
        echo "data-name='" . htmlspecialchars($product['name']) . "' ";
        echo "data-sale-price='" . $product['sale_price'] . "' ";
        echo "data-buy-price='" . $product['buy_price'] . "' ";
        echo "data-quantity='" . $product['quantity'] . "' ";
        echo "data-supplier-id='" . ($product['supplier_id'] ?? '') . "' ";
        echo "data-category-id='" . $product['categorie_id'] . "' ";
        echo "data-expiry-date='" . ($product['expiry_date'] ?? '') . "'>";
        echo htmlspecialchars($product['name']) . " (ID: " . $product['id'] . ")";
        echo "</option>";
    }
    echo "</select>";
    
    echo "<p class='info'>This dropdown will now work in the Add Return form!</p>";
} else {
    echo "<p class='error'>❌ Still no products found. Check database connection.</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Go to <a href='add_return.php'>Add Return</a> page</li>";
echo "<li>Check if the dropdown shows products</li>";
echo "<li>Select a product and verify auto-fill works</li>";
echo "<li>Test the complete return process</li>";
echo "</ol>";

echo "</body></html>";
?>

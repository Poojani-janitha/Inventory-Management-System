<?php
require_once('includes/load.php');

echo "<h2>Database Structure Test</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
try {
    $db = new MySqli_DB();
    echo "✓ Database connection successful<br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test table existence
echo "<h3>2. Table Existence Test</h3>";
$tables = ['categories', 'supplier_info', 'product', 'sales', 'media', 'users', 'user_groups'];
foreach ($tables as $table) {
    if (tableExists($table)) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' does not exist<br>";
    }
}

// Test data retrieval
echo "<h3>3. Data Retrieval Test</h3>";

// Test categories
$categories = find_all('categories');
echo "✓ Found " . count($categories) . " categories<br>";
if (count($categories) > 0) {
    echo "Sample category: " . $categories[0]['category_name'] . "<br>";
}

// Test products
$products = join_product_table();
echo "✓ Found " . count($products) . " products<br>";
if (count($products) > 0) {
    echo "Sample product: " . $products[0]['name'] . " (ID: " . $products[0]['id'] . ")<br>";
}

// Test sales
$sales = find_all_sale();
echo "✓ Found " . count($sales) . " sales records<br>";
if (count($sales) > 0) {
    echo "Sample sale: " . $sales[0]['name'] . " - Qty: " . $sales[0]['qty'] . "<br>";
}

// Test suppliers
$suppliers = find_all('supplier_info');
echo "✓ Found " . count($suppliers) . " suppliers<br>";
if (count($suppliers) > 0) {
    echo "Sample supplier: " . $suppliers[0]['s_name'] . " (ID: " . $suppliers[0]['s_id'] . ")<br>";
}

echo "<h3>4. Test Complete</h3>";
echo "If all tests show ✓, the database structure is working correctly!<br>";
echo "<a href='index.php'>Go to Login Page</a>";
?>

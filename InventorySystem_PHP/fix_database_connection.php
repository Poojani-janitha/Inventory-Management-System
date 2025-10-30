<?php
/**
 * Database Connection Fix and Test Script
 * This script will test and fix database connectivity issues
 */

// Include the load file to get all necessary functions
require_once('includes/load.php');

echo "<!DOCTYPE html>";
echo "<html><head><title>Database Connection Fix</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.info { color: blue; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style></head><body>";

echo "<h1>🔧 Database Connection Fix & Test</h1>";

// Test 1: Check if database connection exists
echo "<h2>1. Database Connection Test</h2>";
if(isset($db) && $db) {
    echo "<p class='success'>✅ Database connection object exists</p>";
} else {
    echo "<p class='error'>❌ Database connection object not found</p>";
    echo "<p class='info'>Trying to create new connection...</p>";
    
    try {
        $db = new MySqli_DB();
        echo "<p class='success'>✅ New database connection created</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Failed to create database connection: " . $e->getMessage() . "</p>";
    }
}

// Test 2: Check database configuration
echo "<h2>2. Database Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
echo "<tr><td>DB_HOST</td><td>" . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "</td><td>" . (defined('DB_HOST') ? '✅' : '❌') . "</td></tr>";
echo "<tr><td>DB_NAME</td><td>" . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</td><td>" . (defined('DB_NAME') ? '✅' : '❌') . "</td></tr>";
echo "<tr><td>DB_USER</td><td>" . (defined('DB_USER') ? DB_USER : 'Not defined') . "</td><td>" . (defined('DB_USER') ? '✅' : '❌') . "</td></tr>";
echo "<tr><td>DB_PASS</td><td>" . (defined('DB_PASS') ? (DB_PASS ? 'Set' : 'Empty') : 'Not defined') . "</td><td>" . (defined('DB_PASS') ? '✅' : '❌') . "</td></tr>";
echo "</table>";

// Test 3: Test basic database query
echo "<h2>3. Basic Database Query Test</h2>";
try {
    $sql = "SELECT 1 as test";
    $result = $db->query($sql);
    if($result) {
        echo "<p class='success'>✅ Basic database query successful</p>";
    } else {
        echo "<p class='error'>❌ Basic database query failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database query error: " . $e->getMessage() . "</p>";
}

// Test 4: Check if products table exists
echo "<h2>4. Products Table Check</h2>";
try {
    $sql = "SHOW TABLES LIKE 'products'";
    $result = $db->query($sql);
    $table_exists = $db->num_rows($result) > 0;
    
    if($table_exists) {
        echo "<p class='success'>✅ Products table exists</p>";
        
        // Check table structure
        $sql = "DESCRIBE products";
        $result = $db->query($sql);
        echo "<h3>Products Table Structure:</h3>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while($row = $db->fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p class='error'>❌ Products table does not exist</p>";
        echo "<p class='info'>You need to run the database setup script</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking products table: " . $e->getMessage() . "</p>";
}

// Test 5: Test find_all function
echo "<h2>5. find_all() Function Test</h2>";
try {
    if(function_exists('find_all')) {
        echo "<p class='success'>✅ find_all() function exists</p>";
        
        $products = find_all('products');
        if($products && is_array($products)) {
            echo "<p class='success'>✅ find_all('products') returned " . count($products) . " products</p>";
            
            if(count($products) > 0) {
                echo "<h3>Sample Products:</h3>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Name</th><th>Quantity</th><th>Sale Price</th><th>Buy Price</th><th>Category ID</th><th>Supplier ID</th></tr>";
                
                $count = 0;
                foreach($products as $product) {
                    if($count >= 5) break; // Show only first 5 products
                    echo "<tr>";
                    echo "<td>" . $product['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                    echo "<td>" . $product['quantity'] . "</td>";
                    echo "<td>$" . number_format($product['sale_price'], 2) . "</td>";
                    echo "<td>$" . number_format($product['buy_price'], 2) . "</td>";
                    echo "<td>" . $product['categorie_id'] . "</td>";
                    echo "<td>" . ($product['supplier_id'] ?? 'No Supplier') . "</td>";
                    echo "</tr>";
                    $count++;
                }
                echo "</table>";
            } else {
                echo "<p class='warning'>⚠️ No products found in database</p>";
            }
        } else {
            echo "<p class='error'>❌ find_all('products') returned no data or error</p>";
        }
    } else {
        echo "<p class='error'>❌ find_all() function does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error testing find_all(): " . $e->getMessage() . "</p>";
}

// Test 6: Test dropdown data generation
echo "<h2>6. Dropdown Data Generation Test</h2>";
try {
    $all_products = find_all('products');
    if($all_products && count($all_products) > 0) {
        echo "<p class='success'>✅ Products data available for dropdown</p>";
        
        echo "<h3>Generated Dropdown Options:</h3>";
        echo "<select style='width: 100%; padding: 10px;'>";
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
        
    } else {
        echo "<p class='error'>❌ No products available for dropdown</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error generating dropdown data: " . $e->getMessage() . "</p>";
}

// Test 7: Recommendations
echo "<h2>7. Recommendations</h2>";
echo "<ul>";
echo "<li>Make sure your database server (MySQL/MariaDB) is running</li>";
echo "<li>Check that the database name, username, and password in config.php are correct</li>";
echo "<li>Ensure the products table exists and has data</li>";
echo "<li>If products table is empty, add some sample products</li>";
echo "<li>Check file permissions for the includes directory</li>";
echo "</ul>";

echo "<h2>8. Quick Fix Commands</h2>";
echo "<p>If you need to create sample data, run these SQL commands:</p>";
echo "<pre>";
echo "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, date) VALUES\n";
echo "('Sample Product 1', '100', '10.00', '15.00', 1, NOW()),\n";
echo "('Sample Product 2', '50', '20.00', '30.00', 1, NOW()),\n";
echo "('Sample Product 3', '75', '5.00', '8.00', 1, NOW());";
echo "</pre>";

echo "</body></html>";
?>

<?php
// Test database connection and product fetching
require_once('includes/load.php');

echo "<h2>Database Connection Test</h2>";

// Test database connection
if($db) {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
}

// Test find_all function
echo "<h3>Testing find_all('products') function:</h3>";
try {
    $products = find_all('products');
    if($products) {
        echo "<p style='color: green;'>✅ Products fetched successfully! Found " . count($products) . " products.</p>";
        
        echo "<h4>Products in Database:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Quantity</th><th>Sale Price</th><th>Buy Price</th><th>Category ID</th><th>Supplier ID</th></tr>";
        
        foreach($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . htmlspecialchars($product['name']) . "</td>";
            echo "<td>" . $product['quantity'] . "</td>";
            echo "<td>$" . number_format($product['sale_price'], 2) . "</td>";
            echo "<td>$" . number_format($product['buy_price'], 2) . "</td>";
            echo "<td>" . $product['categorie_id'] . "</td>";
            echo "<td>" . ($product['supplier_id'] ?? 'No Supplier') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No products found or error occurred!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test direct database query
echo "<h3>Testing Direct Database Query:</h3>";
try {
    $sql = "SELECT * FROM products ORDER BY name ASC";
    $result = $db->query($sql);
    
    if($result) {
        $products_direct = $db->while_loop($result);
        echo "<p style='color: green;'>✅ Direct query successful! Found " . count($products_direct) . " products.</p>";
    } else {
        echo "<p style='color: red;'>❌ Direct query failed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Direct query error: " . $e->getMessage() . "</p>";
}

// Test database configuration
echo "<h3>Database Configuration:</h3>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>User:</strong> " . DB_USER . "</p>";

// Test if products table exists
echo "<h3>Testing Products Table Structure:</h3>";
try {
    $sql = "DESCRIBE products";
    $result = $db->query($sql);
    
    if($result) {
        echo "<p style='color: green;'>✅ Products table exists!</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
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
        echo "<p style='color: red;'>❌ Products table does not exist or cannot be accessed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Table structure error: " . $e->getMessage() . "</p>";
}
?>

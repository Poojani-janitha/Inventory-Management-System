<?php
// Simple test file to verify product search functionality
require_once('includes/load.php');

// Test the search functionality
if(isset($_GET['test'])) {
    $search_term = $_GET['term'] ?? 'demo';
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.categorie_id = c.id 
            WHERE p.name LIKE '%{$search_term}%' 
            ORDER BY p.name ASC 
            LIMIT 10";
    
    $products = $db->while_loop($db->query($sql));
    
    echo "<h3>Search Results for: '{$search_term}'</h3>";
    echo "<pre>";
    print_r($products);
    echo "</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Return Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Product Search</h2>
        <form method="get">
            <div class="mb-3">
                <label for="term" class="form-label">Search Term:</label>
                <input type="text" class="form-control" name="term" id="term" value="demo" placeholder="Enter product name to search">
            </div>
            <button type="submit" name="test" class="btn btn-primary">Test Search</button>
        </form>
        
        <hr>
        
        <h3>Available Products:</h3>
        <?php
        $all_products = find_all('products');
        if($all_products):
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Sale Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($all_products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td>$<?php echo number_format($product['sale_price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No products found in database.</p>
        <?php endif; ?>
    </div>
</body>
</html>

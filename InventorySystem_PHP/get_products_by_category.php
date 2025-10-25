<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
    $category_name = $db->escape($_POST['category_name']);
    
    // Get products for the selected category with available stock
    $sql = "SELECT p_id, product_name, selling_price, quantity 
            FROM product 
            WHERE category_name = '{$category_name}' 
            AND quantity > 0 
            ORDER BY product_name";
    
    $result = $db->query($sql);
    $products = [];
    
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetch_assoc($result)) {
            $products[] = [
                'p_id' => $row['p_id'],
                'product_name' => $row['product_name'],
                'selling_price' => $row['selling_price'],
                'quantity' => $row['quantity']
            ];
        }
    }
    
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>


<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['product_id']) && isset($input['quantity'])) {
        $product_id = $db->escape($input['product_id']);
        $quantity = (int)$input['quantity'];
        
        // Check if product exists and has enough stock
        $check_sql = "SELECT quantity FROM product WHERE p_id = '{$product_id}'";
        $result = $db->query($check_sql);
        
        if ($result && $db->num_rows($result) > 0) {
            $row = $db->fetch_assoc($result);
            $current_stock = (int)$row['quantity'];
            
            if ($current_stock >= $quantity) {
                // Update stock
                $update_sql = "UPDATE product SET quantity = quantity - {$quantity} WHERE p_id = '{$product_id}'";
                if ($db->query($update_sql)) {
                    $new_stock = $current_stock - $quantity;
                    echo json_encode([
                        'success' => true,
                        'new_stock' => $new_stock,
                        'message' => 'Stock updated successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update stock'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $current_stock . ', Requested: ' . $quantity
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>

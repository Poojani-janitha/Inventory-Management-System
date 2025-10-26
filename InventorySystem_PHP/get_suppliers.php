<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if (isset($_GET['product']) && !empty($_GET['product'])) {
    $product = remove_junk($db->escape($_GET['product']));
    
    // Get suppliers who supply this product with their prices
    $sql = "SELECT si.s_id, si.s_name, si.contact_number, si.email, sp.price
            FROM supplier_info si
            JOIN supplier_product sp ON si.s_id = sp.s_id
            WHERE sp.product_name = '{$product}'
            ORDER BY sp.price ASC";
    
    $result = $db->query($sql);

    $suppliers = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = [
                'id' => $row['s_id'],
                'name' => $row['s_name'] . ' (Rs. ' . number_format($row['price'], 2) . ')',
                'contact' => $row['contact_number'],
                'email' => $row['email'],
                'price' => $row['price']
            ];
        }
    }
    
    echo json_encode($suppliers);
} else {
    echo json_encode([]);
}
?>


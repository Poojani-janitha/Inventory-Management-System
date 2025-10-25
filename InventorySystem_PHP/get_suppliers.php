<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if (isset($_POST['product_name']) && !empty($_POST['product_name'])) {
    $product = remove_junk($db->escape($_POST['product_name']));
    
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
            $suppliers[] = $row;
        }
    }
    
    echo json_encode($suppliers);
} else {
    echo json_encode([]);
}
?>


<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = remove_junk($db->escape($_GET['category']));
    
    $sql = "SELECT DISTINCT product_name FROM supplier_product WHERE category_name = '{$category}'";
    $result = $db->query($sql);

    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row['product_name'];
        }
    }
    
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>

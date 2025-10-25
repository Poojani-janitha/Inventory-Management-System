<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
    $category = remove_junk($db->escape($_POST['category_name']));
    
    $sql = "SELECT DISTINCT product_name FROM supplier_product WHERE category_name = '{$category}'";
    $result = $db->query($sql);

    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>
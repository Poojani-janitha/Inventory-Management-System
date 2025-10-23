<?php
require_once('includes/load.php');
header('Content-Type: application/json');

if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
    $category_id = (int)$_POST['category_id'];
    $sql = "SELECT id, name FROM products WHERE categorie_id = '{$category_id}'";
    $result = $db->query($sql);
    $products = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>

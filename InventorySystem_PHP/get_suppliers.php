<?php
require_once('includes/load.php');

// Set header for JSON response
header('Content-Type: application/json');

// Check if product_id is set
if(isset($_POST['product_id']) && !empty($_POST['product_id'])){
    $product_id = (int)$_POST['product_id'];
    
    // Query to get suppliers for the selected product
    $sql = "SELECT id, supplier_name, contact_number, email FROM suppliers WHERE product_id = '{$product_id}'";
    $result = $db->query($sql);
    
    $suppliers = array();
    if($result){
        while($row = $result->fetch_assoc()){
            $suppliers[] = $row;
        }
    }
    
    // Return JSON response
    echo json_encode($suppliers);
} else {
    echo json_encode(array());
}
?>
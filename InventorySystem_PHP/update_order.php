<?php
require_once('includes/load.php');
if(isset($_POST['o_id'])){
    $o_id = (int)$_POST['o_id'];
    $quantity = (int)$_POST['quantity'];
    $status = remove_junk($db->escape($_POST['status']));

    $sql = "UPDATE purchase_order SET quantity='{$quantity}', status='{$status}' WHERE o_id='{$o_id}'";
    if($db->query($sql)){
        echo "Order updated successfully.";
    } else {
        echo "Failed to update order.";
    }
}
?>

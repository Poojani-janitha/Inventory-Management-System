<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  $return = find_return_by_id((int)$_GET['id']);
  if(!$return){
    $session->msg("d","Missing Return ID.");
    redirect('returns.php');
  }
?>
<?php
  // Get the product details to restore quantity
  $product = find_by_id('product', $return['p_id']);
  
  if($product) {
    // Restore the quantity back to product
    $restored_qty = $product['quantity'] + $return['return_quantity'];
    $update_query = "UPDATE product SET quantity = '{$restored_qty}' WHERE p_id = '{$return['p_id']}'";
    $db->query($update_query);
  }
  
  // Delete the return record
  $delete_id = delete_by_id('return_details', (int)$return['return_id']);
  if($delete_id){
      $session->msg("s","Return deleted successfully. Stock quantity has been restored.");
      redirect('returns.php');
  } else {
      $session->msg("d","Return deletion failed.");
      redirect('returns.php');
  }
?>

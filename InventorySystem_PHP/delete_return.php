<?php
  $page_title = 'Delete Return';
  require_once('includes/load.php');
  page_require_level(1);
?>
<?php
  // Check if return ID is provided
  if(isset($_GET['id'])){
    $return_id = (int)$_GET['id'];
    
    // First, get the return details to restore stock
    $return = find_by_id('return_details', $return_id);
    
    if(!$return){
      $session->msg('d', "Return not found.");
      redirect('returns.php', false);
    }
    
    // Delete from return_details table
    $query = "DELETE FROM return_details WHERE return_id='{$return_id}'";
    
    if($db->query($query)){
      
      // Restore the stock back to products table
      $restore_stock_query = "UPDATE product SET quantity = quantity + '{$return['return_quantity']}' WHERE p_id = '{$return['p_id']}'";
      $db->query($restore_stock_query);
      
      $session->msg('s', "Return deleted successfully. Stock has been restored.");
      redirect('returns.php', false);
    } else {
      $session->msg('d', 'Sorry failed to delete!');
      redirect('returns.php', false);
    }
  } else {
    $session->msg('d', "Missing return ID.");
    redirect('returns.php', false);
  }
?>


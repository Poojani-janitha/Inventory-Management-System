<?php
  require_once('includes/load.php');
  // Check what level user has permission to view this page
  page_require_level(2);
  
  // Check if ID parameter exists
  if(!isset($_GET['id']) || empty($_GET['id'])){
    $session->msg("d","Missing Product id.");
    redirect('product.php');
  }

  $product_id = $_GET['id'];
  
  // Delete the product using direct SQL query
  global $db;
  
  // Escape the product ID to prevent SQL injection
  $escaped_id = $db->escape($product_id);
  
  // Create DELETE query
  $sql = "DELETE FROM product WHERE p_id = '{$escaped_id}' LIMIT 1";
  
  // Execute the query
  $result = $db->query($sql);
  
  // Check if deletion was successful
  if($result && $db->affected_rows() === 1){
      $session->msg("s","Product deleted successfully.");
      redirect('product.php');
  } else {
      $session->msg("d","Product deletion failed or product not found.");
      redirect('product.php');
  }
?>
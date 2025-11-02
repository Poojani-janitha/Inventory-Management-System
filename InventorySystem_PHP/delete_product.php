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
  
  // Get product details first
  global $db;
  $escaped_id = $db->escape($product_id);
  
  // Get product information
  $product_sql = "SELECT * FROM product WHERE p_id = '{$escaped_id}' LIMIT 1";
  $product_result = $db->query($product_sql);
  
  if(!$product_result || $db->num_rows($product_result) === 0) {
    $session->msg("d","Product not found.");
    redirect('product.php');
    exit();
  }
  
  $product = $db->fetch_assoc($product_result);
  $product_name = $product['product_name'];
  
  // Check if product is being used in other tables before deletion
  
  // Check if product is used in sales
  $check_sales = "SELECT COUNT(*) as count FROM sales WHERE sale_product_id = '{$escaped_id}'";
  $sales_result = $db->query($check_sales);
  $sales_count = $db->fetch_assoc($sales_result)['count'];
  
  // Check if product is used in return_details
  $check_returns = "SELECT COUNT(*) as count FROM return_details WHERE p_id = '{$escaped_id}'";
  $returns_result = $db->query($check_returns);
  $returns_count = $db->fetch_assoc($returns_result)['count'];
  
  // If product is being used, prevent deletion
  if($sales_count > 0 || $returns_count > 0) {
      $usage_details = [];
      if($sales_count > 0) $usage_details[] = "{$sales_count} sales record(s)";
      if($returns_count > 0) $usage_details[] = "{$returns_count} return record(s)";
      
      $usage_text = implode(', ', $usage_details);
      $session->msg("d", "Cannot delete product '{$product_name}' (ID: {$product_id}) because it is being used by: {$usage_text}. Please remove these transaction records first or consider marking the product as inactive instead.");
      redirect('product.php');
      exit();
  }
  
  // If no dependencies, proceed with deletion
  $sql = "DELETE FROM product WHERE p_id = '{$escaped_id}' LIMIT 1";
  $result = $db->query($sql);
  
  // Check if deletion was successful
  if($result && $db->affected_rows() === 1){
      $session->msg("s","Product '{$product_name}' (ID: {$product_id}) deleted successfully.");
      redirect('product.php');
  } else {
      $session->msg("d","Product deletion failed. Database error occurred.");
      redirect('product.php');
  }
?>
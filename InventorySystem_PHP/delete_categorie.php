<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  // Get category using correct column name for new database structure
  $categorie_id = (int)$_GET['id'];
  $sql = "SELECT * FROM categories WHERE c_id = '{$categorie_id}' LIMIT 1";
  $result = $db->query($sql);
  $categorie = $db->fetch_assoc($result);
  
  if(!$categorie){
    $session->msg("d","Missing Category id.");
    redirect('categorie.php');
    exit();
  }
?>
<?php
  // Check if category is being used in other tables before deletion
  $category_name = $categorie['category_name'];
  
  // Check if category is used in products
  $check_products = "SELECT COUNT(*) as count FROM product WHERE category_name = '{$category_name}'";
  $product_result = $db->query($check_products);
  $product_count = $db->fetch_assoc($product_result)['count'];
  
  // Check if category is used in supplier_product
  $check_supplier_products = "SELECT COUNT(*) as count FROM supplier_product WHERE category_name = '{$category_name}'";
  $supplier_product_result = $db->query($check_supplier_products);
  $supplier_product_count = $db->fetch_assoc($supplier_product_result)['count'];
  
  // Check if category is used in purchase_order
  $check_orders = "SELECT COUNT(*) as count FROM purchase_order WHERE category_name = '{$category_name}'";
  $order_result = $db->query($check_orders);
  $order_count = $db->fetch_assoc($order_result)['count'];
  
  // Check if category is used in sales
  $check_sales = "SELECT COUNT(*) as count FROM sales WHERE category_name = '{$category_name}'";
  $sales_result = $db->query($check_sales);
  $sales_count = $db->fetch_assoc($sales_result)['count'];
  
  // If category is being used, prevent deletion
  if($product_count > 0 || $supplier_product_count > 0 || $order_count > 0 || $sales_count > 0) {
      $usage_details = [];
      if($product_count > 0) $usage_details[] = "{$product_count} product(s)";
      if($supplier_product_count > 0) $usage_details[] = "{$supplier_product_count} supplier product(s)";
      if($order_count > 0) $usage_details[] = "{$order_count} purchase order(s)";
      if($sales_count > 0) $usage_details[] = "{$sales_count} sales record(s)";
      
      $usage_text = implode(', ', $usage_details);
      $session->msg("d", "Cannot delete category '{$category_name}' because it is being used by: {$usage_text}. Please remove or reassign these records first.");
      redirect('categorie.php');
      exit();
  }
  
  // If no dependencies, proceed with deletion
  $sql = "DELETE FROM categories WHERE c_id = '{$categorie_id}' LIMIT 1";
  $result = $db->query($sql);
  
  if($result && $db->affected_rows() === 1){
      $session->msg("s","Category '{$category_name}' deleted successfully.");
      redirect('categorie.php');
      exit();
  } else {
      $session->msg("d","Category deletion failed. Database error occurred.");
      redirect('categorie.php');
      exit();
  }
?>

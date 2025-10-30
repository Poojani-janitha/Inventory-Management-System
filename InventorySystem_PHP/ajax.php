<?php
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>

<?php
 // Auto suggetion
    $html = '';
   if(isset($_POST['product_name']) && strlen($_POST['product_name']))
   {
     $products = find_product_by_title($_POST['product_name']);
     if($products){
        foreach ($products as $product):
           $html .= "<li class=\"list-group-item\">";
           $html .= $product['name'];
           $html .= "</li>";
         endforeach;
      } else {

        $html .= '<li onClick=\"fill(\''.addslashes().'\')\" class=\"list-group-item\">';
        $html .= 'Not found';
        $html .= "</li>";

      }

      echo json_encode($html);
   }
 ?>

<?php
// Get product information for return form
if(isset($_GET['action']) && $_GET['action'] == 'get_product_info' && isset($_GET['id'])) {
  $product_id = (int)$_GET['id'];
  $product = find_by_id('products', $product_id);
  
  if($product) {
    // Get category name
    $category = find_by_id('categories', $product['categorie_id']);
    $product['category'] = $category ? $category['name'] : 'Unknown';
    
    echo json_encode([
      'success' => true,
      'product' => $product
    ]);
  } else {
    echo json_encode([
      'success' => false,
      'message' => 'Product not found'
    ]);
  }
  exit;
}

// Get active alerts
if(isset($_GET['action']) && $_GET['action'] == 'get_alerts') {
  $alerts = find_active_return_alerts();
  echo json_encode([
    'success' => true,
    'alerts' => $alerts
  ]);
  exit;
}

// Resolve alert
if(isset($_GET['action']) && $_GET['action'] == 'resolve_alert' && isset($_GET['id'])) {
  $alert_id = (int)$_GET['id'];
  $user_id = (int)$_SESSION['user_id'];
  
  $sql = "UPDATE return_alerts SET is_resolved = 1, resolved_by = '{$user_id}', resolved_at = NOW() WHERE id = '{$alert_id}'";
  
  if($db->query($sql)) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'error' => 'Failed to resolve alert']);
  }
  exit;
}

// Search products by name
if(isset($_GET['action']) && $_GET['action'] == 'search_products' && isset($_GET['term'])) {
  $search_term = remove_junk($db->escape($_GET['term']));
  
  $sql = "SELECT p.*, c.name as category_name, s.id as supplier_id, s.name as supplier_name, s.email as supplier_email
          FROM products p 
          LEFT JOIN categories c ON p.categorie_id = c.id 
          LEFT JOIN suppliers s ON p.supplier_id = s.id
          WHERE p.name LIKE '%{$search_term}%' 
          ORDER BY p.name ASC 
          LIMIT 10";
  
  $products = $db->while_loop($db->query($sql));
  
  echo json_encode([
    'success' => true,
    'products' => $products
  ]);
  exit;
}

// Get supplier information
if(isset($_GET['action']) && $_GET['action'] == 'get_supplier_info' && isset($_GET['id'])) {
  $supplier_id = (int)$_GET['id'];
  $supplier = find_by_id('suppliers', $supplier_id);
  
  if($supplier) {
    echo json_encode([
      'success' => true,
      'supplier' => $supplier
    ]);
  } else {
    echo json_encode([
      'success' => false,
      'message' => 'Supplier not found'
    ]);
  }
  exit;
}
?>
 <?php
 // find all product
  if(isset($_POST['p_name']) && strlen($_POST['p_name']))
  {
    $product_title = remove_junk($db->escape($_POST['p_name']));
    if($results = find_all_product_info_by_title($product_title)){
        foreach ($results as $result) {

          $html .= "<tr>";

          $html .= "<td id=\"s_name\">".$result['product_name']."</td>";
          $html .= "<input type=\"hidden\" name=\"s_id\" value=\"{$result['p_id']}\">";
          $html .= "<input type=\"hidden\" name=\"product_name\" value=\"{$result['product_name']}\">";
          $html  .= "<td>";
          $html  .= "<input type=\"text\" class=\"form-control\" name=\"price\" value=\"{$result['selling_price']}\">";
          $html  .= "</td>";
          $html .= "<td id=\"s_qty\">";
          $html .= "<input type=\"text\" class=\"form-control\" name=\"quantity\" value=\"1\">";
          $html  .= "</td>";
          $html  .= "<td>";
          $html  .= "<input type=\"text\" class=\"form-control\" name=\"total\" value=\"{$result['selling_price']}\">";
          $html  .= "</td>";
          $html  .= "<td>";
          $html  .= "<input type=\"date\" class=\"form-control datePicker\" name=\"date\" data-date data-date-format=\"yyyy-mm-dd\">";
          $html  .= "</td>";
          $html  .= "<td>";
          $html  .= "<button type=\"submit\" name=\"add_sale\" class=\"btn btn-primary\">Add sale</button>";
          $html  .= "</td>";
          $html  .= "</tr>";

        }
    } else {
        $html ='<tr><td>product name not resgister in database</td></tr>';
    }

    echo json_encode($html);
  }
 ?>

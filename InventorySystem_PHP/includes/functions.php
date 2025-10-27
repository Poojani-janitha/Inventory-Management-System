<?php
 $errors = array();

 /*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors = $field ." can't be blank.";
      return $errors;
    }
  }
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg =''){
   $output = array();
   if(!empty($msg)) {
      foreach ($msg as $key => $value) {
         $output  = "<div class=\"alert alert-{$key}\">";
         $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
         $output .= remove_junk(first_character($value));
         $output .= "</div>";
      }
      return $output;
   } else {
     return "" ;
   }
}
/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
}
/*--------------------------------------------------------------*/
/* Function for find out total saleing price, buying price and profit
/*--------------------------------------------------------------*/
function total_price($totals){
   $sum = 0;
   $sub = 0;
   foreach($totals as $total ){
     $sum += $total['total_saleing_price'];
     $sub += $total['total_buying_price'];
     $profit = $sum - $sub;
   }
   return array($sum,$profit);
}
/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
     if($str)
      return date('F j, Y, g:i:s a', strtotime($str));
     else
      return null;
  }
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return strftime("%Y-%m-%d %H:%M:%S", time());
}
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}
/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha))];
  return $str;
}

/*--------------------------------------------------------------*/
/* Return Management Functions
/*--------------------------------------------------------------*/

// Find all returns
function find_all_returns() {
  global $db;
  $sql = "SELECT rd.*, p.quantity as current_stock, p.category_name, p.expire_date, 
                 s.s_name as supplier_name, s.email as supplier_email, s.contact_number, s.address as supplier_address
          FROM return_details rd 
          LEFT JOIN product p ON rd.p_id = p.p_id 
          LEFT JOIN supplier_info s ON rd.s_id = s.s_id
          ORDER BY rd.return_date DESC";
  return $db->while_loop($db->query($sql));
}

// Find active return alerts
function find_active_return_alerts() {
  global $db;
  $sql = "SELECT ra.*, p.name as product_name 
          FROM return_alerts ra 
          LEFT JOIN products p ON ra.product_id = p.id 
          WHERE ra.is_resolved = 0 
          ORDER BY ra.alert_date DESC";
  return $db->while_loop($db->query($sql));
}

// Check for frequent returns (3+ times for same product)
function check_frequent_returns($product_id) {
  global $db;
  $sql = "SELECT COUNT(*) as return_count FROM returns WHERE product_id = '{$product_id}'";
  $result = $db->query($sql);
  $count = $db->fetch_assoc($result)['return_count'];
  
  if($count >= 3) {
    $product = find_by_id('products', $product_id);
    $alert_message = "Product '{$product['name']}' has been returned {$count} times. Consider quality review.";
    
    $sql = "INSERT INTO return_alerts (product_id, alert_type, alert_message, alert_date) 
            VALUES ('{$product_id}', 'Frequent Returns', '{$alert_message}', NOW())";
    $db->query($sql);
  }
}

// Get reason label class for styling
function get_reason_label_class($reason) {
  $classes = [
    'Expired' => 'danger',
    'Damaged' => 'warning',
    'Customer Mistake' => 'info',
    'Defective' => 'danger',
    'Wrong Item' => 'info',
    'Quality Issue' => 'warning',
    'Recall' => 'danger',
    'Other' => 'default'
  ];
  return isset($classes[$reason]) ? $classes[$reason] : 'default';
}

// Get status label class for styling
function get_status_label_class($status) {
  $classes = [
    'Pending' => 'warning',
    'Approved' => 'success',
    'Rejected' => 'danger',
    'Processed' => 'info'
  ];
  return isset($classes[$status]) ? $classes[$status] : 'default';
}

// Check for expired products and create alerts
function check_expired_products() {
  global $db;
  $sql = "SELECT * FROM products WHERE expiry_date IS NOT NULL AND expiry_date <= CURDATE()";
  $expired_products = $db->while_loop($db->query($sql));
  
  foreach($expired_products as $product) {
    $alert_message = "Product '{$product['name']}' has expired on {$product['expiry_date']}. Consider supplier return.";
    
    $sql = "INSERT INTO return_alerts (product_id, alert_type, alert_message, alert_date) 
            VALUES ('{$product['id']}', 'Expiry Alert', '{$alert_message}', NOW())
            ON DUPLICATE KEY UPDATE alert_date = NOW()";
    $db->query($sql);
  }
}

// Get return statistics
function get_return_statistics() {
  global $db;
  $stats = [];
  
  // Total returns this month
  $sql = "SELECT COUNT(*) as count FROM returns WHERE MONTH(return_date) = MONTH(CURDATE()) AND YEAR(return_date) = YEAR(CURDATE())";
  $result = $db->query($sql);
  $stats['monthly_returns'] = $db->fetch_assoc($result)['count'];
  
  // Most returned products
  $sql = "SELECT p.name, COUNT(r.id) as return_count 
          FROM returns r 
          LEFT JOIN products p ON r.product_id = p.id 
          GROUP BY r.product_id 
          ORDER BY return_count DESC 
          LIMIT 5";
  $stats['most_returned'] = $db->while_loop($db->query($sql));
  
  // Return reasons breakdown
  $sql = "SELECT return_reason, COUNT(*) as count FROM returns GROUP BY return_reason";
  $stats['reason_breakdown'] = $db->while_loop($db->query($sql));
  
  return $stats;
}

// Get supplier email for product
function get_supplier_email($product_id) {
  global $db;
  $sql = "SELECT s.email FROM products p 
          LEFT JOIN suppliers s ON p.supplier_id = s.id 
          WHERE p.id = '{$product_id}'";
  $result = $db->query($sql);
  $supplier = $db->fetch_assoc($result);
  return $supplier ? $supplier['email'] : null;
}

// Process return (approve/reject)
function process_return($return_id, $action, $notes = '') {
  global $db;
  $return = find_by_id('returns', $return_id);
  
  if(!$return) return false;
  
  $status = ($action == 'approve') ? 'Approved' : 'Rejected';
  $sql = "UPDATE returns SET status = '{$status}', notes = CONCAT(IFNULL(notes, ''), ' {$notes}') WHERE id = '{$return_id}'";
  
  if($db->query($sql)) {
    // If approved, mark as processed
    if($action == 'approve') {
      $sql = "UPDATE returns SET status = 'Processed' WHERE id = '{$return_id}'";
      $db->query($sql);
    }
    return true;
  }
  return false;
}

?>

<?php
  //require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
// Sanitize incoming table names to avoid trailing slashes or invalid chars
function _normalize_table($table){
  // allow letters, numbers and underscore only
  return preg_replace('/[^a-zA-Z0-9_]/','', (string)$table);
}

function find_all($table) {
   $table = _normalize_table($table);
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
 return $result_set;
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table, $id, $primary_key = null) {
  $table = _normalize_table($table);
  global $db;

  // Determine primary key name when not provided
  if ($primary_key === null) {
    switch ($table) {
      case 'product':
        $primary_key = 'p_id';
        break;
      case 'return_details':
        $primary_key = 'return_id';
        break;
      default:
        $primary_key = 'id';
    }
  }

  // Normalize id: if numeric cast to int, otherwise escape as string
  if (is_numeric($id)) {
    $id_val = (int)$id;
  } else {
    $id_val = $db->escape($id);
  }

  if (tableExists($table)) {
    $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE {$primary_key}='{$db->escape($id_val)}' LIMIT 1");
    if ($sql && $db->num_rows($sql) > 0) {
      return $db->fetch_assoc($sql);
    }
  }

  return null;
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id)
{
  $table = _normalize_table($table);
  global $db;
  
  // Handle different primary key names for different tables
  $primary_key = 'id';
  if($table === 'product') {
    $primary_key = 'p_id';
    $id = $db->escape($id); // Don't cast to int for p_id
  } elseif($table === 'return_details') {
    $primary_key = 'return_id';
    $id = (int)$id;
  } else {
    $id = (int)$id;
  }
  
  if(tableExists($table))
   {
    $sql = "DELETE FROM ".$db->escape($table);
    $sql .= " WHERE {$primary_key}=". $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
   }
}
/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/

function count_by_id($table){
  $table = _normalize_table($table);
  global $db;
  
  // Handle different primary key names for different tables
  $primary_key = 'id';
  if($table === 'product') {
    $primary_key = 'p_id';
  }
  
  if(tableExists($table))
  {
    // Use COUNT(*) to avoid depending on a specific primary key name
    $sql    = "SELECT COUNT(*) AS total FROM ". $db->escape($table);
    $result = $db->query($sql);
    $row = $db->fetch_assoc($result);
    // Ensure we always return an array with a 'total' key to avoid warnings when
    // calling code attempts to access ['total']
    if ($row && isset($row['total'])) {
        return $row;
    }
    return array('total' => 0);
  }
  // If table doesn't exist or something went wrong, return zero safely
  return array('total' => 0);
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
  $table = _normalize_table($table);
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
  function authenticate($username='', $password='') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if($db->num_rows($result)){
      $user = $db->fetch_assoc($result);
      $password_request = sha1($password);
      if($password_request === $user['password'] ){
        return $user['id'];
      }
    }
   return false;
  }
  /*--------------------------------------------------------------*/
  /* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
   function authenticate_v2($username='', $password='') {
     global $db;
     $username = $db->escape($username);
     $password = $db->escape($password);
     $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
     $result = $db->query($sql);
     if($db->num_rows($result)){
       $user = $db->fetch_assoc($result);
       $password_request = sha1($password);
       if($password_request === $user['password'] ){
         return $user;
       }
     }
    return false;
   }


  /*--------------------------------------------------------------*/
  /* Find current log in user by session id
  /*--------------------------------------------------------------*/
  function current_user(){
      static $current_user;
      global $db;
      if(!$current_user){
         if(isset($_SESSION['user_id'])):
             $user_id = intval($_SESSION['user_id']);
             $current_user = find_by_id('users',$user_id);
        endif;
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
  function find_all_user(){
      global $db;
      $results = array();
      $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
      $sql .="g.group_name ";
      $sql .="FROM users u ";
      $sql .="LEFT JOIN user_groups g ";
      $sql .="ON g.group_level=u.user_level ORDER BY u.name ASC";
      $result = find_by_sql($sql);
      return $result;
  }
  /*--------------------------------------------------------------*/
  /* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

 function updateLastLogIn($user_id)
	{
		global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
	}

  /*--------------------------------------------------------------*/
  /* Find all Group name
  /*--------------------------------------------------------------*/
  function find_by_groupName($val)
  {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Find group level
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT group_level, group_status FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    if($db->num_rows($result) > 0) {
      return $db->fetch_assoc($result);
    }
    return false;
  }
  /*--------------------------------------------------------------*/
  /* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     $login_level = find_by_groupLevel($current_user['user_level']);
     //if user not login
  if (!$session->isUserLoggedIn()):
            $session->msg('d','Please login...');
            redirect('index.php', false);
      //if Group status Deactive
     elseif($login_level && $login_level['group_status'] == 0):
           $session->msg('d','This level user has been band!');
           redirect('home.php',false);
      //cheackin log in User level and Require level is Less than or equal to
     elseif($current_user['user_level'] <= (int)$require_level):
              return true;
      else:
            $session->msg("d", "Sorry! you dont have permission to view the page.");
            redirect('home.php', false);
        endif;

     }
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name */

 function join_product_table(){
    global $db;
    $sql  =" SELECT p.p_id AS id,p.product_name AS name,p.quantity,p.buying_price AS buy_price,p.selling_price AS sale_price,p.recorded_date AS date,c.category_name";
  //  $sql  .=" AS categorie,m.file_name AS image";
   $sql  .=" FROM product p";
   $sql  .=" LEFT JOIN categories c ON c.category_name = p.category_name";
  //  $sql  .=" LEFT JOIN media m ON m.id = p.media_id";
   $sql  .=" ORDER BY p.p_id ASC";
   return find_by_sql($sql);

  }
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

   function find_product_by_title($product_name){
     global $db;
     $p_name = remove_junk($db->escape($product_name));
     $sql = "SELECT product_name AS name FROM product WHERE product_name like '%$p_name%' LIMIT 5";
     $result = find_by_sql($sql);
     return $result;
   }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM product ";
    $sql .= " WHERE product_name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
  }

  /*--------------------------------------------------------------*/
  /* Function for Update product quantity
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = $db->escape($p_id);
    $sql = "UPDATE product SET quantity=quantity -'{$qty}' WHERE p_id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
 function find_recent_product_added($limit){
   global $db;
   $sql   = " SELECT p.p_id AS id,p.product_name AS name,p.selling_price AS sale_price,c.category_name AS categorie,";
  //  $sql  .= "m.file_name AS image FROM product p";
   $sql  .= " LEFT JOIN categories c ON c.category_name = p.category_name";
  //  $sql  .= " LEFT JOIN media m ON m.id = p.media_id";
   $sql  .= " ORDER BY p.p_id DESC LIMIT ".$db->escape((int)$limit);
   return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
 /* Function for Find Highest saleing Product
 /*--------------------------------------------------------------*/
 function find_higest_saleing_product($limit){
   global $db;
   $sql  = "SELECT p.product_name AS name, COUNT(s.p_id) AS totalSold, SUM(s.qty) AS totalQty";
   $sql .= " FROM sales s";
   $sql .= " LEFT JOIN product p ON p.p_id = s.p_id ";
   $sql .= " GROUP BY s.p_id";
   $sql .= " ORDER BY SUM(s.qty) DESC LIMIT ".$db->escape((int)$limit);
   return $db->query($sql);
 }
 /*--------------------------------------------------------------*/
 /* Function for find all sales
 /*--------------------------------------------------------------*/
 function find_all_sale(){
   global $db;
   $sql  = "SELECT s.id,s.qty,s.price,s.date,p.product_name AS name";
   $sql .= " FROM sales s";
   $sql .= " LEFT JOIN product p ON s.p_id = p.p_id";
   $sql .= " ORDER BY s.date DESC";
   return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
 /* Function for Display Recent sale
 /*--------------------------------------------------------------*/
function find_recent_sale_added($limit){
  global $db;
  $sql  = "SELECT s.id,s.qty,s.price,s.date,p.product_name AS name";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN product p ON s.p_id = p.p_id";
  $sql .= " ORDER BY s.date DESC LIMIT ".$db->escape((int)$limit);
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date,$end_date){
  global $db;
  $start_date  = date("Y-m-d", strtotime($start_date));
  $end_date    = date("Y-m-d", strtotime($end_date));
  $sql  = "SELECT s.date, p.product_name AS name,p.selling_price AS sale_price,p.buying_price AS buy_price,";
  $sql .= "COUNT(s.p_id) AS total_records,";
  $sql .= "SUM(s.qty) AS total_sales,";
  $sql .= "SUM(p.selling_price * s.qty) AS total_saleing_price,";
  $sql .= "SUM(p.buying_price * s.qty) AS total_buying_price ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN product p ON s.p_id = p.p_id";
  $sql .= " WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
  $sql .= " GROUP BY DATE(s.date),p.product_name";
  $sql .= " ORDER BY DATE(s.date) DESC";
  return $db->query($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function  dailySales($year,$month){
  global $db;
  $sql  = "SELECT s.qty,";
  $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.product_name AS name,";
  $sql .= "SUM(p.selling_price * s.qty) AS total_saleing_price";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN product p ON s.p_id = p.p_id";
  $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
  $sql .= " GROUP BY DATE_FORMAT( s.date,  '%e' ),s.p_id";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate Monthly sales report
/*--------------------------------------------------------------*/
function  monthlySales($year){
  global $db;
  $sql  = "SELECT s.qty,";
  $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.product_name AS name,";
  $sql .= "SUM(p.selling_price * s.qty) AS total_saleing_price";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN product p ON s.p_id = p.p_id";
  $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
  $sql .= " GROUP BY DATE_FORMAT( s.date,  '%c' ),s.p_id";
  $sql .= " ORDER BY date_format(s.date, '%c' ) ASC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding all returns with complete details
/* Joins return_details, product, categories, and supplier_info tables
/*--------------------------------------------------------------*/
function find_all_returns(){
  global $db;
  $sql  = "SELECT ";
  $sql .= "r.return_id, ";
  $sql .= "r.p_id, ";
  $sql .= "r.s_id, ";
  $sql .= "r.product_name, ";
  $sql .= "r.buying_price, ";
  $sql .= "r.return_quantity, ";
  $sql .= "r.return_date, ";
  $sql .= "p.quantity AS current_stock, ";
  $sql .= "p.selling_price, ";
  $sql .= "p.category_name, ";
  $sql .= "c.category_name AS category_name, ";
  $sql .= "s.s_name AS supplier_name, ";
  $sql .= "s.contact_number, ";
  $sql .= "s.email AS supplier_email, ";
  $sql .= "s.address AS supplier_address ";
  $sql .= "FROM return_details r ";
  $sql .= "LEFT JOIN product p ON r.p_id = p.p_id ";
  $sql .= "LEFT JOIN categories c ON p.category_name = c.category_name ";
  $sql .= "LEFT JOIN supplier_info s ON r.s_id = s.s_id ";
  $sql .= "ORDER BY r.return_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding return by ID with complete details
/*--------------------------------------------------------------*/
function find_return_by_id($return_id){
  global $db;
  $id = (int)$return_id;
  $sql  = "SELECT ";
  $sql .= "r.return_id, ";
  $sql .= "r.p_id, ";
  $sql .= "r.s_id, ";
  $sql .= "r.product_name, ";
  $sql .= "r.buying_price, ";
  $sql .= "r.return_quantity, ";
  $sql .= "r.return_date, ";
  $sql .= "p.quantity AS current_stock, ";
  $sql .= "p.selling_price, ";
  $sql .= "p.category_name, ";
  $sql .= "c.category_name AS category_name, ";
  $sql .= "s.s_name AS supplier_name, ";
  $sql .= "s.contact_number, ";
  $sql .= "s.email AS supplier_email, ";
  $sql .= "s.address AS supplier_address ";
  $sql .= "FROM return_details r ";
  $sql .= "LEFT JOIN product p ON r.p_id = p.p_id ";
  $sql .= "LEFT JOIN categories c ON p.category_name = c.category_name ";
  $sql .= "LEFT JOIN supplier_info s ON r.s_id = s.s_id ";
  $sql .= "WHERE r.return_id = '{$id}' ";
  $sql .= "LIMIT 1";
  $result = find_by_sql($sql);
  return !empty($result) ? $result[0] : null;
}

/*--------------------------------------------------------------*/
/* Function for Finding returns by date range
/*--------------------------------------------------------------*/
function find_returns_by_date_range($start_date, $end_date){
  global $db;
  $start = $db->escape($start_date);
  $end = $db->escape($end_date);
  $sql  = "SELECT ";
  $sql .= "r.return_id, ";
  $sql .= "r.p_id, ";
  $sql .= "r.s_id, ";
  $sql .= "r.product_name, ";
  $sql .= "r.buying_price, ";
  $sql .= "r.return_quantity, ";
  $sql .= "r.return_date, ";
  $sql .= "p.quantity AS current_stock, ";
  $sql .= "p.selling_price, ";
  $sql .= "p.category_name, ";
  $sql .= "c.category_name AS category_name, ";
  $sql .= "s.s_name AS supplier_name, ";
  $sql .= "s.contact_number, ";
  $sql .= "s.email AS supplier_email ";
  $sql .= "FROM return_details r ";
  $sql .= "LEFT JOIN product p ON r.p_id = p.p_id ";
  $sql .= "LEFT JOIN categories c ON p.category_name = c.category_name ";
  $sql .= "LEFT JOIN supplier_info s ON r.s_id = s.s_id ";
  $sql .= "WHERE DATE(r.return_date) BETWEEN '{$start}' AND '{$end}' ";
  $sql .= "ORDER BY r.return_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Calculating total return amount
/*--------------------------------------------------------------*/
function calculate_total_returns(){
  global $db;
  $sql  = "SELECT ";
  $sql .= "COUNT(*) AS total_returns, ";
  $sql .= "SUM(return_quantity * buying_price) AS total_amount ";
  $sql .= "FROM return_details";
  $result = $db->query($sql);
  return $db->fetch_assoc($result);
}





// function for report generating@@@@@@@@@@@




/*--------------------------------------------------------------*/
/* Function for Get all purchase orders for report
/*--------------------------------------------------------------*/
function find_all_purchase_orders($start_date = null, $end_date = null){
  global $db;
  $sql = "SELECT po.o_id, po.s_id, si.s_name AS supplier_name, po.product_name, po.category_name, ";
  $sql .= "po.quantity, po.price, (po.quantity * po.price) AS total_amount, ";
  $sql .= "po.order_date, po.status ";
  $sql .= "FROM purchase_order po ";
  $sql .= "LEFT JOIN supplier_info si ON po.s_id = si.s_id ";
  
  if($start_date && $end_date){
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));
    $sql .= "WHERE DATE(po.order_date) BETWEEN '{$start_date}' AND '{$end_date}' ";
  }
  
  $sql .= "ORDER BY po.order_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Get all sales for report
/*--------------------------------------------------------------*/
function find_all_sales_report($start_date = null, $end_date = null){
  global $db;
  $sql = "SELECT s.sales_id, s.sale_product_id, s.invoice_number, p.product_name, ";
  $sql .= "c.category_name, s.quantity, s.sale_selling_price, s.total, s.discount, ";
  $sql .= "s.name AS customer_name, s.pNumber, s.created_at ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN product p ON s.sale_product_id = p.p_id ";
  $sql .= "LEFT JOIN categories c ON s.category_name = c.category_name ";
  
  if($start_date && $end_date){
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));
    $sql .= "WHERE DATE(s.created_at) BETWEEN '{$start_date}' AND '{$end_date}' ";
  }
  
  $sql .= "ORDER BY s.created_at DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Get all return details for report
/*--------------------------------------------------------------*/
function find_all_return_details($start_date = null, $end_date = null){
  global $db;
  $sql = "SELECT rd.return_id, rd.p_id, rd.s_id, si.s_name AS supplier_name, ";
  $sql .= "rd.product_name, rd.buying_price, rd.return_quantity, ";
  $sql .= "(rd.buying_price * rd.return_quantity) AS total_return_amount, ";
  $sql .= "rd.return_date ";
  $sql .= "FROM return_details rd ";
  $sql .= "LEFT JOIN supplier_info si ON rd.s_id = si.s_id ";
  
  if($start_date && $end_date){
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = date("Y-m-d", strtotime($end_date));
    $sql .= "WHERE DATE(rd.return_date) BETWEEN '{$start_date}' AND '{$end_date}' ";
  }
  
  $sql .= "ORDER BY rd.return_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Get stock summary report
/*--------------------------------------------------------------*/
function find_stock_summary(){
  global $db;
  $sql = "SELECT p.p_id, p.product_name, p.quantity, p.buying_price, p.selling_price, ";
  $sql .= "c.category_name, si.s_name AS supplier_name, p.expire_date, p.recorded_date ";
  $sql .= "FROM product p ";
  $sql .= "LEFT JOIN categories c ON p.category_name = c.category_name ";
  $sql .= "LEFT JOIN supplier_info si ON p.s_id = si.s_id ";
  $sql .= "ORDER BY p.product_name ASC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Get inventory valuation report
/*--------------------------------------------------------------*/
function find_inventory_valuation(){
  global $db;
  $sql = "SELECT p.p_id, p.product_name, p.quantity, p.buying_price, p.selling_price, ";
  $sql .= "(p.quantity * p.buying_price) AS stock_value, ";
  $sql .= "(p.quantity * p.selling_price) AS potential_sales_value, ";
  $sql .= "((p.quantity * p.selling_price) - (p.quantity * p.buying_price)) AS potential_profit, ";
  $sql .= "c.category_name, si.s_name AS supplier_name, p.expire_date ";
  $sql .= "FROM product p ";
  $sql .= "LEFT JOIN categories c ON p.category_name = c.category_name ";
  $sql .= "LEFT JOIN supplier_info si ON p.s_id = si.s_id ";
  $sql .= "ORDER BY stock_value DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Get profit report from product and sales tables
/*--------------------------------------------------------------*/
function find_profit_report(){
  global $db;
  
  // Get total sales revenue and cost from sales table joined with product table
  $sales_sql = "SELECT ";
  $sales_sql .= "COALESCE(SUM(s.total), 0) AS total_sales_revenue, ";
  $sales_sql .= "COALESCE(SUM(s.discount), 0) AS total_discounts, ";
  $sales_sql .= "COALESCE(SUM(p.buying_price * s.quantity), 0) AS total_cost_of_sales, ";
  $sales_sql .= "COALESCE(SUM(p.selling_price * s.quantity), 0) AS total_potential_revenue ";
  $sales_sql .= "FROM sales s ";
  $sales_sql .= "LEFT JOIN product p ON s.sale_product_id = p.p_id ";
  
  $sales_result = find_by_sql($sales_sql);
  
  $sales_data = isset($sales_result[0]) ? $sales_result[0] : array(
    'total_sales_revenue' => 0, 
    'total_discounts' => 0, 
    'total_cost_of_sales' => 0,
    'total_potential_revenue' => 0
  );
  
  // Calculate gross profit (Revenue - Cost - Discounts)
  $gross_profit = $sales_data['total_sales_revenue'] - $sales_data['total_cost_of_sales'] - $sales_data['total_discounts'];
  
  // Calculate profit margin percentage
  $profit_margin = 0;
  if($sales_data['total_sales_revenue'] > 0){
    $profit_margin = ($gross_profit / $sales_data['total_sales_revenue']) * 100;
  }
  
  return array(
    'sales_revenue' => $sales_data['total_sales_revenue'],
    'cost_of_sales' => $sales_data['total_cost_of_sales'],
    'discounts' => $sales_data['total_discounts'],
    'gross_profit' => $gross_profit,
    'profit_margin' => $profit_margin,
    'potential_revenue' => $sales_data['total_potential_revenue']
  );
}

/*--------------------------------------------------------------*/
/* Function for Get profit report for a given year and month    */
/*--------------------------------------------------------------*/
function find_profit_report_month($year, $month){
  global $db;
  $year = (int)$year;
  $month = sprintf('%02d', (int)$month);

  // Restrict to selected month using created_at on sales
  $sales_sql = "SELECT ";
  $sales_sql .= "COALESCE(SUM(s.total), 0) AS total_sales_revenue, ";
  $sales_sql .= "COALESCE(SUM(s.discount), 0) AS total_discounts, ";
  $sales_sql .= "COALESCE(SUM(p.buying_price * s.quantity), 0) AS total_cost_of_sales, ";
  $sales_sql .= "COALESCE(SUM(p.selling_price * s.quantity), 0) AS total_potential_revenue ";
  $sales_sql .= "FROM sales s ";
  $sales_sql .= "LEFT JOIN product p ON s.sale_product_id = p.p_id ";
  $sales_sql .= "WHERE DATE_FORMAT(s.created_at, '%Y-%m') = '{$year}-{$month}'";

  $sales_result = find_by_sql($sales_sql);
  $sales_data = isset($sales_result[0]) ? $sales_result[0] : array(
    'total_sales_revenue' => 0,
    'total_discounts' => 0,
    'total_cost_of_sales' => 0,
    'total_potential_revenue' => 0
  );

  $gross_profit = $sales_data['total_sales_revenue'] - $sales_data['total_cost_of_sales'] - $sales_data['total_discounts'];
  $profit_margin = 0;
  if($sales_data['total_sales_revenue'] > 0){
    $profit_margin = ($gross_profit / $sales_data['total_sales_revenue']) * 100;
  }

  return array(
    'sales_revenue' => $sales_data['total_sales_revenue'],
    'cost_of_sales' => $sales_data['total_cost_of_sales'],
    'discounts' => $sales_data['total_discounts'],
    'gross_profit' => $gross_profit,
    'profit_margin' => $profit_margin,
    'potential_revenue' => $sales_data['total_potential_revenue']
  );
}



?>

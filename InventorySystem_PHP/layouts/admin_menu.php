<?php 
  // Detect if we're in the reports folder and adjust base path
  $current_path = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
  if(empty($current_path) && isset($_SERVER['REQUEST_URI'])) {
    $current_path = $_SERVER['REQUEST_URI'];
  }
  $is_in_reports = (strpos($current_path, '/reports/') !== false);
  $base_path = $is_in_reports ? '../' : '';
?>
<ul>
  <li>
    <a href="<?php echo $base_path; ?>admin.php">
      <i class="glyphicon glyphicon-home"></i>
      <span>Dashboard</span>
    </a>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-user"></i>
      <span>User Management</span>
    </a>
    <ul class="nav submenu">
      <li><a href="<?php echo $base_path; ?>group.php">Manage Groups</a> </li>
      <li><a href="<?php echo $base_path; ?>users.php">Manage Users</a> </li>
   </ul>
  </li>
  <li>
    <a href="<?php echo $base_path; ?>categorie.php" >
      <i class="glyphicon glyphicon-indent-left"></i>
      <span>Categories</span>
    </a>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-folder-open"></i>
      <span>Supplier</span>
    </a>
    <ul class="nav submenu">
      <li><a href="<?php echo $base_path; ?>add_supplier.php">Add Supplier</a></li>
      <li><a href="<?php echo $base_path; ?>add_supplier_product.php">Add Supplier Product</a></li>
    </ul>
  </li>
  <li>
    <a href="<?php echo $base_path; ?>order.php" >
      <i class="glyphicon glyphicon-shopping-cart"></i>
      <span>Order</span>
    </a>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-th-large"></i>
      <span>Products</span>
    </a>
    <ul class="nav submenu">
       <li><a href="<?php echo $base_path; ?>Purchase_orders_Accepted.php">Purchase orders</a> </li>
       <li><a href="<?php echo $base_path; ?>product.php">Manage Products</a> </li>
       <li><a href="<?php echo $base_path; ?>add_product.php">Add Products</a> </li>
   </ul>
  </li>
  
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-credit-card"></i>
       <span>Sales</span>
      </a>
      <ul class="nav submenu">
         <li><a href="<?php echo $base_path; ?>invoice_list.php">Manage Sales</a> </li>
         <li><a href="<?php echo $base_path; ?>add_sales.php">Add Sale</a> </li>
     </ul>
  </li>
  
  <li>
    <a href="<?php echo $base_path; ?>reports/index.php" class="submenu-toggle">
      <i class="glyphicon glyphicon-file"></i>
      <span>Reports</span>
    </a>
    <ul class="nav submenu">
      <li><a href="<?php echo $is_in_reports ? 'index.php' : $base_path . 'reports/index.php'; ?>">Reports Dashboard</a></li>
      <li><a href="<?php echo $is_in_reports ? 'purchase_report.php' : $base_path . 'reports/purchase_report.php'; ?>">Purchase Report</a></li>
      <li><a href="<?php echo $is_in_reports ? 'sales_report.php' : $base_path . 'reports/sales_report.php'; ?>">Sales Report</a></li>
      <li><a href="<?php echo $is_in_reports ? 'return_report.php' : $base_path . 'reports/return_report.php'; ?>">Return Report</a></li>
      <li><a href="<?php echo $is_in_reports ? 'stock_summary.php' : $base_path . 'reports/stock_summary.php'; ?>">Stock Summary</a></li>
      <li><a href="<?php echo $is_in_reports ? 'inventory_valuation.php' : $base_path . 'reports/inventory_valuation.php'; ?>">Inventory Valuation</a></li>
      <li><a href="<?php echo $is_in_reports ? 'profit_report.php' : $base_path . 'reports/profit_report.php'; ?>">Profit Report</a></li>
    </ul>
  </li>
  <li>
    <a href="<?php echo $base_path; ?>database_backup.php">
      <i class="glyphicon glyphicon-download-alt"></i>
      <span>Database Backup</span>
    </a>
  </li>
</ul>

<?php
// role_access.php
// Returns JSON list of accessible menu entries for a given user level
require_once('includes/load.php');

$level = isset($_REQUEST['level']) ? (string)$_REQUEST['level'] : '';

$access = [];

// Define mapping of user_level to accessible pages (label => url)
// Keep this in sync with layouts/admin_menu.php, special_menu.php and user_menu.php
switch ($level) {
    case '1': // Admin
        $access = [
            ['label' => 'Dashboard', 'url' => 'admin.php'],
            ['label' => 'User Management', 'url' => 'users.php'],
            ['label' => 'Categories', 'url' => 'categorie.php'],
            ['label' => 'Supplier - Add Supplier', 'url' => 'add_supplier.php'],
            ['label' => 'Supplier - Add Supplier Product', 'url' => 'add_supplier_product.php'],
            ['label' => 'Order', 'url' => 'order.php'],
            ['label' => 'Products - Manage', 'url' => 'product.php'],
            ['label' => 'Products - Add', 'url' => 'add_product.php'],
            ['label' => 'Sales - Manage', 'url' => 'invoice_list.php'],
            ['label' => 'Sales - Add', 'url' => 'add_sales.php'],
            ['label' => 'Sales Report - Monthly', 'url' => 'monthly_sales.php'],
            ['label' => 'Sales Report - Daily', 'url' => 'daily_sales.php'],
            ['label' => 'Database Backup', 'url' => 'database_backup.php'],
            ['label' => 'Return Management - Manage', 'url' => 'returns.php'],
            ['label' => 'Return Management - Add', 'url' => 'add_return.php'],
            ['label' => 'Return Management - Expiry Checker', 'url' => 'auto_expiry_checker.php']
        ];
        break;
    case '2': // Special
        $access = [
            ['label' => 'Dashboard', 'url' => 'home.php'],
            ['label' => 'Categories', 'url' => 'categorie.php'],
            ['label' => 'Products - Manage', 'url' => 'product.php'],
            ['label' => 'Products - Add', 'url' => 'add_product.php'],
            ['label' => 'Media', 'url' => 'media.php']
        ];
        break;
    case '3': // User
        $access = [
            ['label' => 'Dashboard', 'url' => 'home.php'],
            ['label' => 'Sales - Manage', 'url' => 'sales.php'],
            ['label' => 'Sales - Add', 'url' => 'add_sale.php'],
            ['label' => 'Sales Report - By Date', 'url' => 'sales_report.php'],
            ['label' => 'Sales Report - Monthly', 'url' => 'monthly_sales.php'],
            ['label' => 'Sales Report - Daily', 'url' => 'daily_sales.php']
        ];
        break;
    default:
        $access = [];
}

header('Content-Type: application/json');
echo json_encode(['level' => $level, 'access' => $access]);
exit;

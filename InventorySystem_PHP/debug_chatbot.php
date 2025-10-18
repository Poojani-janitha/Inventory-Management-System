<?php
// Debug version to test what's happening
header('Content-Type: application/json');

$debug_info = [];

// Test if we can include the files using the same path structure as admin.php
try {
    require_once('includes/load.php');
    $debug_info['step1'] = 'Load.php loaded successfully';
    $debug_info['db_host'] = DB_HOST;
    $debug_info['step2'] = 'All includes loaded successfully';
} catch(Exception $e) {
    $debug_info['load_error'] = $e->getMessage();
    echo json_encode($debug_info);
    exit();
}

try {
    global $db;
    $test_query = $db->query("SELECT COUNT(*) as total FROM products");
    $result = $db->fetch_assoc($test_query);
    $debug_info['step3'] = 'Database query successful';
    $debug_info['product_count'] = $result['total'];
    $debug_info['status'] = 'Everything is working!';
} catch(Exception $e) {
    $debug_info['step3_error'] = $e->getMessage();
}

echo json_encode($debug_info);
?>

<?php
// Simple debug version
header('Content-Type: application/json');

try {
    require_once('includes/load.php');
    
    global $db;
    $test_query = $db->query("SELECT COUNT(*) as total FROM products");
    $result = $db->fetch_assoc($test_query);
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'product_count' => $result['total'],
        'db_host' => DB_HOST
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>

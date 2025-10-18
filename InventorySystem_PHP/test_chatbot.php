<?php
// Test file to check if the chatbot API is working
header('Content-Type: application/json');

// Include existing database configuration using the same path structure as admin.php
require_once('includes/load.php');

try {
    // Use existing database connection
    global $db;
    
    // Test a simple query
    $products = $db->query("SELECT COUNT(*) as total FROM products");
    $result = $db->fetch_assoc($products);
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'product_count' => $result['total'],
        'database_host' => DB_HOST,
        'database_name' => DB_NAME,
        'status' => 'Chatbot API is ready to use!'
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage(),
        'database_host' => DB_HOST,
        'database_name' => DB_NAME,
        'suggestion' => 'Please check your database configuration in includes/config.php'
    ]);
}
?>

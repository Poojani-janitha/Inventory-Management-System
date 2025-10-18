<?php
// Minimal chatbot test - no includes
header('Content-Type: application/json');

// Test basic functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
    
    echo json_encode([
        'success' => true,
        'response' => 'Hello! This is a test response. Your message was: ' . $message,
        'data' => []
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Minimal chatbot is working! Send a POST request with a message.',
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}
?>

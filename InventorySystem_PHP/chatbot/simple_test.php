<?php
// Very simple test
header('Content-Type: application/json');

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
        'response' => 'Simple test is working! Send a POST request.',
        'data' => []
    ]);
}
?>

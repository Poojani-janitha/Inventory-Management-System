<?php
require_once('includes/load.php');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method');
}

// Get username from POST data
$username = isset($_POST['username']) ? $_POST['username'] : '';

if (empty($username)) {
    echo json_encode(['exists' => false]);
    exit;
}

$username = remove_junk($db->escape($username));

// Check if username exists in database
$sql = "SELECT COUNT(*) as count FROM users WHERE username = '{$username}'";
$result = $db->query($sql);
$row = $db->fetch_assoc($result);

echo json_encode(['exists' => $row['count'] > 0]);
?>

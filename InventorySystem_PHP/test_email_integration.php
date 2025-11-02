<?php
/**
 * Test Email Integration
 * Tests if PHPMailer is properly configured
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Email Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; color: #0c5460; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>";

echo "<h1>PHPMailer Email Test</h1>";
echo "<hr>";

// Test 1: Check if PHPMailer files exist
echo "<h2>1. PHPMailer Files Check</h2>";
$files_to_check = [
    'PHPMailer/src/Exception.php',
    'PHPMailer/src/PHPMailer.php',
    'PHPMailer/src/SMTP.php'
];

$all_files_exist = true;
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<div class='success'>✓ Found: {$file}</div>";
    } else {
        echo "<div class='error'>✗ Missing: {$file}</div>";
        $all_files_exist = false;
    }
}

if (!$all_files_exist) {
    echo "<div class='error'><strong>Error:</strong> PHPMailer files are missing. Please download and install PHPMailer.</div>";
    echo "</body></html>";
    exit;
}

// Test 2: Send test email
echo "<h2>2. Sending Test Email</h2>";
echo "<div class='info'>Attempting to send email to: chamikaliyanage2002322@gmail.com</div>";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'chamikaliyanage2002322@gmail.com';
    $mail->Password   = 'vfwg uuhy dzxs ixfo';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Recipients
    $mail->setFrom('chamikaliyanage2002322@gmail.com', 'Test Sender');
    $mail->addAddress('chamikaliyanage2002322@gmail.com', 'Test Recipient');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test - ' . date('Y-m-d H:i:s');
    $mail->Body    = '
    <html>
    <body style="font-family: Arial, sans-serif;">
        <h2 style="color: #667eea;">Email Test Successful!</h2>
        <p>If you\'re reading this, PHPMailer is working correctly on your system.</p>
        <p><strong>Test Date:</strong> ' . date('F d, Y H:i:s') . '</p>
        <p><strong>Server:</strong> ' . $_SERVER['SERVER_NAME'] . '</p>
        <p><strong>MySQL Port:</strong> 3307 (custom)</p>
        <hr>
        <p style="color: #666; font-size: 12px;">This is an automated test email from your Inventory Management System.</p>
    </body>
    </html>';
    $mail->AltBody = 'PHPMailer test successful! Test date: ' . date('Y-m-d H:i:s');
    
    $mail->send();
    
    echo "<div class='success'>";
    echo "<h3>✓ Email Sent Successfully!</h3>";
    echo "<p><strong>To:</strong> chamikaliyanage2002322@gmail.com</p>";
    echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Please check your inbox (and spam folder).</p>";
    echo "</div>";
    
    echo "<h2>3. Configuration Summary</h2>";
    echo "<div class='info'>";
    echo "<ul>";
    echo "<li><strong>SMTP Server:</strong> smtp.gmail.com</li>";
    echo "<li><strong>Port:</strong> 587 (TLS)</li>";
    echo "<li><strong>Authentication:</strong> Enabled</li>";
    echo "<li><strong>Username:</strong> chamikaliyanage2002322@gmail.com</li>";
    echo "<li><strong>MySQL Port:</strong> 3307 (works independently)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>4. Next Steps</h2>";
    echo "<div class='info'>";
    echo "<ol>";
    echo "<li>If you received the test email, your setup is complete!</li>";
    echo "<li>Go to <strong>add_sales.php</strong> and create a test sale with a valid email</li>";
    echo "<li>The invoice will be automatically emailed to the customer</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>✗ Email Failed</h3>";
    echo "<p><strong>Error:</strong> {$mail->ErrorInfo}</p>";
    echo "</div>";
    
    echo "<h2>3. Troubleshooting</h2>";
    echo "<div class='info'>";
    echo "<h4>Common Issues:</h4>";
    echo "<ul>";
    echo "<li><strong>Authentication failed:</strong> Check if App Password is correct</li>";
    echo "<li><strong>Connection timeout:</strong> Check your internet connection</li>";
    echo "<li><strong>Port blocked:</strong> Check firewall settings for port 587</li>";
    echo "</ul>";
    echo "<p>Check error logs at: <code>C:\\xampp\\php\\logs\\php_error_log</code></p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
echo "</body></html>";
?>
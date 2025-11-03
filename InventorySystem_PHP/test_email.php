<?php
// test_email.php - Upload this to your server and access via browser
// Example: http://yoursite.com/test_email.php

// Change this to YOUR email address for testing
$test_email = "chamikaliyanage2002322@gmail.com"; // <-- CHANGE THIS

echo "<h2>Email Configuration Test</h2>";
echo "<hr>";

// Test 1: Check if mail function exists
echo "<h3>Test 1: PHP mail() function</h3>";
if (function_exists('mail')) {
    echo "✅ mail() function is available<br><br>";
} else {
    echo "❌ mail() function is NOT available<br><br>";
    die("STOP: Your server doesn't support mail() function. Contact your hosting provider.");
}

// Test 2: Check PHP mail configuration
echo "<h3>Test 2: PHP Mail Configuration</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";

$mail_settings = [
    'SMTP' => ini_get('SMTP'),
    'smtp_port' => ini_get('smtp_port'),
    'sendmail_from' => ini_get('sendmail_from'),
    'sendmail_path' => ini_get('sendmail_path')
];

foreach ($mail_settings as $key => $value) {
    $status = empty($value) ? "❌ Not Set" : "✅ " . $value;
    echo "<tr><td><strong>{$key}</strong></td><td>{$status}</td></tr>";
}
echo "</table><br>";

// Test 3: Send a simple test email
echo "<h3>Test 3: Send Test Email</h3>";
$to = $test_email;
$subject = "Test Email from HealStock";
$message = "This is a test email. If you receive this, your email configuration is working!";
$headers = "From: HealStock <chamikaliyanage2002322@gmail.com>\r\n";
$headers .= "Reply-To: chamikaliyanage2002322@gmail.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

echo "Sending test email to: <strong>{$to}</strong><br>";
echo "From: chamikaliyanage2002322@gmail.com<br>";

// Clear previous errors
error_clear_last();

// Try sending
$result = @mail($to, $subject, $message, $headers);

if ($result) {
    echo "<br>✅ <strong style='color: green;'>Email sent successfully!</strong><br>";
    echo "Check your inbox (and spam folder) at: {$to}<br>";
} else {
    echo "<br>❌ <strong style='color: red;'>Email failed to send!</strong><br>";
    
    // Get error details
    $error = error_get_last();
    if ($error) {
        echo "Error: " . $error['message'] . "<br>";
    }
}

// Test 4: Send HTML email (like invoice)
echo "<hr>";
echo "<h3>Test 4: Send HTML Test Email</h3>";

$html_message = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .box { background: #f0f0f0; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Test HTML Email</h2>
        <p>If you can see this formatted email, HTML emails are working!</p>
        <p><strong>From:</strong> HealStock System</p>
    </div>
</body>
</html>';

$html_headers = "MIME-Version: 1.0\r\n";
$html_headers .= "Content-type:text/html;charset=UTF-8\r\n";
$html_headers .= "From: HealStock <chamikaliyanage2002322@gmail.com>\r\n";
$html_headers .= "Reply-To: chamikaliyanage2002322@gmail.com\r\n";

$html_result = @mail($to, "HTML Test Email from HealStock", $html_message, $html_headers);

if ($html_result) {
    echo "✅ <strong style='color: green;'>HTML email sent successfully!</strong><br>";
} else {
    echo "❌ <strong style='color: red;'>HTML email failed!</strong><br>";
}

echo "<hr>";
echo "<h3>Server Information</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "OS: " . PHP_OS . "<br>";

echo "<hr>";
echo "<h3>Recommendations:</h3>";
echo "<ul>";

if (empty(ini_get('SMTP')) && empty(ini_get('sendmail_path'))) {
    echo "<li>⚠️ <strong>No mail server configured!</strong> You need to configure SMTP in php.ini or use a third-party email service.</li>";
}

echo "<li>📧 Check spam/junk folder in your email</li>";
echo "<li>🔧 If emails still don't arrive, contact your hosting provider to enable email sending</li>";
echo "<li>💡 Consider using PHPMailer or SMTP services (Gmail SMTP, SendGrid, Mailgun) for better reliability</li>";
echo "</ul>";

?>
<?php
/**
 * Test Return Email Configuration
 * This script will help you test and debug email sending for returns
 */
require_once('includes/load.php');
page_require_level(1);

$test_result = null;
$test_error = null;
$config_info = [];

// Get configuration from php.ini
$config_info['smtp'] = ini_get('SMTP');
$config_info['smtp_port'] = ini_get('smtp_port');
$config_info['sendmail_from'] = ini_get('sendmail_from');
$config_info['sendmail_path'] = ini_get('sendmail_path');

// Test email if form submitted
if(isset($_POST['test_email'])){
    $test_email = $_POST['test_email'];
    
    if(filter_var($test_email, FILTER_VALIDATE_EMAIL)){
        // Test the send_return_email function
        $test_subject = "Test Email - Return Notification System";
        $test_message = "
        <html>
        <body>
            <h2>Test Email from Return Notification System</h2>
            <p>This is a test email to verify your email configuration.</p>
            <p>If you receive this email, your configuration is working correctly!</p>
            <p><strong>From:</strong> nimharachalana12@gmail.com</p>
            <p><strong>To:</strong> {$test_email}</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
        </body>
        </html>
        ";
        
        // Clear previous errors
        error_clear_last();
        
        // Try sending
        if(function_exists('send_return_email')){
            $test_result = @send_return_email($test_email, $test_subject, $test_message);
        } else {
            // Direct test
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: Inventory Management System <nimharachalana12@gmail.com>\r\n";
            $test_result = @mail($test_email, $test_subject, $test_message, $headers);
        }
        
        if(!$test_result){
            $error = error_get_last();
            $test_error = $error ? $error['message'] : 'No error message available';
        }
    } else {
        $test_error = "Invalid email address";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Return Email Configuration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #dc3545;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 10px;
        }
        .info-box {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background: #dc3545;
            color: white;
            padding: 12px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .form-group {
            margin: 20px 0;
        }
        input[type="email"] {
            width: 300px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #c82333;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Test Return Email Configuration</h1>
        
        <div class="info-box">
            <strong>Purpose:</strong> This tool tests if emails can be sent from <code>nimharachalana12@gmail.com</code> to supplier emails when processing returns.
        </div>
        
        <h2>Current PHP.ini Configuration</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><strong>SMTP Server</strong></td>
                <td><?php echo $config_info['smtp'] ?: 'Not Set'; ?></td>
                <td><?php echo $config_info['smtp'] ? '✅ Set' : '❌ Not Set'; ?></td>
            </tr>
            <tr>
                <td><strong>SMTP Port</strong></td>
                <td><?php echo $config_info['smtp_port'] ?: 'Not Set'; ?></td>
                <td><?php echo $config_info['smtp_port'] ? '✅ Set' : '⚠️ Not Set'; ?></td>
            </tr>
            <tr>
                <td><strong>sendmail_from</strong></td>
                <td><?php echo $config_info['sendmail_from'] ?: 'Not Set'; ?></td>
                <td><?php echo $config_info['sendmail_from'] ? '✅ Set' : '❌ Not Set'; ?></td>
            </tr>
            <tr>
                <td><strong>sendmail_path</strong></td>
                <td><?php echo $config_info['sendmail_path'] ?: 'Not Set'; ?></td>
                <td><?php echo $config_info['sendmail_path'] ? '✅ Set' : '⚠️ Not Set'; ?></td>
            </tr>
        </table>
        
        <?php if(empty($config_info['smtp'])): ?>
        <div class="warning">
            <h3>⚠️ Configuration Issue Detected</h3>
            <p><strong>SMTP is not configured in php.ini.</strong></p>
            <p>To fix this, you need to edit your <code>php.ini</code> file (usually at <code>C:\xampp\php\php.ini</code>):</p>
            <ol>
                <li>Open <code>php.ini</code> in a text editor</li>
                <li>Find the <code>[mail function]</code> section</li>
                <li>Add or uncomment these lines:
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px;">
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = nimharachalana12@gmail.com</pre>
                </li>
                <li>Save the file and <strong>restart Apache</strong> in XAMPP</li>
            </ol>
            <p><strong>Note:</strong> For Gmail, you may also need to configure sendmail or use an SMTP library. The <code>mail()</code> function on XAMPP often doesn't work directly with Gmail.</p>
        </div>
        <?php endif; ?>
        
        <h2>Test Email Sending</h2>
        <form method="post">
            <div class="form-group">
                <label><strong>Enter email address to test:</strong></label><br><br>
                <input type="email" name="test_email" placeholder="supplier@example.com" required>
                <button type="submit">Send Test Email</button>
            </div>
        </form>
        
        <?php if($test_result !== null): ?>
            <?php if($test_result): ?>
                <div class="success">
                    <h3>✅ Test Email Sent Successfully!</h3>
                    <p>The email was sent. Check the recipient's inbox (and spam folder) for the test email.</p>
                    <p><strong>Sent to:</strong> <?php echo htmlspecialchars($_POST['test_email']); ?></p>
                    <p><strong>From:</strong> nimharachalana12@gmail.com</p>
                </div>
            <?php else: ?>
                <div class="error">
                    <h3>❌ Test Email Failed</h3>
                    <p><strong>Error:</strong> <?php echo htmlspecialchars($test_error); ?></p>
                    <p><strong>Possible causes:</strong></p>
                    <ul>
                        <li>SMTP not properly configured in php.ini</li>
                        <li>Gmail requires App Password (not regular password)</li>
                        <li>XAMPP's mail() function doesn't work with Gmail directly</li>
                        <li>Firewall blocking port 587 or 465</li>
                        <li>sendmail_path not configured correctly</li>
                    </ul>
                    <p><strong>Solution:</strong> The <code>mail()</code> function in XAMPP often doesn't work with Gmail. Consider using PHPMailer or a similar SMTP library for reliable email sending.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="info-box" style="margin-top: 30px;">
            <h3>📝 What to Check:</h3>
            <ol>
                <li><strong>php.ini location:</strong> Usually at <code>C:\xampp\php\php.ini</code></li>
                <li><strong>Apache restart:</strong> After changing php.ini, restart Apache in XAMPP Control Panel</li>
                <li><strong>Gmail App Password:</strong> If using Gmail, you need an App Password, not your regular password</li>
                <li><strong>Error logs:</strong> Check <code>C:\xampp\php\logs\php_error_log</code> for detailed error messages</li>
            </ol>
        </div>
        
        <p style="margin-top: 30px;">
            <a href="add_return.php" style="color: #dc3545; text-decoration: none;">← Back to Return Management</a>
        </p>
    </div>
</body>
</html>


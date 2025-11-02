<?php
/**
 * Email Configuration Diagnostic Tool
 * This will help identify why emails are not being sent
 */
require_once('includes/load.php');
page_require_level(1);

$diagnostics = [];
$email_working = false;

// Check 1: PHP mail function exists
$diagnostics['php_mail_function'] = function_exists('mail') ? '✅ Available' : '❌ Not Available';

// Check 2: Check PHP mail configuration
$diagnostics['sendmail_path'] = ini_get('sendmail_path');
$diagnostics['smtp_server'] = ini_get('SMTP');
$diagnostics['smtp_port'] = ini_get('smtp_port');
$diagnostics['sendmail_from'] = ini_get('sendmail_from');

// Check 3: Test email sending capability
$test_email = isset($_GET['test_email']) ? $_GET['test_email'] : '';
if($test_email && filter_var($test_email, FILTER_VALIDATE_EMAIL)){
  $test_subject = "Test Email from Inventory System";
  $test_message = "This is a test email. If you receive this, your email configuration is working!";
  $test_headers = "From: Inventory System <noreply@inventorysystem.com>\r\n";
  $test_headers .= "MIME-Version: 1.0\r\n";
  $test_headers .= "Content-type:text/html;charset=UTF-8\r\n";
  
  $diagnostics['test_result'] = @mail($test_email, $test_subject, $test_message, $test_headers);
  $diagnostics['test_email_sent'] = $diagnostics['test_result'] ? '✅ Email sent (check your inbox/spam)' : '❌ Email failed to send';
  
  if(!$diagnostics['test_result']){
    $last_error = error_get_last();
    $diagnostics['last_error'] = isset($last_error['message']) ? $last_error['message'] : 'No error message available';
  }
}

// Check 4: Check if send_email function exists
$diagnostics['send_email_function'] = function_exists('send_email') ? '✅ Available' : '❌ Not Available';

// Check 5: PHP Version
$diagnostics['php_version'] = phpversion();

// Check 6: Operating System
$diagnostics['operating_system'] = PHP_OS;

// Check 7: Server Software
$diagnostics['server_software'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration Diagnostic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 10px;
        }
        .diagnostic-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .diagnostic-table th {
            background: #dc3545;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .diagnostic-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .diagnostic-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .status-ok { color: #28a745; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
        .test-form {
            background: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #c82333;
        }
        .solution {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .solution h3 {
            margin-top: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Configuration Diagnostic Tool</h1>
        
        <h2>System Information</h2>
        <table class="diagnostic-table">
            <tr>
                <th>Check</th>
                <th>Status/Value</th>
            </tr>
            <tr>
                <td><strong>PHP Mail Function</strong></td>
                <td class="<?php echo strpos($diagnostics['php_mail_function'], '✅') !== false ? 'status-ok' : 'status-error'; ?>">
                    <?php echo $diagnostics['php_mail_function']; ?>
                </td>
            </tr>
            <tr>
                <td><strong>PHP Version</strong></td>
                <td><?php echo $diagnostics['php_version']; ?></td>
            </tr>
            <tr>
                <td><strong>Operating System</strong></td>
                <td><?php echo $diagnostics['operating_system']; ?></td>
            </tr>
            <tr>
                <td><strong>Server Software</strong></td>
                <td><?php echo $diagnostics['server_software']; ?></td>
            </tr>
            <tr>
                <td><strong>Send Email Function Available</strong></td>
                <td class="<?php echo strpos($diagnostics['send_email_function'], '✅') !== false ? 'status-ok' : 'status-error'; ?>">
                    <?php echo $diagnostics['send_email_function']; ?>
                </td>
            </tr>
        </table>
        
        <h2>Email Configuration</h2>
        <table class="diagnostic-table">
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><strong>SMTP Server</strong></td>
                <td><?php echo $diagnostics['smtp_server'] ?: '<span class="status-error">❌ Not Configured</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>SMTP Port</strong></td>
                <td><?php echo $diagnostics['smtp_port'] ?: '<span class="status-warning">⚠️ Not Set</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>Sendmail Path</strong></td>
                <td><?php echo $diagnostics['sendmail_path'] ?: '<span class="status-error">❌ Not Configured</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>Sendmail From</strong></td>
                <td><?php echo $diagnostics['sendmail_from'] ?: '<span class="status-warning">⚠️ Not Set</span>'; ?></td>
            </tr>
        </table>
        
        <?php if(isset($diagnostics['test_email_sent'])): ?>
        <h2>Test Email Result</h2>
        <table class="diagnostic-table">
            <tr>
                <td><strong>Result</strong></td>
                <td class="<?php echo strpos($diagnostics['test_email_sent'], '✅') !== false ? 'status-ok' : 'status-error'; ?>">
                    <?php echo $diagnostics['test_email_sent']; ?>
                </td>
            </tr>
            <?php if(isset($diagnostics['last_error'])): ?>
            <tr>
                <td><strong>Error Message</strong></td>
                <td class="status-error"><?php echo htmlspecialchars($diagnostics['last_error']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php endif; ?>
        
        <div class="test-form">
            <h3>Test Email Sending</h3>
            <form method="get">
                <label>Enter your email address to test:</label><br><br>
                <input type="email" name="test_email" placeholder="your.email@example.com" required style="padding: 8px; width: 300px; border: 1px solid #ddd; border-radius: 5px;">
                <button type="submit" class="btn">Send Test Email</button>
            </form>
        </div>
        
        <div class="solution">
            <h3>🔧 Solutions to Fix Email Sending</h3>
            
            <h4>Problem:</h4>
            <p>PHP's <code>mail()</code> function requires a mail server to be configured. XAMPP doesn't include one by default.</p>
            
            <h4>Solution 1: Configure PHP to use Gmail SMTP (Recommended for Testing)</h4>
            <ol>
                <li>Open <code>php.ini</code> file (usually in <code>C:\xampp\php\php.ini</code>)</li>
                <li>Find the <code>[mail function]</code> section</li>
                <li>Add or modify these lines:
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;">
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
                    </pre>
                </li>
                <li>However, this requires additional configuration. <strong>Better solution below:</strong></li>
            </ol>
            
            <h4>Solution 2: Use PHPMailer Library (Best Solution)</h4>
            <p>I can help you set up PHPMailer which works much better with Gmail, Outlook, and other email providers.</p>
            
            <h4>Solution 3: For Production Server</h4>
            <p>On a live server (like cPanel, shared hosting, VPS), the mail() function usually works automatically.</p>
            
            <p><strong>Would you like me to set up PHPMailer for you? It's the most reliable solution.</strong></p>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background: #d1ecf1; border-radius: 5px;">
            <strong>Current Status:</strong> <?php 
                if(!$diagnostics['smtp_server'] && !$diagnostics['sendmail_path']) {
                    echo '<span class="status-error">❌ Email is NOT configured. You need to set up SMTP or use PHPMailer.</span>';
                } else {
                    echo '<span class="status-warning">⚠️ Email may work, but testing is recommended.</span>';
                }
            ?>
        </div>
        
        <p style="margin-top: 20px;">
            <a href="add_return.php" style="color: #dc3545; text-decoration: none;">← Back to Return Management</a>
        </p>
    </div>
</body>
</html>


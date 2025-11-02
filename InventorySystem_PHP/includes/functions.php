<?php
$errors = array();

/*--------------------------------------------------------------*/
/* Escape special characters for SQL */
function real_escape($str){
    global $con;
    return mysqli_real_escape_string($con, $str);
}

/*--------------------------------------------------------------*/
/* Remove HTML characters */
function remove_junk($str){
    $str = nl2br($str);
    $str = htmlspecialchars(strip_tags($str), ENT_QUOTES);
    return $str;
}

/*--------------------------------------------------------------*/
/* Uppercase first character */
function first_character($str){
    $val = str_replace('-', " ", $str);
    return ucfirst($val);
}

/*--------------------------------------------------------------*/
/* Check input fields not empty */
function validate_fields($var){
    global $errors;
    foreach ($var as $field) {
        $val = remove_junk($_POST[$field] ?? '');
        if($val == ''){
            $errors[] = $field ." can't be blank.";
        }
    }
}

/*--------------------------------------------------------------*/
/* Display session messages */
function display_msg($msg = array()){
    $output = '';
    if (!empty($msg)) {
        foreach ($msg as $key => $value) {
            $output .= "<div class=\"alert alert-{$key}\">";
            $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
            $output .= remove_junk($value);
            $output .= "</div>";
        }
    }
    return $output;
}

/*--------------------------------------------------------------*/
/* Redirect to another page */
function redirect($url, $permanent = false){
    if (!headers_sent()) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
    }
    exit();
}

/*--------------------------------------------------------------*/
/* Count ID (used for table rows) */
function count_id(){
    static $count = 1;
    return $count++;
}

/* Create readable date */
function read_date($str){
    if($str)
        return date('F j, Y, g:i:s a', strtotime($str));
    else
        return null;
}

/*--------------------------------------------------------------*/
/* Create current datetime */
function make_date(){
    return strftime("%Y-%m-%d %H:%M:%S", time());
}

/*--------------------------------------------------------------*/
/* Total sale, buying price, and profit */
function total_price($totals){
    $sum = 0;
    $sub = 0;
    foreach($totals as $total ){
        $sum += $total['total_saleing_price'];
        $sub += $total['total_buying_price'];
    }
    $profit = $sum - $sub;
    return array($sum, $profit);
}

/*--------------------------------------------------------------*/
/* Generate random string */
function randString($length = 5){
    $str = '';
    $cha = "0123456789abcdefghijklmnopqrstuvwxyz";
    for($x = 0; $x < $length; $x++){
        $str .= $cha[mt_rand(0, strlen($cha)-1)];
    }
    return $str;
}

/*--------------------------------------------------------------*/
/* Send email function - REPLACE THE OLD ONE WITH THIS */
function send_email($to, $subject, $message, $from_email = "nuwaniprabhashi2003@gmail.com") {
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Inventory System <" . $from_email . ">" . "\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";

    return mail($to, $subject, $message, $headers);
}

/*--------------------------------------------------------------*/
/* Send return notification email function - for add_return.php */
function send_return_email($to, $subject, $message) {
    $from_email = "nimharachalana12@gmail.com";
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: HealStock Pvt Ltd <" . $from_email . ">" . "\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    // Clear any previous errors
    error_clear_last();
    
    // Try to send email
    $result = @mail($to, $subject, $message, $headers);
    
    // If failed, log the error for debugging
    if(!$result){
        $error = error_get_last();
        if($error){
            error_log("Email sending failed to {$to}: " . $error['message']);
        }
        
        // Check php.ini mail configuration
        $smtp = ini_get('SMTP');
        $smtp_port = ini_get('smtp_port');
        $sendmail_from = ini_get('sendmail_from');
        
        if(empty($smtp)){
            error_log("Email config: SMTP not configured in php.ini");
        } else {
            error_log("Email config: SMTP={$smtp}, Port={$smtp_port}, From={$sendmail_from}");
        }
    }
    
    return $result;
}



/*--------------------------------------------------------------*/
/* Send invoice email function */
/*--------------------------------------------------------------*/
function send_invoice_email($invoice_number, $customer_email, $customer_name, $customer_phone) {
    global $db;
    
    // Debug logging
    error_log("send_invoice_email called with: invoice={$invoice_number}, email={$customer_email}");
    
    // Validate email
    if(empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        error_log("Email validation failed: empty or invalid format");
        return false;
    }
    
    // Get all sale items for this invoice
    $sql = "SELECT s.*, p.product_name 
            FROM sales s 
            LEFT JOIN product p ON s.sale_product_id = p.p_id 
            WHERE s.invoice_number = '{$db->escape($invoice_number)}' 
            ORDER BY s.sales_id";
    
    $result = $db->query($sql);
    
    if(!$result || $db->num_rows($result) == 0) {
        error_log("No sales records found for invoice: {$invoice_number}");
        return false;
    }
    
    error_log("Found " . $db->num_rows($result) . " items for invoice {$invoice_number}");
    
    // Fetch all items and calculate totals
    $products = [];
    $subtotal = 0;
    $total_discount = 0;
    $grand_total = 0;
    $sale_date = '';
    
    while($row = $db->fetch_assoc($result)) {
        $product_total = $row['sale_selling_price'] * $row['quantity'];
        $discount_amount = $row['discount'];
        $final_total = $product_total - $discount_amount;
        
        $products[] = [
            'name' => $row['product_name'],
            'price' => $row['sale_selling_price'],
            'quantity' => $row['quantity'],
            'total' => $product_total,
            'discount' => $discount_amount,
            'final_total' => $final_total
        ];
        
        $subtotal += $product_total;
        $total_discount += $discount_amount;
        $grand_total += $final_total;
        
        if(empty($sale_date)) {
            $sale_date = $row['created_at'];
        }
    }
$email_body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #' . htmlspecialchars($invoice_number) . '</title>
    <style type="text/css">
        /* ==== CLIENT-SPECIFIC RESET ==== */
        #outlook a { padding:0; }
        body { width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; }
        .ReadMsgBody { width:100%; } .ExternalClass { width:100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height:100%; }

        /* ==== BASE ==== */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background:#f5f7fa; color:#222; line-height:1.5; }
        a { color:#0066cc; text-decoration:none; }
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

        /* ==== LAYOUT ==== */
        .wrapper { max-width:640px; margin:20px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,.08); }
        .pad { padding:0 32px; }
        .pad-sm { padding:0 20px; }

        /* ==== HEADER ==== */
        .header { background:#0066cc; color:#fff; padding:32px 32px 24px; text-align:center; }
        .logo { font-size:28px; font-weight:700; margin:0 0 4px; }
        .tagline { font-size:15px; opacity:0.9; margin:0; }

        /* ==== HERO NOTE ==== */
        .hero-note { background:#e8f4fc; border-left:5px solid #0066cc; margin:24px 32px; padding:16px 20px; border-radius:0 8px 8px 0; }
        .hero-note strong { color:#004c99; }

        /* ==== INFO GRID ==== */
        .info-grid { display:flex; flex-wrap:wrap; gap:32px; margin:0 0 32px; }
        .info-block { flex:1; min-width:240px; }
        .info-block h3 { margin:0 0 12px; font-size:16px; color:#222; font-weight:600; }
        .info-block p { margin:6px 0; font-size:14px; color:#444; }
        .info-block strong { color:#222; }

        /* ==== TABLE ==== */
        .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .items-table { width:100%; min-width:560px; }
        .items-table th { background:#f8f9fa; color:#222; font-weight:600; text-align:left; padding:14px 16px; font-size:14px; }
        .items-table td { padding:14px 16px; border-bottom:1px solid #eaeaea; font-size:14px; }
        .items-table tr:nth-child(even) td { background:#fcfcfc; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }

        /* ==== TOTALS ==== */
        .totals { float:right; width:100%; max-width:340px; margin:24px 0 32px; }
        .totals td { padding:10px 16px; font-size:15px; }
        .totals .label { text-align:right; font-weight:600; color:#222; background:#f8f9fa; }
        .totals .value { text-align:right; font-weight:600; white-space:nowrap; }
        .grand td { background:#0066cc !important; color:#fff !important; font-size:16px; font-weight:700; }

        /* ==== FOOTER ==== */
        .footer { background:#fafafa; border-top:1px solid #eaeaea; padding:28px 32px; text-align:center; font-size:12px; color:#666; }
        .footer a { color:#0066cc; }

        /* ==== RESPONSIVE ==== */
        @media screen and (max-width: 600px) {
            .wrapper { margin:10px; border-radius:8px; }
            .pad { padding:0 20px; }
            .header { padding:24px 20px 20px; }
            .hero-note { margin:20px; }
            .info-grid { gap:24px; }
            .info-block { min-width:100%; }
            .totals { float:none; max-width:none; }
            .items-table { min-width:auto; }
            .items-table th,
            .items-table td { padding:12px 10px; font-size:13px; }
            .hide-mobile { display:none; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#f5f7fa;">
    <!-- Wrapper -->
    <div class="wrapper">

        <!-- Hero Note -->
        <div class="hero-note">
            <p style="margin:0 0 4px;"><strong>Invoice #' . htmlspecialchars($invoice_number) . '</strong></p>
            <p style="margin:0;">Thank you for your purchase. Your invoice is attached below.</p>
        </div>

        <!-- Header -->
        <div class="header">
            <h1 class="logo">HealStock Pvt. Ltd</h1>
            <p class="tagline">Your Health, Our Priority</p>
        </div>

        <div class="pad">

            <!-- Company Info -->
            <p style="margin:24px 0 8px; font-size:14px; color:#555; line-height:1.6;">
                123 Main Street, Colombo 01, Sri Lanka<br>
                Phone: <a href="tel:+94112345678" style="color:#0066cc;">+94 11 234 5678</a> |
                Email: <a href="mailto:info@healstock.com" style="color:#0066cc;">info@healstock.com</a><br>
                <a href="https://www.healstock.com" target="_blank" style="color:#0066cc;">www.healstock.com</a>
            </p>

            <!-- Invoice & Customer Grid -->
            <div class="info-grid">
                <div class="info-block">
                    <h3>Invoice Details</h3>
                    <p><strong>Invoice #:</strong> ' . htmlspecialchars($invoice_number) . '</p>
                    <p><strong>Date:</strong> ' . date('F j, Y', strtotime($sale_date)) . '</p>
                    <p><strong>Time:</strong> ' . date('g:i A', strtotime($sale_date)) . '</p>
                </div>
                <div class="info-block">
                    <h3>Bill To</h3>
                    <p><strong>' . htmlspecialchars($customer_name) . '</strong></p>
                    <p>' . htmlspecialchars($customer_phone) . '</p>'
                    . (!empty($customer_email) ? '<p>' . htmlspecialchars($customer_email) . '</p>' : '') . '
                </div>
            </div>

            <!-- Line Items -->
            <div class="table-wrap">
                <table class="items-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right hide-mobile">Unit Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right hide-mobile">Discount</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>';
foreach ($products as $p) {
    $email_body .= '
                        <tr>
                            <td style="font-weight:500;">' . htmlspecialchars($p['name']) . '</td>
                            <td class="text-right hide-mobile">LKR ' . number_format($p['price'], 2) . '</td>
                            <td class="text-center">' . (int)$p['quantity'] . '</td>
                            <td class="text-right">LKR ' . number_format($p['total'], 2) . '</td>
                            <td class="text-right hide-mobile">LKR ' . number_format($p['discount'], 2) . '</td>
                            <td class="text-right" style="font-weight:600;">LKR ' . number_format($p['final_total'], 2) . '</td>
                        </tr>';
}
$email_body .= '
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <table class="totals" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">LKR ' . number_format($subtotal, 2) . '</td>
                </tr>
                <tr>
                    <td class="label">Total Discount</td>
                    <td class="value">-LKR ' . number_format($total_discount, 2) . '</td>
                </tr>
                <tr class="grand">
                    <td class="label">Grand Total</td>
                    <td class="value">LKR ' . number_format($grand_total, 2) . '</td>
                </tr>
            </table>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin:0 0 8px;"><strong>Thank you for choosing HealStock!</strong></p>
            <p style="margin:0; font-size:11px; color:#888;">
                Generated on ' . date('F j, Y \a\t g:i A') . '<br>
                This is an automated message — please do not reply.<br>
                Questions? Reach us at <a href="mailto:info@healstock.com">info@healstock.com</a>
            </p>
        </div>

    </div>
</body>
</html>';
    
    // Email settings
    $subject = "Invoice #" . $invoice_number . " - HealStock Pvt.Ltd";
    $from_email = "chamikaliyanage2002322@gmail.com";
    
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: HealStock Pvt.Ltd <" . $from_email . ">\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";
    
    // Clear any previous errors
    error_clear_last();
    
    // Try to send email
    $result = @mail($customer_email, $subject, $email_body, $headers);
    
    // Log result
    if($result) {
        error_log("Invoice email sent successfully to {$customer_email} for invoice {$invoice_number}");
    } else {
        $error = error_get_last();
        error_log("Failed to send invoice email to {$customer_email} for invoice {$invoice_number}. Error: " . ($error ? $error['message'] : 'Unknown'));
    }
    
    return $result;
}



?>


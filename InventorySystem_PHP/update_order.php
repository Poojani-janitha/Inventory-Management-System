<?php
require_once('includes/load.php');
if(isset($_POST['o_id'])){
    $o_id = (int)$_POST['o_id'];
    $quantity = (int)$_POST['quantity'];
    $status = remove_junk($db->escape($_POST['status']));

    // Get current order details to check if quantity changed
    $current_sql = "SELECT po.*, si.s_name, si.email 
                    FROM purchase_order po 
                    JOIN supplier_info si ON po.s_id = si.s_id 
                    WHERE po.o_id = '{$o_id}'";
    $current_result = $db->query($current_sql);
    
    if($current_result && $current_result->num_rows > 0) {
        $current_order = $current_result->fetch_assoc();
        $old_quantity = (int)$current_order['quantity'];
        
        // Update the order
        $sql = "UPDATE purchase_order SET quantity='{$quantity}', status='{$status}' WHERE o_id='{$o_id}'";
        if($db->query($sql)){
            
            // Check if quantity changed and send email if it did
            if($old_quantity != $quantity) {
                $to = $current_order['email'];
                $subject = "📝 Order Quantity Updated - {$current_order['product_name']}";
                
                $message = "
                <!DOCTYPE html>
                <html>
                <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Order Quantity Updated - Inventory Management System</title>
                  <style>
                    body { 
                      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                      margin: 0; 
                      padding: 20px; 
                      color: #333;
                    }
                    .email-container { 
                      max-width: 600px; 
                      margin: 0 auto; 
                      background: #ffffff; 
                      border-radius: 15px; 
                      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                      overflow: hidden;
                    }
                    .header { 
                      background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
                      color: white; 
                      padding: 30px 20px; 
                      text-align: center; 
                      position: relative;
                    }
                    .header::before {
                      content: '';
                      position: absolute;
                      top: 0;
                      left: 0;
                      right: 0;
                      bottom: 0;
                      background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"1\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>') repeat;
                      opacity: 0.3;
                    }
                    .header h1 { 
                      margin: 0; 
                      font-size: 28px; 
                      font-weight: 600; 
                      position: relative;
                      z-index: 1;
                    }
                    .header .subtitle {
                      margin: 8px 0 0 0;
                      font-size: 16px;
                      opacity: 0.9;
                      position: relative;
                      z-index: 1;
                    }
                    .content { 
                      padding: 40px 30px; 
                      background: #ffffff;
                    }
                    .greeting {
                      font-size: 18px;
                      color: #2c3e50;
                      margin-bottom: 25px;
                      line-height: 1.6;
                    }
                    .update-card {
                      background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
                      border-radius: 12px;
                      padding: 25px;
                      margin: 25px 0;
                      border-left: 5px solid #ff9800;
                      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                    }
                    .quantity-change {
                      background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
                      border-radius: 8px;
                      padding: 20px;
                      margin: 20px 0;
                      border: 2px solid #4CAF50;
                    }
                    .quantity-comparison {
                      display: flex;
                      justify-content: space-around;
                      align-items: center;
                      margin: 20px 0;
                      flex-wrap: wrap;
                      gap: 15px;
                    }
                    .old-quantity, .new-quantity {
                      text-align: center;
                      padding: 15px;
                      border-radius: 8px;
                      min-width: 120px;
                    }
                    .old-quantity {
                      background: linear-gradient(135deg, #ffcdd2, #ffab91);
                      color: #d32f2f;
                    }
                    .new-quantity {
                      background: linear-gradient(135deg, #c8e6c9, #a5d6a7);
                      color: #2e7d32;
                    }
                    .arrow {
                      font-size: 24px;
                      color: #ff9800;
                      font-weight: bold;
                    }
                    .order-table { 
                      width: 100%; 
                      border-collapse: collapse; 
                      margin-top: 15px;
                      background: white;
                      border-radius: 8px;
                      overflow: hidden;
                      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                    }
                    .order-table th { 
                      background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
                      color: #fff; 
                      padding: 15px 12px; 
                      text-align: left; 
                      font-weight: 600;
                      font-size: 14px;
                      text-transform: uppercase;
                      letter-spacing: 0.5px;
                    }
                    .order-table td { 
                      padding: 15px 12px; 
                      border-bottom: 1px solid #e9ecef;
                      font-size: 15px;
                    }
                    .order-table tr:last-child td {
                      border-bottom: none;
                    }
                    .order-table tr:nth-child(even) {
                      background-color: #f8f9fa;
                    }
                    .highlight {
                      background: linear-gradient(135deg, #4CAF50, #45a049);
                      color: white;
                      padding: 4px 8px;
                      border-radius: 4px;
                      font-weight: 600;
                    }
                    .total-amount {
                      background: linear-gradient(135deg, #ff6b6b, #ee5a52);
                      color: white;
                      padding: 8px 12px;
                      border-radius: 6px;
                      font-weight: 700;
                      font-size: 16px;
                    }
                    .notice {
                      background: #e3f2fd;
                      border-left: 4px solid #2196F3;
                      padding: 20px;
                      margin: 25px 0;
                      border-radius: 0 8px 8px 0;
                    }
                    .contact-info {
                      background: #f8f9fa;
                      padding: 20px;
                      border-radius: 8px;
                      margin: 25px 0;
                      text-align: center;
                    }
                    .contact-info h3 {
                      color: #2c3e50;
                      margin: 0 0 15px 0;
                      font-size: 18px;
                    }
                    .contact-links {
                      display: flex;
                      justify-content: center;
                      gap: 20px;
                      flex-wrap: wrap;
                    }
                    .contact-links a {
                      color: #ff9800;
                      text-decoration: none;
                      font-weight: 600;
                      padding: 8px 16px;
                      border: 2px solid #ff9800;
                      border-radius: 25px;
                      transition: all 0.3s ease;
                    }
                    .contact-links a:hover {
                      background: #ff9800;
                      color: white;
                    }
                    .footer { 
                      background: #2c3e50;
                      color: #bdc3c7; 
                      padding: 20px 30px; 
                      text-align: center; 
                      font-size: 14px;
                      line-height: 1.5;
                    }
                    .footer p {
                      margin: 5px 0;
                    }
                    .company-logo {
                      width: 40px;
                      height: 40px;
                      background: white;
                      border-radius: 50%;
                      display: inline-flex;
                      align-items: center;
                      justify-content: center;
                      margin-bottom: 10px;
                      font-weight: bold;
                      color: #ff9800;
                      font-size: 18px;
                    }
                    @media (max-width: 600px) {
                      .email-container { margin: 10px; }
                      .content { padding: 20px 15px; }
                      .contact-links { flex-direction: column; align-items: center; }
                      .quantity-comparison { flex-direction: column; }
                    }
                  </style>
                </head>
                <body>
                  <div class='email-container'>
                    <div class='header'>
                      <div class='company-logo'>IMS</div>
                      <h1>📝 Order Quantity Updated</h1>
                      <p class='subtitle'>Inventory Management System</p>
                    </div>
                    <div class='content'>
                      <div class='greeting'>
                        Dear <strong>{$current_order['s_name']}</strong>,<br><br>
                        We have updated the quantity for your order. Please review the changes below:
                      </div>
                      
                      <div class='update-card'>
                        <h3 style='margin: 0 0 20px 0; color: #2c3e50; font-size: 20px;'>🔄 Quantity Change Details</h3>
                        
                        <div class='quantity-change'>
                          <h4 style='margin: 0 0 15px 0; color: #2e7d32; text-align: center;'>📊 Quantity Comparison</h4>
                          <div class='quantity-comparison'>
                            <div class='old-quantity'>
                              <div style='font-size: 14px; margin-bottom: 5px;'>Previous</div>
                              <div style='font-size: 24px; font-weight: bold;'>{$old_quantity} units</div>
                            </div>
                            <div class='arrow'>→</div>
                            <div class='new-quantity'>
                              <div style='font-size: 14px; margin-bottom: 5px;'>Updated</div>
                              <div style='font-size: 24px; font-weight: bold;'>{$quantity} units</div>
                            </div>
                          </div>
                        </div>
                        
                        <table class='order-table'>
                          <tr><th>Order Information</th><th>Details</th></tr>
                          <tr><td><strong>Product Name</strong></td><td>{$current_order['product_name']}</td></tr>
                          <tr><td><strong>Category</strong></td><td>{$current_order['category_name']}</td></tr>
                          <tr><td><strong>Unit Price</strong></td><td>Rs. " . number_format($current_order['price'], 2) . "</td></tr>
                          <tr><td><strong>New Total Amount</strong></td><td><span class='total-amount'>Rs. " . number_format($current_order['price'] * $quantity, 2) . "</span></td></tr>
                          <tr><td><strong>Order Date</strong></td><td>" . date('F j, Y \a\t g:i A', strtotime($current_order['order_date'])) . "</td></tr>
                          <tr><td><strong>Status</strong></td><td><span class='highlight'>{$current_order['status']}</span></td></tr>
                        </table>
                      </div>

                      <div class='notice'>
                        <h4 style='margin: 0 0 15px 0; color: #1976D2;'>⚠️ Important Notice</h4>
                        <p style='margin: 0; color: #424242; line-height: 1.6;'>
                          The total amount has been automatically recalculated based on the new quantity. 
                          Please confirm your acceptance of these changes and provide an updated delivery timeline if necessary.
                        </p>
                      </div>

                      <div class='contact-info'>
                        <h3>📞 Need Assistance?</h3>
                        <p style='margin: 0 0 15px 0; color: #666;'>For any questions about this update, please contact us:</p>
                        <div class='contact-links'>
                          <a href='mailto:admin@inventorysystem.lk'>📧 Email Support</a>
                          <a href='tel:+94112345678'>📞 Call Us</a>
                        </div>
                      </div>
                    </div>
                    <div class='footer'>
                      <p><strong>Inventory Management System</strong></p>
                      <p>This is an automated message. Please do not reply to this email.</p>
                      <p>© " . date('Y') . " All rights reserved.</p>
                    </div>
                  </div>
                </body>
                </html>
                ";

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                $headers .= "From: Inventory System <noreply@inventorysystem.com>\r\n";
                $headers .= "Reply-To: admin@inventorysystem.com\r\n";

                if(mail($to, $subject, $message, $headers)){
                    echo "Order updated successfully and email sent to supplier.";
                } else {
                    echo "Order updated successfully, but email could not be sent.";
                }
            } else {
                // Only status changed, no email needed
                echo "Order updated successfully.";
            }
        } else {
            echo "Failed to update order.";
        }
    } else {
        echo "Order not found.";
    }
}
?>

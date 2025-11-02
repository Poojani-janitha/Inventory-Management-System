<?php
/**
 * Email Invoice Function using PHPMailer
 * Sends invoice details via email after successful sale
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendInvoiceEmail($invoice_number, $customer_name, $customer_email, $customer_phone, $grand_total, $db) {
    // Validate email address
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email address: " . $customer_email);
        return false;
    }
    
    try {
        // Get invoice details from database
        $invoice_sql = "SELECT s.*, p.product_name 
                       FROM sales s 
                       LEFT JOIN product p ON s.sale_product_id = p.p_id 
                       WHERE s.invoice_number = '{$db->escape($invoice_number)}' 
                       ORDER BY s.sales_id ASC";
        
        $result = $db->query($invoice_sql);
        
        if (!$result || $db->num_rows($result) == 0) {
            error_log("No invoice data found for: " . $invoice_number);
            return false;
        }
        
        // Build invoice items table
        $items_html = '';
        $subtotal = 0;
        $invoice_date = '';
        
        while ($item = $db->fetch_assoc($result)) {
            if (empty($invoice_date)) {
                $invoice_date = date('F d, Y', strtotime($item['created_at']));
            }
            
            $product_name = !empty($item['product_name']) ? $item['product_name'] : 'Product #' . $item['sale_product_id'];
            $item_subtotal = $item['sale_selling_price'] * $item['quantity'];
            $discount_display = $item['discount'] > 0 ? 'LKR ' . number_format($item['discount'], 2) : '-';
            
            $items_html .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0;'>{$product_name}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$item['category_name']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: right;'>LKR " . number_format($item['sale_selling_price'], 2) . "</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: right;'>{$discount_display}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: right; font-weight: bold;'>LKR " . number_format($item['total'], 2) . "</td>
            </tr>";
            
            $subtotal += $item_subtotal;
        }
        
        // Create HTML message
        $html_message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Invoice</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                            <tr>
                                <td style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
                                    <h1 style='color: #ffffff; margin: 0; font-size: 28px;'>INVOICE</h1>
                                    <p style='color: #ffffff; margin: 10px 0 0 0; font-size: 16px;'>Thank you for your business!</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 30px;'>
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td>
                                                <p style='margin: 0 0 5px 0; color: #666;'><strong>Invoice Number:</strong></p>
                                                <p style='margin: 0 0 15px 0; font-size: 18px; color: #667eea; font-weight: bold;'>{$invoice_number}</p>
                                                <p style='margin: 0 0 5px 0; color: #666;'><strong>Date:</strong></p>
                                                <p style='margin: 0;'>{$invoice_date}</p>
                                            </td>
                                            <td align='right'>
                                                <p style='margin: 0 0 5px 0; color: #666;'><strong>Bill To:</strong></p>
                                                <p style='margin: 0 0 5px 0; font-weight: bold;'>{$customer_name}</p>
                                                <p style='margin: 0 0 5px 0; color: #666;'>{$customer_phone}</p>
                                                <p style='margin: 0; color: #666;'>{$customer_email}</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 0 30px 30px 30px;'>
                                    <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse;'>
                                        <thead>
                                            <tr style='background-color: #f8f9fa;'>
                                                <th style='padding: 12px; text-align: left; border-bottom: 2px solid #667eea; color: #667eea;'>Product</th>
                                                <th style='padding: 12px; text-align: center; border-bottom: 2px solid #667eea; color: #667eea;'>Category</th>
                                                <th style='padding: 12px; text-align: right; border-bottom: 2px solid #667eea; color: #667eea;'>Price</th>
                                                <th style='padding: 12px; text-align: center; border-bottom: 2px solid #667eea; color: #667eea;'>Qty</th>
                                                <th style='padding: 12px; text-align: right; border-bottom: 2px solid #667eea; color: #667eea;'>Discount</th>
                                                <th style='padding: 12px; text-align: right; border-bottom: 2px solid #667eea; color: #667eea;'>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {$items_html}
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 0 30px 30px 30px;'>
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td width='70%'></td>
                                            <td style='padding: 10px; text-align: right; color: #666;'>Subtotal:</td>
                                            <td style='padding: 10px; text-align: right; font-weight: bold;'>LKR " . number_format($subtotal, 2) . "</td>
                                        </tr>
                                        <tr style='background-color: #667eea; color: white;'>
                                            <td width='70%'></td>
                                            <td style='padding: 15px; text-align: right; font-size: 18px;'><strong>Grand Total:</strong></td>
                                            <td style='padding: 15px; text-align: right; font-size: 20px; font-weight: bold;'>LKR " . number_format($grand_total, 2) . "</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 30px; background-color: #f8f9fa; text-align: center; border-radius: 0 0 8px 8px;'>
                                    <p style='margin: 0 0 10px 0; color: #666;'>Thank you for your purchase!</p>
                                    <p style='margin: 0; color: #999; font-size: 12px;'>If you have any questions, please contact us.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
        
        // Create PHPMailer instance
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 0;                                       // Disable verbose debug output (set to 2 for debugging)
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'chamikaliyanage2002322@gmail.com';     // SMTP username
        $mail->Password   = 'vfwg uuhy dzxs ixfo';                  // SMTP App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to
        
        // Recipients
        $mail->setFrom('chamikaliyanage2002322@gmail.com', 'Sales Department');
        $mail->addAddress($customer_email, $customer_name);
        $mail->addReplyTo('chamikaliyanage2002322@gmail.com', 'Sales Department');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Invoice #{$invoice_number} - Thank You for Your Purchase";
        $mail->Body    = $html_message;
        $mail->AltBody = "Invoice #{$invoice_number}\n\nThank you for your purchase!\n\nCustomer: {$customer_name}\nTotal: LKR " . number_format($grand_total, 2) . "\n\nPlease view this email in HTML format for the complete invoice.";
        
        // Send email
        $mail->send();
        error_log("Invoice email sent successfully to: " . $customer_email);
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}
?>
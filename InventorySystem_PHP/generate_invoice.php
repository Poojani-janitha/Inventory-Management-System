<?php
require_once('includes/load.php');

// Check if invoice number is provided
if (!isset($_GET['invoice_number']) || empty($_GET['invoice_number'])) {
    die('Invoice number is required');
}

$invoice_number = $db->escape($_GET['invoice_number']);

// Get sales data for the invoice
$sales_sql = "SELECT s.*, p.product_name, p.selling_price 
              FROM sales s 
              LEFT JOIN product p ON s.sale_product_id = p.p_id 
              WHERE s.invoice_number = '{$invoice_number}' 
              ORDER BY s.sales_id";

$sales_result = $db->query($sales_sql);

if (!$sales_result || $db->num_rows($sales_result) == 0) {
    die('No sales found for invoice number: ' . $invoice_number);
}

// Get the first sale record for customer details
$first_sale = $db->fetch_assoc($sales_result);
$customer_name = $first_sale['name'];
$customer_phone = $first_sale['pNumber'];
$customer_email = $first_sale['email'];
$sale_date = $first_sale['created_at'];

// Reset result pointer
$sales_result = $db->query($sales_sql);

// Calculate totals
$subtotal = 0;
$total_discount = 0;
$grand_total = 0;

$products = [];
while ($sale = $db->fetch_assoc($sales_result)) {
    $product_total = $sale['sale_selling_price'] * $sale['quantity'];
    $discount_amount = $sale['discount']; // This is now the actual discount amount in LKR
    $final_total = $product_total - $discount_amount;
    
    $products[] = [
        'name' => $sale['product_name'],
        'price' => $sale['sale_selling_price'],
        'quantity' => $sale['quantity'],
        'total' => $product_total,
        'discount' => $discount_amount,
        'final_total' => $final_total
    ];
    
    $subtotal += $product_total;
    $total_discount += $discount_amount;
    $grand_total += $final_total;
}

// Generate HTML invoice
$invoice_html = generateInvoiceHTML($invoice_number, $customer_name, $customer_phone, $customer_email, $sale_date, $products, $subtotal, $total_discount, $grand_total);

// Set headers for HTML output (can be printed as PDF)
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the HTML invoice
echo $invoice_html;

function generateInvoiceHTML($invoice_number, $customer_name, $customer_phone, $customer_email, $sale_date, $products, $subtotal, $total_discount, $grand_total) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Invoice ' . $invoice_number . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            .company-name {
                font-size: 28px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }
            .company-details {
                font-size: 14px;
                color: #666;
                line-height: 1.5;
            }
            .invoice-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
            }
            .invoice-details, .customer-details {
                flex: 1;
            }
            .invoice-details h3, .customer-details h3 {
                margin: 0 0 10px 0;
                color: #333;
                font-size: 18px;
            }
            .invoice-details p, .customer-details p {
                margin: 5px 0;
                color: #666;
            }
            .products-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            .products-table th, .products-table td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }
            .products-table th {
                background-color: #f8f9fa;
                font-weight: bold;
                color: #333;
            }
            .products-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .totals-section {
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
            }
            .totals-table {
                width: 300px;
                border-collapse: collapse;
            }
            .totals-table td {
                padding: 8px 12px;
                border: 1px solid #ddd;
            }
            .totals-table .label {
                background-color: #f8f9fa;
                font-weight: bold;
                text-align: right;
            }
            .totals-table .amount {
                text-align: right;
                font-weight: bold;
            }
            .grand-total {
                background-color: #333 !important;
                color: white !important;
                font-size: 16px;
            }
            .footer {
                margin-top: 40px;
                text-align: center;
                color: #666;
                font-size: 12px;
                border-top: 1px solid #ddd;
                padding-top: 20px;
            }
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                z-index: 1000;
            }
            .print-button:hover {
                background-color: #0056b3;
            }
            @media print {
                body { margin: 0; padding: 0; }
                .invoice-container { box-shadow: none; }
                .print-button { display: none; }
            }
        </style>
    </head>
    <body>
        <button class="print-button" onclick="window.print()">
            🖨️ Print Invoice
        </button>
        <div class="invoice-container">
            <div class="header">
                <div class="company-name">Pharmacy Management System</div>
                <div class="company-details">
                    123 Main Street, Colombo 01, Sri Lanka<br>
                    Phone: +94 11 234 5678 | Email: info@pharmacy.com<br>
                    Website: www.pharmacy.com
                </div>
            </div>
            
            <div class="invoice-info">
                <div class="invoice-details">
                    <h3>Invoice Details</h3>
                    <p><strong>Invoice Number:</strong> ' . $invoice_number . '</p>
                    <p><strong>Date:</strong> ' . date('F j, Y', strtotime($sale_date)) . '</p>
                    <p><strong>Time:</strong> ' . date('g:i A', strtotime($sale_date)) . '</p>
                </div>
                <div class="customer-details">
                    <h3>Bill To</h3>
                    <p><strong>Name:</strong> ' . htmlspecialchars($customer_name) . '</p>
                    <p><strong>Phone:</strong> ' . htmlspecialchars($customer_phone) . '</p>';
    
    if (!empty($customer_email)) {
        $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($customer_email) . '</p>';
    }
    
    $html .= '
                </div>
            </div>
            
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Unit Price (LKR)</th>
                        <th>Quantity</th>
                        <th>Subtotal (LKR)</th>
                        <th>Discount (LKR)</th>
                        <th>Total (LKR)</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($products as $product) {
        $html .= '
                    <tr>
                        <td>' . htmlspecialchars($product['name']) . '</td>
                        <td>' . number_format($product['price'], 2) . '</td>
                        <td>' . $product['quantity'] . '</td>
                        <td>' . number_format($product['total'], 2) . '</td>
                        <td>' . number_format($product['discount'], 2) . '</td>
                        <td>' . number_format($product['final_total'], 2) . '</td>
                    </tr>';
    }
    
    $html .= '
                </tbody>
            </table>
            
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">LKR ' . number_format($subtotal, 2) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Total Discount:</td>
                        <td class="amount">-LKR ' . number_format($total_discount, 2) . '</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="label">Grand Total:</td>
                        <td class="amount">LKR ' . number_format($grand_total, 2) . '</td>
                    </tr>
                </table>
            </div>
            
            <div class="footer">
                <p>Thank you for your business!</p>
                <p>This invoice was generated on ' . date('F j, Y \a\t g:i A') . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}
?>

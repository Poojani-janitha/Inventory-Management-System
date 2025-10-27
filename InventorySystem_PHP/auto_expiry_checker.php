<?php
/**
 * Auto Expiry Checker
 * This script checks for expired products and creates alerts
 * Should be run as a cron job daily
 */

require_once('includes/load.php');

// Function to check for expired products and create alerts
function check_expired_products() {
  global $db;
  
  // Find products that are expired or expiring within 30 days
  $sql = "SELECT p.*, s.email as supplier_email, s.name as supplier_name 
          FROM products p 
          LEFT JOIN suppliers s ON p.supplier_id = s.id 
          WHERE p.expiry_date IS NOT NULL 
          AND (p.expiry_date <= CURDATE() OR p.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))";
  
  $products = $db->while_loop($db->query($sql));
  
  foreach($products as $product) {
    $days_until_expiry = floor((strtotime($product['expiry_date']) - time()) / (60 * 60 * 24));
    
    if($days_until_expiry < 0) {
      // Product is expired
      $alert_message = "Product '{$product['name']}' expired on {$product['expiry_date']}. ";
      if($product['supplier_email']) {
        $alert_message .= "Contact supplier: {$product['supplier_name']} ({$product['supplier_email']}) for return.";
      }
      
      create_expiry_alert($product['id'], 'Expired', $alert_message);
      
    } elseif($days_until_expiry <= 7) {
      // Product expiring within 7 days
      $alert_message = "Product '{$product['name']}' expires in {$days_until_expiry} days ({$product['expiry_date']}). ";
      if($product['supplier_email']) {
        $alert_message .= "Consider contacting supplier: {$product['supplier_name']} ({$product['supplier_email']}) for return.";
      }
      
      create_expiry_alert($product['id'], 'Expiring Soon', $alert_message);
      
    } elseif($days_until_expiry <= 30) {
      // Product expiring within 30 days
      $alert_message = "Product '{$product['name']}' expires in {$days_until_expiry} days ({$product['expiry_date']}). Plan accordingly.";
      
      create_expiry_alert($product['id'], 'Expiry Warning', $alert_message);
    }
  }
}

// Function to create expiry alerts
function create_expiry_alert($product_id, $alert_type, $message) {
  global $db;
  
  // Check if alert already exists for this product and type
  $sql = "SELECT id FROM return_alerts 
          WHERE product_id = '{$product_id}' 
          AND alert_type = '{$alert_type}' 
          AND is_resolved = 0";
  
  $existing = $db->query($sql);
  
  if($db->num_rows($existing) == 0) {
    $sql = "INSERT INTO return_alerts (product_id, alert_type, alert_message, alert_date) 
            VALUES ('{$product_id}', '{$alert_type}', '{$message}', NOW())";
    
    if($db->query($sql)) {
      echo "Alert created for product ID {$product_id}: {$message}\n";
    }
  }
}

// Function to send email notifications for critical alerts
function send_expiry_notifications() {
  global $db;
  
  // Get critical alerts (expired products)
  $sql = "SELECT ra.*, p.name as product_name, s.email as supplier_email, s.name as supplier_name
          FROM return_alerts ra
          LEFT JOIN products p ON ra.product_id = p.id
          LEFT JOIN suppliers s ON p.supplier_id = s.id
          WHERE ra.alert_type = 'Expired' 
          AND ra.is_resolved = 0
          AND ra.alert_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
  
  $alerts = $db->while_loop($db->query($sql));
  
  if(count($alerts) > 0) {
    $subject = "Expired Products Alert - " . date('Y-m-d');
    $message = "The following products have expired and may need supplier returns:\n\n";
    
    foreach($alerts as $alert) {
      $message .= "- {$alert['product_name']} (Expired)\n";
      if($alert['supplier_email']) {
        $message .= "  Supplier: {$alert['supplier_name']} ({$alert['supplier_email']})\n";
      }
      $message .= "  Alert: {$alert['alert_message']}\n\n";
    }
    
    // Send email to admin (you can configure this)
    $admin_email = "admin@yourcompany.com"; // Configure this
    mail($admin_email, $subject, $message);
    
    echo "Expiry notification sent to {$admin_email}\n";
  }
}

// Function to generate supplier return suggestions
function generate_supplier_return_suggestions() {
  global $db;
  
  $sql = "SELECT p.*, s.email as supplier_email, s.name as supplier_name, s.return_policy
          FROM products p
          LEFT JOIN suppliers s ON p.supplier_id = s.id
          WHERE p.expiry_date IS NOT NULL 
          AND p.expiry_date <= CURDATE()
          AND s.email IS NOT NULL";
  
  $expired_products = $db->while_loop($db->query($sql));
  
  if(count($expired_products) > 0) {
    echo "\n=== SUPPLIER RETURN SUGGESTIONS ===\n";
    
    foreach($expired_products as $product) {
      echo "\nProduct: {$product['name']}\n";
      echo "Expired: {$product['expiry_date']}\n";
      echo "Supplier: {$product['supplier_name']}\n";
      echo "Email: {$product['supplier_email']}\n";
      echo "Return Policy: {$product['return_policy']}\n";
      echo "Suggested Action: Contact supplier for return authorization\n";
      echo "---\n";
    }
  }
}

// Main execution
if(php_sapi_name() === 'cli' || isset($_GET['run'])) {
  echo "Starting auto expiry check...\n";
  
  // Check for expired products
  check_expired_products();
  
  // Send notifications
  send_expiry_notifications();
  
  // Generate supplier suggestions
  generate_supplier_return_suggestions();
  
  echo "Auto expiry check completed.\n";
} else {
  // Web interface
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <title>Auto Expiry Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3>Auto Expiry Checker</h3>
            </div>
            <div class="card-body">
              <p>This tool checks for expired products and creates alerts.</p>
              <a href="?run=1" class="btn btn-primary">Run Expiry Check</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
  </html>
  <?php
}
?>

<?php
  $page_title = 'Auto Expiry Checker';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $check_run = false;
  $expiry_data = array();
  
  // Run expiry check if button clicked
  if(isset($_POST['run_check']) || isset($_GET['run'])){
    $check_run = true;
    $expiry_data = get_expiry_analysis();
  }
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Main Panel -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-time"></span>
          <span>Auto Expiry Checker</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="alert alert-info">
          <h4><i class="glyphicon glyphicon-info-sign"></i> About This Tool</h4>
          <p>This tool checks for expired products and creates alerts. It analyzes:</p>
          <ul>
            <li><strong>Expired Products</strong> - Products that have already expired</li>
            <li><strong>Expiring Soon</strong> - Products expiring within 7 days</li>
            <li><strong>Expiring This Month</strong> - Products expiring within 30 days</li>
            <li><strong>Expiring This Quarter</strong> - Products expiring within 90 days</li>
          </ul>
        </div>
        
        <form method="post" action="auto_expiry_checker.php">
          <button type="submit" name="run_check" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-play"></span> Run Expiry Check
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if($check_run): ?>

<!-- Statistics Cards -->
<div class="row">
  <div class="col-md-3">
    <div class="panel panel-danger">
      <div class="panel-heading text-center">
        <h3 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-remove-circle"></i>
        </h3>
        <h4>Expired Products</h4>
      </div>
      <div class="panel-body text-center">
        <h1 style="color: #d9534f; margin: 0; font-weight: bold;">
          <?php echo $expiry_data['stats']['expired']; ?>
        </h1>
        <p class="text-muted">Immediate Action Required</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="panel panel-warning">
      <div class="panel-heading text-center">
        <h3 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-warning-sign"></i>
        </h3>
        <h4>Expiring in 7 Days</h4>
      </div>
      <div class="panel-body text-center">
        <h1 style="color: #f0ad4e; margin: 0; font-weight: bold;">
          <?php echo $expiry_data['stats']['7_days']; ?>
        </h1>
        <p class="text-muted">Critical Warning</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="panel panel-info">
      <div class="panel-heading text-center">
        <h3 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-exclamation-sign"></i>
        </h3>
        <h4>Expiring in 30 Days</h4>
      </div>
      <div class="panel-body text-center">
        <h1 style="color: #5bc0de; margin: 0; font-weight: bold;">
          <?php echo $expiry_data['stats']['30_days']; ?>
        </h1>
        <p class="text-muted">Plan Ahead</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="panel panel-success">
      <div class="panel-heading text-center">
        <h3 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-ok-circle"></i>
        </h3>
        <h4>Expiring in 90 Days</h4>
      </div>
      <div class="panel-body text-center">
        <h1 style="color: #5cb85c; margin: 0; font-weight: bold;">
          <?php echo $expiry_data['stats']['90_days']; ?>
        </h1>
        <p class="text-muted">Monitor</p>
      </div>
    </div>
  </div>
</div>

<!-- Detailed Tables -->
<?php if(!empty($expiry_data['expired'])): ?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-danger">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-alert"></span>
          <span>EXPIRED PRODUCTS - Immediate Action Required</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead style="background-color: #f2dede;">
              <tr>
                <th class="text-center">#</th>
                <th>Product Details</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Buying Price</th>
                <th class="text-center">Total Value</th>
                <th class="text-center">Expired Date</th>
                <th class="text-center">Days Expired</th>
                <th>Supplier</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $counter = 1;
              foreach($expiry_data['expired'] as $product): 
                $total_value = $product['quantity'] * $product['buying_price'];
              ?>
              <tr style="background-color: #fff5f5;">
                <td class="text-center"><?php echo $counter++; ?></td>
                <td>
                  <strong style="color: #d9534f;"><?php echo $product['product_name']; ?></strong>
                  <br><small class="text-muted">ID: <?php echo $product['p_id']; ?></small>
                  <br><small class="text-muted">Category: <?php echo $product['category_name']; ?></small>
                </td>
                <td class="text-center">
                  <span class="label label-danger" style="font-size: 13px;">
                    <?php echo $product['quantity']; ?> units
                  </span>
                </td>
                <td class="text-center">
                  <strong>Rs. <?php echo number_format($product['buying_price'], 2); ?></strong>
                </td>
                <td class="text-center">
                  <strong style="color: #d9534f; font-size: 15px;">
                    Rs. <?php echo number_format($total_value, 2); ?>
                  </strong>
                </td>
                <td class="text-center">
                  <strong><?php echo date('Y-m-d', strtotime($product['expire_date'])); ?></strong>
                </td>
                <td class="text-center">
                  <span class="label label-danger" style="font-size: 13px;">
                    <?php echo abs($product['days_until_expiry']); ?> days ago
                  </span>
                </td>
                <td>
                  <strong><?php echo $product['supplier_name'] ?? 'N/A'; ?></strong>
                  <br><small>ID: <?php echo $product['s_id']; ?></small>
                  <?php if(!empty($product['contact_number'])): ?>
                    <br><small><i class="glyphicon glyphicon-phone"></i> <?php echo $product['contact_number']; ?></small>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <a href="add_return.php?product=<?php echo $product['p_id']; ?>" 
                     class="btn btn-danger btn-xs">
                    <span class="glyphicon glyphicon-arrow-left"></span> Create Return
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(!empty($expiry_data['7_days'])): ?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-warning-sign"></span>
          <span>EXPIRING WITHIN 7 DAYS - Critical Warning</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead style="background-color: #fcf8e3;">
              <tr>
                <th class="text-center">#</th>
                <th>Product Details</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Buying Price</th>
                <th class="text-center">Total Value</th>
                <th class="text-center">Expiry Date</th>
                <th class="text-center">Days Remaining</th>
                <th>Supplier</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $counter = 1;
              foreach($expiry_data['7_days'] as $product): 
                $total_value = $product['quantity'] * $product['buying_price'];
              ?>
              <tr style="background-color: #fffaf0;">
                <td class="text-center"><?php echo $counter++; ?></td>
                <td>
                  <strong style="color: #f0ad4e;"><?php echo $product['product_name']; ?></strong>
                  <br><small class="text-muted">ID: <?php echo $product['p_id']; ?></small>
                  <br><small class="text-muted">Category: <?php echo $product['category_name']; ?></small>
                </td>
                <td class="text-center">
                  <span class="label label-warning" style="font-size: 13px;">
                    <?php echo $product['quantity']; ?> units
                  </span>
                </td>
                <td class="text-center">
                  <strong>Rs. <?php echo number_format($product['buying_price'], 2); ?></strong>
                </td>
                <td class="text-center">
                  <strong style="color: #f0ad4e; font-size: 15px;">
                    Rs. <?php echo number_format($total_value, 2); ?>
                  </strong>
                </td>
                <td class="text-center">
                  <strong><?php echo date('Y-m-d', strtotime($product['expire_date'])); ?></strong>
                </td>
                <td class="text-center">
                  <span class="label label-warning" style="font-size: 13px;">
                    <?php echo $product['days_until_expiry']; ?> days
                  </span>
                </td>
                <td>
                  <strong><?php echo $product['supplier_name'] ?? 'N/A'; ?></strong>
                  <br><small>ID: <?php echo $product['s_id']; ?></small>
                  <?php if(!empty($product['contact_number'])): ?>
                    <br><small><i class="glyphicon glyphicon-phone"></i> <?php echo $product['contact_number']; ?></small>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <a href="add_return.php?product=<?php echo $product['p_id']; ?>" 
                     class="btn btn-warning btn-xs">
                    <span class="glyphicon glyphicon-arrow-left"></span> Plan Return
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(!empty($expiry_data['30_days'])): ?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-info-sign"></span>
          <span>EXPIRING WITHIN 30 DAYS - Plan Ahead</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead style="background-color: #d9edf7;">
              <tr>
                <th class="text-center">#</th>
                <th>Product Details</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Buying Price</th>
                <th class="text-center">Total Value</th>
                <th class="text-center">Expiry Date</th>
                <th class="text-center">Days Remaining</th>
                <th>Supplier</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $counter = 1;
              foreach($expiry_data['30_days'] as $product): 
                $total_value = $product['quantity'] * $product['buying_price'];
              ?>
              <tr>
                <td class="text-center"><?php echo $counter++; ?></td>
                <td>
                  <strong><?php echo $product['product_name']; ?></strong>
                  <br><small class="text-muted">ID: <?php echo $product['p_id']; ?></small>
                  <br><small class="text-muted">Category: <?php echo $product['category_name']; ?></small>
                </td>
                <td class="text-center">
                  <span class="label label-info" style="font-size: 13px;">
                    <?php echo $product['quantity']; ?> units
                  </span>
                </td>
                <td class="text-center">
                  <strong>Rs. <?php echo number_format($product['buying_price'], 2); ?></strong>
                </td>
                <td class="text-center">
                  <strong style="font-size: 15px;">
                    Rs. <?php echo number_format($total_value, 2); ?>
                  </strong>
                </td>
                <td class="text-center">
                  <strong><?php echo date('Y-m-d', strtotime($product['expire_date'])); ?></strong>
                </td>
                <td class="text-center">
                  <span class="label label-info" style="font-size: 13px;">
                    <?php echo $product['days_until_expiry']; ?> days
                  </span>
                </td>
                <td>
                  <strong><?php echo $product['supplier_name'] ?? 'N/A'; ?></strong>
                  <br><small>ID: <?php echo $product['s_id']; ?></small>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Summary Report -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-list-alt"></span>
          <span>Expiry Analysis Summary</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <h4><strong>Financial Impact</strong></h4>
            <table class="table table-bordered">
              <tr>
                <td><strong>Total Expired Value:</strong></td>
                <td class="text-right" style="color: #d9534f; font-size: 16px;">
                  <strong>Rs. <?php echo number_format($expiry_data['financial']['expired_value'], 2); ?></strong>
                </td>
              </tr>
              <tr>
                <td><strong>Value Expiring in 7 Days:</strong></td>
                <td class="text-right" style="color: #f0ad4e; font-size: 16px;">
                  <strong>Rs. <?php echo number_format($expiry_data['financial']['7_days_value'], 2); ?></strong>
                </td>
              </tr>
              <tr>
                <td><strong>Value Expiring in 30 Days:</strong></td>
                <td class="text-right" style="color: #5bc0de; font-size: 16px;">
                  <strong>Rs. <?php echo number_format($expiry_data['financial']['30_days_value'], 2); ?></strong>
                </td>
              </tr>
              <tr style="background-color: #f5f5f5;">
                <td><strong>Total at Risk:</strong></td>
                <td class="text-right" style="color: #333; font-size: 18px;">
                  <strong>Rs. <?php echo number_format($expiry_data['financial']['total_at_risk'], 2); ?></strong>
                </td>
              </tr>
            </table>
          </div>
          
          <div class="col-md-6">
            <h4><strong>Recommended Actions</strong></h4>
            <div class="alert alert-danger">
              <strong><i class="glyphicon glyphicon-exclamation-sign"></i> Immediate Actions:</strong>
              <ul>
                <li>Process returns for <?php echo $expiry_data['stats']['expired']; ?> expired products</li>
                <li>Contact suppliers for return authorization</li>
                <li>Remove expired stock from shelves</li>
              </ul>
            </div>
            <div class="alert alert-warning">
              <strong><i class="glyphicon glyphicon-warning-sign"></i> This Week:</strong>
              <ul>
                <li>Plan promotions for <?php echo $expiry_data['stats']['7_days']; ?> products expiring soon</li>
                <li>Arrange supplier returns if applicable</li>
              </ul>
            </div>
            <div class="alert alert-info">
              <strong><i class="glyphicon glyphicon-info-sign"></i> This Month:</strong>
              <ul>
                <li>Monitor <?php echo $expiry_data['stats']['30_days']; ?> products expiring within 30 days</li>
                <li>Adjust inventory ordering</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php endif; ?>

<style>
.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
}

.panel-danger .panel-heading {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%) !important;
}

.panel-warning .panel-heading {
  background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%) !important;
}

.panel-info .panel-heading {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%) !important;
}

.panel-success .panel-heading {
  background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%) !important;
}

.panel-primary .panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  padding: 12px 40px;
  font-size: 16px;
  font-weight: bold;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.table thead th {
  font-weight: 600;
  border-bottom: 2px solid #ddd;
}

.table-hover tbody tr:hover {
  background-color: #f1f8ff !important;
  cursor: pointer;
}

@media print {
  .btn, .panel-heading .pull-right {
    display: none !important;
  }
}
</style>

<?php include_once('layouts/footer.php'); ?>

<?php
// Function to get comprehensive expiry analysis
function get_expiry_analysis() {
  global $db;
  
  $data = array(
    'expired' => array(),
    '7_days' => array(),
    '30_days' => array(),
    '90_days' => array(),
    'stats' => array(
      'expired' => 0,
      '7_days' => 0,
      '30_days' => 0,
      '90_days' => 0
    ),
    'financial' => array(
      'expired_value' => 0,
      '7_days_value' => 0,
      '30_days_value' => 0,
      'total_at_risk' => 0
    ),
    'category_labels' => array(),
    'category_counts' => array(),
    'timeline_labels' => array(),
    'timeline_days' => array()
  );
  
  // Get all products with expiry dates
  $sql  = "SELECT p.*, s.s_name AS supplier_name, s.contact_number, s.email AS supplier_email ";
  $sql .= "FROM product p ";
  $sql .= "LEFT JOIN supplier_info s ON p.s_id = s.s_id ";
  $sql .= "WHERE p.expire_date IS NOT NULL ";
  $sql .= "ORDER BY p.expire_date ASC";
  
  $result = $db->query($sql);
  $products = $db->while_loop($result);
  
  $today = date('Y-m-d');
  $category_data = array();
  
  foreach($products as $product) {
    $expiry_date = $product['expire_date'];
    $days_diff = (strtotime($expiry_date) - strtotime($today)) / (60 * 60 * 24);
    $product['days_until_expiry'] = floor($days_diff);
    
    // Calculate financial values
    $product_value = $product['quantity'] * $product['buying_price'];
    
    // Categorize products
    if($days_diff < 0) {
      // Expired
      $data['expired'][] = $product;
      $data['stats']['expired']++;
      $data['financial']['expired_value'] += $product_value;
    } elseif($days_diff <= 7) {
      // Expiring within 7 days
      $data['7_days'][] = $product;
      $data['stats']['7_days']++;
      $data['financial']['7_days_value'] += $product_value;
    } elseif($days_diff <= 30) {
      // Expiring within 30 days
      $data['30_days'][] = $product;
      $data['stats']['30_days']++;
      $data['financial']['30_days_value'] += $product_value;
    } elseif($days_diff <= 90) {
      // Expiring within 90 days
      $data['90_days'][] = $product;
      $data['stats']['90_days']++;
    }
    
    // Category data
    $category = $product['category_name'];
    if(!isset($category_data[$category])) {
      $category_data[$category] = 0;
    }
    if($days_diff <= 90) {
      $category_data[$category]++;
    }
    
    // Timeline data (for products expiring within 90 days)
    if($days_diff <= 90) {
      $data['timeline_labels'][] = substr($product['product_name'], 0, 20) . '...';
      $data['timeline_days'][] = $product['days_until_expiry'];
    }
  }
  
  // Calculate total at risk
  $data['financial']['total_at_risk'] = $data['financial']['expired_value'] + 
                                        $data['financial']['7_days_value'] + 
                                        $data['financial']['30_days_value'];
  
  // Prepare category chart data
  $data['category_labels'] = array_keys($category_data);
  $data['category_counts'] = array_values($category_data);
  
  return $data;
}
?>

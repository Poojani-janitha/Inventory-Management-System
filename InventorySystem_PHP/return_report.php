<?php
  $page_title = 'Return Reports';
  require_once('includes/load.php');
  page_require_level(1);
?>
<?php include_once('layouts/header.php'); ?>

<?php
  // Get all returns
  $all_returns = find_all_returns();
  
  // Calculate statistics
  $total_returns = count($all_returns);
  $total_quantity = 0;
  $total_return_value = 0;
  $product_counts = [];
  $supplier_counts = [];
  $category_counts = [];
  
  foreach($all_returns as $return) {
    $total_quantity += (int)$return['return_quantity'];
    $return_price = $return['return_quantity'] * $return['buying_price'];
    $total_return_value += $return_price;
    
    // Count by product
    if(!isset($product_counts[$return['p_id']])) {
      $product_counts[$return['p_id']] = [
        'name' => $return['product_name'],
        'count' => 0,
        'quantity' => 0,
        'total_value' => 0
      ];
    }
    $product_counts[$return['p_id']]['count']++;
    $product_counts[$return['p_id']]['quantity'] += (int)$return['return_quantity'];
    $product_counts[$return['p_id']]['total_value'] += $return_price;
    
    // Count by supplier
    if(!isset($supplier_counts[$return['s_id']])) {
      $supplier_counts[$return['s_id']] = [
        'name' => $return['supplier_name'] ?? $return['s_id'],
        'count' => 0,
        'quantity' => 0,
        'total_value' => 0
      ];
    }
    $supplier_counts[$return['s_id']]['count']++;
    $supplier_counts[$return['s_id']]['quantity'] += (int)$return['return_quantity'];
    $supplier_counts[$return['s_id']]['total_value'] += $return_price;
    
    // Count by category
    if(!empty($return['category_name'])) {
      if(!isset($category_counts[$return['category_name']])) {
        $category_counts[$return['category_name']] = [
          'count' => 0,
          'quantity' => 0,
          'total_value' => 0
        ];
      }
      $category_counts[$return['category_name']]['count']++;
      $category_counts[$return['category_name']]['quantity'] += (int)$return['return_quantity'];
      $category_counts[$return['category_name']]['total_value'] += $return_price;
    }
  }
  
  // Sort arrays
  arsort($product_counts);
  arsort($supplier_counts);
  arsort($category_counts);
?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-stats"></span>
          <span>Return Management Report</span>
        </strong>
        <div class="pull-right">
          <button onclick="window.print()" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-print"></span> Print Report
          </button>
          <a href="returns.php" class="btn btn-default btn-sm">
            <span class="glyphicon glyphicon-arrow-left"></span> Back to Returns
          </a>
        </div>
      </div>
      <div class="panel-body">
        
        <!-- Summary Statistics -->
        <div class="row">
          <div class="col-md-3">
            <div class="panel panel-primary text-center" style="padding: 20px;">
              <h2 style="margin: 0; color: #337ab7;"><?php echo $total_returns; ?></h2>
              <p style="margin: 5px 0 0 0; color: #777;">Total Returns</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-success text-center" style="padding: 20px;">
              <h2 style="margin: 0; color: #5cb85c;"><?php echo number_format($total_quantity); ?></h2>
              <p style="margin: 5px 0 0 0; color: #777;">Total Quantity</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-info text-center" style="padding: 20px;">
              <h2 style="margin: 0; color: #5bc0de;">Rs. <?php echo number_format($total_return_value, 2); ?></h2>
              <p style="margin: 5px 0 0 0; color: #777;">Total Return Value</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-warning text-center" style="padding: 20px;">
              <h2 style="margin: 0; color: #f0ad4e;"><?php echo number_format($total_returns > 0 ? $total_return_value / $total_returns : 0, 2); ?></h2>
              <p style="margin: 5px 0 0 0; color: #777;">Average Return Value</p>
            </div>
          </div>
        </div>
        
        <hr>
        
        <!-- Returns by Product -->
        <div class="row">
          <div class="col-md-12">
            <h3><i class="glyphicon glyphicon-list"></i> Returns by Product</h3>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Product ID</th>
                  <th>Product Name</th>
                  <th class="text-center">Number of Returns</th>
                  <th class="text-center">Total Quantity</th>
                  <th class="text-center">Total Return Value</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($product_counts)): ?>
                  <?php foreach(array_slice($product_counts, 0, 10) as $p_id => $data): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($p_id); ?></td>
                    <td><?php echo htmlspecialchars($data['name']); ?></td>
                    <td class="text-center"><span class="label label-primary"><?php echo $data['count']; ?></span></td>
                    <td class="text-center"><span class="label label-warning"><?php echo number_format($data['quantity']); ?> units</span></td>
                    <td class="text-center"><strong>Rs. <?php echo number_format($data['total_value'], 2); ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center">No data available</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        
        <hr>
        
        <!-- Returns by Supplier -->
        <div class="row">
          <div class="col-md-6">
            <h3><i class="glyphicon glyphicon-user"></i> Returns by Supplier</h3>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Supplier</th>
                  <th class="text-center">Count</th>
                  <th class="text-center">Total Value</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($supplier_counts)): ?>
                  <?php foreach(array_slice($supplier_counts, 0, 10) as $s_id => $data): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($data['name']); ?></td>
                    <td class="text-center"><span class="label label-info"><?php echo $data['count']; ?></span></td>
                    <td class="text-center"><strong>Rs. <?php echo number_format($data['total_value'], 2); ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center">No data available</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Returns by Category -->
          <div class="col-md-6">
            <h3><i class="glyphicon glyphicon-folder-open"></i> Returns by Category</h3>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Category</th>
                  <th class="text-center">Count</th>
                  <th class="text-center">Total Value</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($category_counts)): ?>
                  <?php foreach(array_slice($category_counts, 0, 10) as $category => $data): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($category); ?></td>
                    <td class="text-center"><span class="label label-default"><?php echo $data['count']; ?></span></td>
                    <td class="text-center"><strong>Rs. <?php echo number_format($data['total_value'], 2); ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center">No data available</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        
        <hr>
        
        <!-- Detailed Returns List -->
        <div class="row">
          <div class="col-md-12">
            <h3><i class="glyphicon glyphicon-list-alt"></i> Detailed Returns List</h3>
            <table class="table table-bordered table-striped" id="detailedTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Return ID</th>
                  <th>Product</th>
                  <th class="text-center">Qty</th>
                  <th class="text-center">Buying Price</th>
                  <th class="text-center">Return Price</th>
                  <th>Supplier</th>
                  <th>Category</th>
                  <th class="text-center">Date</th>
                </tr>
              </thead>
              <tbody>
                <?php $counter = 1; ?>
                <?php if($all_returns): ?>
                  <?php foreach($all_returns as $return): ?>
                  <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo (int)$return['return_id']; ?></td>
                    <td>
                      <strong><?php echo htmlspecialchars($return['product_name']); ?></strong>
                      <br><small class="text-muted"><?php echo htmlspecialchars($return['p_id']); ?></small>
                    </td>
                    <td class="text-center"><?php echo (int)$return['return_quantity']; ?></td>
                    <td class="text-center">Rs. <?php echo number_format($return['buying_price'], 2); ?></td>
                    <td class="text-center"><strong>Rs. <?php echo number_format($return['return_quantity'] * $return['buying_price'], 2); ?></strong></td>
                    <td><?php echo htmlspecialchars($return['supplier_name'] ?? $return['s_id']); ?></td>
                    <td><?php echo htmlspecialchars($return['category_name'] ?? 'N/A'); ?></td>
                    <td class="text-center"><?php echo date('d-M-Y', strtotime($return['return_date'])); ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="9" class="text-center">No returns found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  .panel-heading button,
  .panel-heading a {
    display: none !important;
  }
}

.panel-primary {
  border-color: #337ab7;
}

.panel-success {
  border-color: #5cb85c;
}

.panel-info {
  border-color: #5bc0de;
}

.panel-warning {
  border-color: #f0ad4e;
}

table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.label {
  font-size: 0.85em;
  padding: 0.3em 0.6em;
}

h3 {
  color: #667eea;
  margin-bottom: 20px;
}

hr {
  margin: 30px 0;
  border-color: #e0e0e0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Print functionality
  document.addEventListener('keydown', function(e) {
    if(e.ctrlKey && e.key === 'p') {
      e.preventDefault();
      window.print();
    }
  });
});
</script>

<?php include_once('layouts/footer.php'); ?>


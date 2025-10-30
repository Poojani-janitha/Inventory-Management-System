<?php
  $page_title = 'Purchase Report';
  require_once('../includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  if(!isset($msg)) $msg = $session->msg();
  $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
  $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
  
  // Handle year/month filtering
  if(isset($_GET['year']) && !empty($_GET['year']) && isset($_GET['month']) && !empty($_GET['month'])){
    $start_date = $_GET['year'] . '-' . $_GET['month'] . '-01';
    $end_date = date('Y-m-t', strtotime($start_date)); // Last day of the month
  }
  
  $purchases = find_all_purchase_orders($start_date, $end_date);
  
  // Calculate totals
  $total_amount = 0;
  $total_quantity = 0;
  foreach($purchases as $purchase){
    $total_amount += isset($purchase['total_amount']) ? $purchase['total_amount'] : 0;
    $total_quantity += isset($purchase['quantity']) ? $purchase['quantity'] : 0;
  }
?>
<?php include_once('../layouts/header.php'); ?>
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
          <span class="glyphicon glyphicon-shopping-cart"></span>
          <span>Purchase Order Report</span>
        </strong>
        <div class="pull-right">
          <form method="get" action="purchase_report.php" class="form-inline">
            <div class="form-group">
              <select name="year" class="form-control" style="width: 100px;">
                <option value="">Select Year</option>
                <?php 
                  $current_year = date('Y');
                  for($i = $current_year; $i >= $current_year - 5; $i--): 
                    $selected = (isset($_GET['year']) && $_GET['year'] == $i) ? 'selected' : '';
                ?>
                  <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="form-group">
              <select name="month" class="form-control" style="width: 120px;">
                <option value="">Select Month</option>
                <?php 
                  $months = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', 
                            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', 
                            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                  foreach($months as $num => $name): 
                    $selected = (isset($_GET['month']) && $_GET['month'] == $num) ? 'selected' : '';
                ?>
                  <option value="<?php echo $num; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="purchase_report.php" class="btn btn-default">Reset</a>
          </form>
        </div>
      </div>
      <div class="panel-body">
        <?php if(empty($purchases)): ?>
          <div class="alert alert-info">
            <p>No purchase orders found<?php if(isset($_GET['year']) && isset($_GET['month']) && $_GET['year'] !== '' && $_GET['month'] !== ''): ?> for the selected month<?php endif; ?>.</p>
          </div>
        <?php else: ?>
          <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Order ID</th>
              <th>Supplier</th>
              <th>Product Name</th>
              <th>Category</th>
              <th class="text-center" style="width: 10%;">Quantity</th>
              <th class="text-center" style="width: 10%;">Unit Price</th>
              <th class="text-center" style="width: 10%;">Total Amount</th>
              <th class="text-center" style="width: 10%;">Status</th>
              <th class="text-center" style="width: 15%;">Order Date</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; ?>
            <?php foreach ($purchases as $purchase):?>
            <tr>
              <td class="text-center"><?php echo $count++; ?></td>
              <td><?php echo remove_junk($purchase['o_id']); ?></td>
              <td><?php echo remove_junk($purchase['supplier_name']); ?> (<?php echo remove_junk($purchase['s_id']); ?>)</td>
              <td><?php echo remove_junk($purchase['product_name']); ?></td>
              <td><?php echo remove_junk($purchase['category_name']); ?></td>
              <td class="text-center"><?php echo (int)$purchase['quantity']; ?></td>
              <td class="text-center">Rs. <?php echo number_format($purchase['price'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format(isset($purchase['total_amount']) ? $purchase['total_amount'] : ($purchase['quantity'] * $purchase['price']), 2); ?></td>
              <td class="text-center"><span class="label label-<?php echo $purchase['status'] == 'accepted' ? 'success' : ($purchase['status'] == 'pending' ? 'warning' : 'default'); ?>"><?php echo ucfirst($purchase['status']); ?></span></td>
              <td class="text-center"><?php echo date("Y-m-d H:i", strtotime($purchase['order_date'])); ?></td>
            </tr>
            <?php endforeach;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="text-right"><strong>Total:</strong></td>
              <td class="text-center"><strong><?php echo $total_quantity; ?></strong></td>
              <td colspan="2" class="text-center"><strong>Rs. <?php echo number_format($total_amount, 2); ?></strong></td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('../layouts/footer.php'); ?>


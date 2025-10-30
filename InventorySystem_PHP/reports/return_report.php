<?php
  $page_title = 'Purchase Return Report';
  require_once('includes/load.php');
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
  
  $returns = find_all_return_details($start_date, $end_date);
  
  // Calculate totals
  $total_amount = 0;
  $total_quantity = 0;
  foreach($returns as $return){
    $total_amount += isset($return['total_return_amount']) ? $return['total_return_amount'] : ($return['buying_price'] * $return['return_quantity']);
    $total_quantity += $return['return_quantity'];
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
          <span class="glyphicon glyphicon-arrow-left"></span>
          <span>Purchase Return Report</span>
        </strong>
        <div class="pull-right">
          <form method="get" action="return_report.php" class="form-inline">
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
            <a href="return_report.php" class="btn btn-default">Reset</a>
          </form>
        </div>
      </div>
      <div class="panel-body">
        <?php if(empty($returns)): ?>
          <div class="alert alert-info">
            <p>No return records found<?php if(isset($_GET['year']) && isset($_GET['month']) && $_GET['year'] !== '' && $_GET['month'] !== ''): ?> for the selected month<?php endif; ?>.</p>
          </div>
        <?php else: ?>
          <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Return ID</th>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Supplier</th>
              <th class="text-center" style="width: 10%;">Quantity</th>
              <th class="text-center" style="width: 10%;">Buying Price</th>
              <th class="text-center" style="width: 10%;">Total Amount</th>
              <th class="text-center" style="width: 15%;">Return Date</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; ?>
            <?php foreach ($returns as $return):?>
            <tr>
              <td class="text-center"><?php echo $count++; ?></td>
              <td><?php echo remove_junk($return['return_id']); ?></td>
              <td><?php echo remove_junk($return['p_id']); ?></td>
              <td><?php echo remove_junk($return['product_name']); ?></td>
              <td><?php echo remove_junk($return['supplier_name']); ?> (<?php echo remove_junk($return['s_id']); ?>)</td>
              <td class="text-center"><?php echo (int)$return['return_quantity']; ?></td>
              <td class="text-center">Rs. <?php echo number_format($return['buying_price'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format(isset($return['total_return_amount']) ? $return['total_return_amount'] : ($return['buying_price'] * $return['return_quantity']), 2); ?></td>
              <td class="text-center"><?php echo date("Y-m-d H:i", strtotime($return['return_date'])); ?></td>
            </tr>
            <?php endforeach;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="text-right"><strong>Total:</strong></td>
              <td class="text-center"><strong><?php echo $total_quantity; ?></strong></td>
              <td></td>
              <td class="text-center"><strong>Rs. <?php echo number_format($total_amount, 2); ?></strong></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>


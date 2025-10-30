<?php
  $page_title = 'Profit Report';
  require_once('../includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  if(!isset($msg)) $msg = $session->msg();
  
  // If year+month provided, show that month's profit; otherwise show current month
  $selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
  $selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
  $profit_data = find_profit_report_month($selected_year, $selected_month);
  
  // Extract data
  $sales_revenue = $profit_data['sales_revenue'];
  $cost_of_sales = $profit_data['cost_of_sales'];
  $discounts = $profit_data['discounts'];
  $gross_profit = $profit_data['gross_profit'];
  $profit_margin = $profit_data['profit_margin'];
  $potential_revenue = $profit_data['potential_revenue'];
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
          <span class="glyphicon glyphicon-stats"></span>
          <span>Profit Report</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="pull-right" style="margin-bottom:15px;">
          <form method="get" action="profit_report.php" class="form-inline">
            <div class="form-group">
              <select name="year" class="form-control" style="width: 100px;">
                <?php 
                  $current_year = date('Y');
                  for($i = $current_year; $i >= $current_year - 5; $i--): 
                    $selected = ($selected_year == $i) ? 'selected' : '';
                ?>
                  <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="form-group">
              <select name="month" class="form-control" style="width: 120px;">
                <?php 
                  $months = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', 
                            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', 
                            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                  foreach($months as $num => $name): 
                    $selected = ($selected_month == (int)$num) ? 'selected' : '';
                ?>
                  <option value="<?php echo $num; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Apply</button>
            <button type="button" class="btn btn-default" onclick="window.print()">
              <span class="glyphicon glyphicon-print"></span> Print
            </button>
          </form>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-primary">
              <div class="panel-body text-center">
                <h4>Total Sales Revenue</h4>
                <h2 class="text-primary">Rs. <?php echo number_format($sales_revenue, 2); ?></h2>
                <p class="text-muted">Total revenue from all sales</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-danger">
              <div class="panel-body text-center">
                <h4>Total Cost of Sales</h4>
                <h2 class="text-danger">Rs. <?php echo number_format($cost_of_sales, 2); ?></h2>
                <p class="text-muted">Based on buying price × quantity sold</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panel panel-success">
              <div class="panel-body text-center">
                <h4>Gross Profit</h4>
                <h2 class="<?php echo $gross_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                  Rs. <?php echo number_format($gross_profit, 2); ?>
                </h2>
                <p class="text-muted">Profit Margin: <?php echo number_format($profit_margin, 2); ?>%</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <h4>Profit Calculation Details</h4>
            <table class="table table-bordered">
              <tr>
                <td><strong>Total Sales Revenue</strong></td>
                <td class="text-right"><strong>Rs. <?php echo number_format($sales_revenue, 2); ?></strong></td>
              </tr>
              <tr>
                <td>Less: Cost of Sales (Buying Price × Quantity)</td>
                <td class="text-right text-danger">- Rs. <?php echo number_format($cost_of_sales, 2); ?></td>
              </tr>
              <tr>
                <td>Less: Discounts</td>
                <td class="text-right text-danger">- Rs. <?php echo number_format($discounts, 2); ?></td>
              </tr>
              <tr class="success">
                <td><strong>Gross Profit</strong></td>
                <td class="text-right"><strong class="<?php echo $gross_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                  Rs. <?php echo number_format($gross_profit, 2); ?>
                </strong></td>
              </tr>
              <tr class="info">
                <td><strong>Profit Margin</strong></td>
                <td class="text-right"><strong><?php echo number_format($profit_margin, 2); ?>%</strong></td>
              </tr>
            </table>
          </div>
          
          <div class="col-md-6">
            <h4>Additional Information</h4>
            <table class="table table-bordered">
              <tr>
                <td>Potential Revenue (if all sold at selling price)</td>
                <td class="text-right">Rs. <?php echo number_format($potential_revenue, 2); ?></td>
              </tr>
              <tr>
                <td>Total Discounts Given</td>
                <td class="text-right text-warning">Rs. <?php echo number_format($discounts, 2); ?></td>
              </tr>
              <tr>
                <td>Average Profit per Sale</td>
                <td class="text-right">
                  <?php 
                    $total_quantity = 0;
                    $sales_count = find_by_sql("SELECT COUNT(*) as count FROM sales");
                    $count = isset($sales_count[0]['count']) ? $sales_count[0]['count'] : 1;
                    $avg_profit = $count > 0 ? $gross_profit / $count : 0;
                  ?>
                  <strong>Rs. <?php echo number_format($avg_profit, 2); ?></strong>
                </td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-info">
              <h4><i class="glyphicon glyphicon-info-sign"></i> About This Report</h4>
              <p>This profit report calculates profit based on:</p>
              <ul>
                <li><strong>Sales Revenue:</strong> Total revenue from all sales transactions</li>
                <li><strong>Cost of Sales:</strong> Calculated using buying price from product table × quantity sold from sales table</li>
                <li><strong>Gross Profit:</strong> Sales Revenue - Cost of Sales - Discounts</li>
                <li><strong>Profit Margin:</strong> (Gross Profit / Sales Revenue) × 100%</li>
              </ul>
              <p><strong>Note:</strong> This report shows all-time profit data from product and sales tables.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('../layouts/footer.php'); ?>

<style>
@media print {
  body { 
    margin: 0; 
    padding: 0; 
    font-size: 11pt;
  }
  .sidebar, .header, .panel-heading .pull-right, .panel-body .pull-right, .btn, .alert {
    display: none !important;
  }
  .page {
    margin: 0;
    padding: 10px;
  }
  .panel {
    border: none;
    box-shadow: none;
    margin-bottom: 0;
  }
  .panel-body {
    padding: 10px 0;
  }
  .panel-heading {
    border-bottom: 2px solid #333 !important;
    page-break-after: avoid;
  }
  table {
    font-size: 10pt;
  }
  table thead {
    display: table-header-group;
  }
  table tr {
    page-break-inside: avoid;
  }
  .no-print {
    display: none !important;
  }
  .row .col-md-4, .row .col-md-6 {
    width: 48%;
    float: left;
    margin-right: 2%;
    margin-bottom: 10px;
  }
}
</style>


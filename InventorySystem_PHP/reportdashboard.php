<?php
  $page_title = 'Reports';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  if(!isset($msg)) $msg = $session->msg();
?>
<?php 


include_once('layouts/header.php'); ?>
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
          <span class="glyphicon glyphicon-file"></span>
          <span>Reports</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <i class="glyphicon glyphicon-shopping-cart"></i> Purchase Reports
                </h4>
              </div>
              <div class="panel-body">
                <p><a href="purchase_report.php" class="btn btn-primary">View Purchase Order Report</a></p>
                <p class="text-muted">View all purchase orders with supplier details, quantities, and totals.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <i class="glyphicon glyphicon-ok-circle"></i> Sales Reports
                </h4>
              </div>
              <div class="panel-body">
                <p><a href="sales_report.php" class="btn btn-success">View Sales Report</a></p>
                <p class="text-muted">View all sales transactions with customer details and invoice information.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <i class="glyphicon glyphicon-arrow-left"></i> Return Reports
                </h4>
              </div>
              <div class="panel-body">
                <p><a href="return_report.php" class="btn btn-warning">View Purchase Return Report</a></p>
                <p class="text-muted">View all returned products with supplier details and return values.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <i class="glyphicon glyphicon-list"></i> Stock Reports
                </h4>
              </div>
              <div class="panel-body">
                <p><a href="stock_summary.php" class="btn btn-info">View Stock Summary</a></p>
                <p class="text-muted">View current stock levels for all products with category and supplier information.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <i class="glyphicon glyphicon-usd"></i> Inventory Valuation
                </h4>
              </div>
              <div class="panel-body">
                <p><a href="inventory_valuation.php" class="btn btn-default">View Inventory Valuation</a></p>
                <p class="text-muted">View inventory valuation based on buying price and potential sales value.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <i class="glyphicon glyphicon-stats"></i> Profit Report
                </h4>
              </div>
              <div class="panel-body">
                <p><a href="profit_report.php" class="btn btn-danger">View Profit Report</a></p>
                <p class="text-muted">View profit analysis combining purchases, sales, and returns.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>


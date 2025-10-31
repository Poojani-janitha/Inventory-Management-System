<?php
  $page_title = 'Inventory Valuation Report';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  if(!isset($msg)) $msg = $session->msg();
  $products = find_inventory_valuation();
  
  // Calculate totals
  $total_stock_value = 0;
  $total_sales_value = 0;
  $total_potential_profit = 0;
  foreach($products as $product){
    $total_stock_value += $product['stock_value'];
    $total_sales_value += $product['potential_sales_value'];
    $total_potential_profit += $product['potential_profit'];
  }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-primary">
      <div class="panel-body">
        <h4>Total Stock Value</h4>
        <h2>Rs. <?php echo number_format($total_stock_value, 2); ?></h2>
        <small>(Based on Buying Price)</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-success">
      <div class="panel-body">
        <h4>Potential Sales Value</h4>
        <h2>Rs. <?php echo number_format($total_sales_value, 2); ?></h2>
        <small>(Based on Selling Price)</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-info">
      <div class="panel-body">
        <h4>Potential Profit</h4>
        <h2>Rs. <?php echo number_format($total_potential_profit, 2); ?></h2>
        <small>(If all items sold)</small>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-usd"></span>
          <span>Inventory Valuation Report</span>
        </strong>
      </div>
      <div class="panel-body">
        <?php if(empty($products)): ?>
          <div class="alert alert-info">
            <p>No products found in inventory.</p>
          </div>
        <?php else: ?>
          <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Supplier</th>
              <th class="text-center" style="width: 8%;">Quantity</th>
              <th class="text-center" style="width: 10%;">Buying Price</th>
              <th class="text-center" style="width: 10%;">Selling Price</th>
              <th class="text-center" style="width: 12%;">Stock Value</th>
              <th class="text-center" style="width: 12%;">Sales Value</th>
              <th class="text-center" style="width: 12%;">Potential Profit</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; ?>
            <?php foreach ($products as $product):?>
            <tr>
              <td class="text-center"><?php echo $count++; ?></td>
              <td><?php echo remove_junk($product['p_id']); ?></td>
              <td><?php echo remove_junk($product['product_name']); ?></td>
              <td><?php echo remove_junk($product['category_name']); ?></td>
              <td><?php echo remove_junk($product['supplier_name']); ?></td>
              <td class="text-center"><?php echo (int)$product['quantity']; ?></td>
              <td class="text-center">Rs. <?php echo number_format($product['buying_price'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format($product['selling_price'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format($product['stock_value'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format($product['potential_sales_value'], 2); ?></td>
              <td class="text-center text-success"><strong>Rs. <?php echo number_format($product['potential_profit'], 2); ?></strong></td>
            </tr>
            <?php endforeach;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="8" class="text-right"><strong>Total:</strong></td>
              <td class="text-center"><strong>Rs. <?php echo number_format($total_stock_value, 2); ?></strong></td>
              <td class="text-center"><strong>Rs. <?php echo number_format($total_sales_value, 2); ?></strong></td>
              <td class="text-center"><strong class="text-success">Rs. <?php echo number_format($total_potential_profit, 2); ?></strong></td>
            </tr>
          </tfoot>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>


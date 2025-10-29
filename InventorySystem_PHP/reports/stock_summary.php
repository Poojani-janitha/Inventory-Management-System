<?php
  $page_title = 'Stock Summary Report';
  require_once('../includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  if(!isset($msg)) $msg = $session->msg();
  $products = find_stock_summary();
  
  // Calculate totals
  $total_items = 0;
  $total_value = 0;
  $low_stock = 0;
  foreach($products as $product){
    $total_items += $product['quantity'];
    $total_value += $product['quantity'] * $product['buying_price'];
    if($product['quantity'] < 10){
      $low_stock++;
    }
  }
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-3">
    <div class="panel panel-primary">
      <div class="panel-body">
        <h4>Total Products</h4>
        <h2><?php echo count($products); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-info">
      <div class="panel-body">
        <h4>Total Items</h4>
        <h2><?php echo $total_items; ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-success">
      <div class="panel-body">
        <h4>Inventory Value</h4>
        <h2>Rs. <?php echo number_format($total_value, 2); ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-warning">
      <div class="panel-body">
        <h4>Low Stock Items</h4>
        <h2><?php echo $low_stock; ?></h2>
        <small>(Quantity < 10)</small>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-list"></span>
          <span>Stock Summary Report</span>
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
              <th class="text-center" style="width: 10%;">Quantity</th>
              <th class="text-center" style="width: 10%;">Buying Price</th>
              <th class="text-center" style="width: 10%;">Selling Price</th>
              <th class="text-center" style="width: 10%;">Stock Value</th>
              <th class="text-center" style="width: 12%;">Expire Date</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; ?>
            <?php foreach ($products as $product):?>
            <tr class="<?php echo $product['quantity'] < 10 ? 'danger' : ''; ?>">
              <td class="text-center"><?php echo $count++; ?></td>
              <td><?php echo remove_junk($product['p_id']); ?></td>
              <td><?php echo remove_junk($product['product_name']); ?></td>
              <td><?php echo remove_junk($product['category_name']); ?></td>
              <td><?php echo remove_junk($product['supplier_name']); ?></td>
              <td class="text-center <?php echo $product['quantity'] < 10 ? 'text-danger' : ''; ?>">
                <strong><?php echo (int)$product['quantity']; ?></strong>
                <?php if($product['quantity'] < 10): ?>
                  <span class="glyphicon glyphicon-exclamation-sign" title="Low Stock"></span>
                <?php endif; ?>
              </td>
              <td class="text-center">Rs. <?php echo number_format($product['buying_price'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format($product['selling_price'], 2); ?></td>
              <td class="text-center">Rs. <?php echo number_format($product['quantity'] * $product['buying_price'], 2); ?></td>
              <td class="text-center"><?php echo $product['expire_date'] ? date("Y-m-d", strtotime($product['expire_date'])) : 'N/A'; ?></td>
            </tr>
            <?php endforeach;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="text-right"><strong>Total:</strong></td>
              <td class="text-center"><strong><?php echo $total_items; ?></strong></td>
              <td colspan="2"></td>
              <td class="text-center"><strong>Rs. <?php echo number_format($total_value, 2); ?></strong></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('../layouts/footer.php'); ?>


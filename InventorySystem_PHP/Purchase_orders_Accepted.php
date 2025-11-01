<?php
  $page_title = 'All Purchase Orders';
  require_once('includes/load.php');
  // Only users with level 2 or above can view
  page_require_level(2);

  // Fetch all purchase orders from database
  $purchase_orders = find_all('purchase_order');
  //extra add prabashi 
  $msg = $session->msg();
?>
<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/purchase_orders.css">
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
          <span class="glyphicon glyphicon-th"></span>
          <span>All Purchase Orders</span>
        </strong>
        <!-- <a href="add_product.php" class="btn btn-info pull-right">Add New</a> -->
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center">Order ID</th>
              <th class="text-center">Supplier ID</th>
              <th class="text-center">Product Name</th>
              <th class="text-center">Category</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Price (Rs)</th>
              <th class="text-center">Order Date</th>
              <th class="text-center">Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($purchase_orders as $order): ?>
              <tr>
                <td class="text-center"><?php echo remove_junk($order['o_id']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['s_id']); ?></td>
                <td><?php echo remove_junk($order['product_name']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['category_name']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['quantity']); ?></td>
                <td class="text-center"><?php echo number_format($order['price'], 2); ?></td>
                <td class="text-center"><?php echo read_date($order['order_date']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['status']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                  <?php if (strtolower(trim($order['status'])) == 'approved'): ?>
                         <a href="add_product.php?o_id=<?php echo $order['o_id'];?>" class="btn btn-info btn-xs" data-toggle="tooltip" title="Add Product to Inventory">
                           <i class="glyphicon glyphicon-plus"></i> Add
                         </a>  
                    <?php else: ?>
                      <!-- Show alert if status is Pending -->
                      <button class="btn btn-xs btn-secondary" 
                              onclick="alert('Cannot add — order is still pending.')" 
                              data-toggle="tooltip" 
                              title="Pending Order">
                        <i class="glyphicon glyphicon-ban-circle"></i> Add
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once 'layouts/footer.php'; ?>

<?php
  $page_title = 'All Products';
  require_once('includes/load.php');
  page_require_level(2);
  $products = find_all('product');
  
  // Check for expiring products (within 1 month)
  $expiring_products = array();
  $low_stock_products = array();
  
  foreach($products as $product) {
    // Check expiry date (within 1 month)
    if(!empty($product['expire_date'])) {
      $expire_date = strtotime($product['expire_date']);
      $one_month_from_now = strtotime('+1 month');
      
      if($expire_date <= $one_month_from_now) {
        $expiring_products[] = $product;
      }
    }
    
    // Check low stock (below 50% of original stock)
    // We'll use a dynamic approach based on current quantity
    $current_quantity = (int)$product['quantity'];
    
    // If quantity is very low (less than 50), consider it low stock
    // You can adjust this threshold as needed
    if($current_quantity < 50) {
      $low_stock_products[] = $product;
    }
  }
?>
<?php include_once('layouts/header.php'); ?>

<!-- Alert Modals -->
<?php if(!empty($expiring_products) || !empty($low_stock_products)): ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    
    <!-- Expiring Products Alert -->
    <?php if(!empty($expiring_products)): ?>
    <div class="alert alert-warning alert-dismissible fade in" role="alert" id="expiringAlert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4><i class="glyphicon glyphicon-warning-sign"></i> Products Expiring Soon!</h4>
      <p><strong>The following products are expiring within 1 month:</strong></p>
      <ul>
        <?php foreach($expiring_products as $product): ?>
        <li>
          <strong><?php echo remove_junk($product['product_name']); ?></strong> 
          (ID: <?php echo remove_junk($product['p_id']); ?>) 
          - Expires: <?php echo date('Y-m-d', strtotime($product['expire_date'])); ?>
          <span class="label label-warning"><?php echo $product['quantity']; ?> units</span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    
    <!-- Low Stock Products Alert -->
    <?php if(!empty($low_stock_products)): ?>
    <div class="alert alert-danger alert-dismissible fade in" role="alert" id="lowStockAlert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4><i class="glyphicon glyphicon-exclamation-sign"></i> Low Stock Alert!</h4>
      <p><strong>The following products are running low on stock (below 50% of original):</strong></p>
      <ul>
        <?php foreach($low_stock_products as $product): ?>
        <li>
          <strong><?php echo remove_junk($product['product_name']); ?></strong> 
          (ID: <?php echo remove_junk($product['p_id']); ?>) 
          - Current Stock: <span class="label label-danger"><?php echo $product['quantity']; ?> units</span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php else: ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<?php endif; ?>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All  Available  Products</span>
        </strong>
        <!-- <a href="add_product.php" class="btn btn-info pull-right">Add New</a> -->
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center">Product ID</th>
              <th class="text-center">Product Name</th>
              <th class="text-center">Category</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Buying Price (Rs)</th>
              <th class="text-center">Selling Price (Rs)</th>
              <th class="text-center">Added Date</th>
              <th class="text-center">Expire Date</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td class="text-center"><?php echo remove_junk($product['p_id']); ?></td>
                <td><?php echo remove_junk($product['product_name']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['category_name']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"><?php echo number_format($product['buying_price'], 2); ?></td>
                <td class="text-center"><?php echo number_format($product['selling_price'], 2); ?></td>
                <td class="text-center"><?php echo read_date($product['recorded_date']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['expire_date']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <!-- <a href="edit_product.php?id=<?php echo (int)$product['p_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit"> -->
                    <a href="edit_product.php?id=<?php echo urlencode($product['p_id']); ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
  
                    <i class="glyphicon glyphicon-edit"></i>
                    <a href="delete_product.php?id=<?php echo urlencode($product['p_id']); ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this product?\n\nProduct: <?php echo addslashes($product['product_name']); ?>\nID: <?php echo $product['p_id']; ?>');">
                      <i class="glyphicon glyphicon-trash"></i>
                    </a>
                    
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

<?php include_once('layouts/footer.php'); ?>

<script>
$(document).ready(function() {
    // Auto-show alerts on page load
    <?php if(!empty($expiring_products)): ?>
    setTimeout(function() {
        $('#expiringAlert').addClass('show');
    }, 500);
    <?php endif; ?>
    
    <?php if(!empty($low_stock_products)): ?>
    setTimeout(function() {
        $('#lowStockAlert').addClass('show');
    }, 1000);
    <?php endif; ?>
    
    // Add click handlers for alert actions
    $('.alert').on('click', function() {
        $(this).fadeOut();
    });
    
    // Auto-hide alerts after 10 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 10000);
});
</script>

<style>
.alert {
    margin-bottom: 20px;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-warning {
    background-color: #fcf8e3;
    border-color: #faebcc;
    color: #8a6d3b;
}

.alert-danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
}

.alert h4 {
    margin-top: 0;
    font-weight: bold;
}

.alert ul {
    margin-bottom: 0;
}

.alert li {
    margin-bottom: 5px;
}

.label {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 3px;
}

.label-warning {
    background-color: #f0ad4e;
}

.label-danger {
    background-color: #d9534f;
}
</style>

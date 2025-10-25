<?php
  $page_title = 'All Products';
  require_once('includes/load.php');
  page_require_level(2);
  $products = find_all('product');
?>
<?php include_once('layouts/header.php'); ?>

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
                    <a href="edit_product.php?id=<?php echo (int)$product['p_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <i class="glyphicon glyphicon-edit"></i>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['p_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete">
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

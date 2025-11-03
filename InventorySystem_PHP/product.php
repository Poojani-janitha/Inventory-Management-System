<?php
  $page_title = 'All Products';
  require_once('includes/load.php');
  page_require_level(2);
  $products = find_all('product');

  //extra add prabashi 
  $msg = $session->msg();
  
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
    if($current_quantity <= 30) {
      $low_stock_products[] = $product;
    }
  }
?>
<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/product.css">

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

/* Compact Delete Modal Styles */
.delete-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(5px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.delete-modal-backdrop.active {
    display: flex;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.delete-modal {
    background: white;
    border-radius: 16px;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.delete-modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    padding: 20px;
    text-align: center;
    position: relative;
}

.delete-icon-wrapper {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.delete-icon-wrapper i {
    font-size: 28px;
    color: white;
}

.delete-modal-header h3 {
    color: white;
    margin: 0;
    font-size: 20px;
    font-weight: 700;
}

.delete-modal-body {
    padding: 25px 20px;
    text-align: center;
}

.warning-message {
    color: #495057;
    font-size: 14px;
    margin-bottom: 18px;
    line-height: 1.5;
}

.product-details-box {
    background: #f8f9fa;
    border-left: 3px solid #dc3545;
    padding: 15px;
    border-radius: 8px;
    margin: 18px 0;
    text-align: left;
}

.product-detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.product-detail-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.product-detail-label {
    color: #6c757d;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-detail-value {
    color: #212529;
    font-weight: 700;
    font-size: 13px;
}

.delete-modal-footer {
    display: flex;
    gap: 12px;
    padding: 0 20px 20px;
}

.modal-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.modal-btn-delete {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.modal-btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
}

.modal-btn-cancel {
    background: #f8f9fa;
    color: #495057;
    border: 2px solid #dee2e6;
}

.modal-btn-cancel:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.note-text {
    color: #856404;
    background: #fff3cd;
    padding: 10px;
    border-radius: 6px;
    font-size: 11px;
    margin-top: 15px;
    border: 1px solid #ffeaa7;
    line-height: 1.4;
}

.note-text i {
    margin-right: 4px;
}
</style>

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
      <p><strong>The following products have 30 units or less in stock:</strong></p>
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
          <span>All Available Products</span>
        </strong>
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
                    <a href="edit_product.php?id=<?php echo urlencode($product['p_id']); ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <i class="glyphicon glyphicon-edit"></i>
                    </a>
                    
                    <button type="button" 
                            class="btn btn-xs btn-danger delete-product-btn" 
                            data-toggle="tooltip" 
                            title="Delete"
                            data-product-id="<?php echo htmlspecialchars($product['p_id']); ?>"
                            data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                            data-product-category="<?php echo htmlspecialchars($product['category_name']); ?>"
                            data-product-quantity="<?php echo htmlspecialchars($product['quantity']); ?>">
                      <i class="glyphicon glyphicon-trash"></i>
                    </button>
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

<!-- Compact Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal-backdrop">
  <div class="delete-modal">
    <div class="delete-modal-header">
      <div class="delete-icon-wrapper">
        <i class="glyphicon glyphicon-trash"></i>
      </div>
      <h3>Delete Product</h3>
    </div>
    
    <div class="delete-modal-body">
      <p class="warning-message">
        Are you sure you want to permanently delete this product? This action cannot be undone.
      </p>
      
      <div class="product-details-box">
        <div class="product-detail-item">
          <span class="product-detail-label">Product Name:</span>
          <span class="product-detail-value" id="modal-product-name">-</span>
        </div>
        <div class="product-detail-item">
          <span class="product-detail-label">Product ID:</span>
          <span class="product-detail-value" id="modal-product-id">-</span>
        </div>
        <div class="product-detail-item">
          <span class="product-detail-label">Category:</span>
          <span class="product-detail-value" id="modal-product-category">-</span>
        </div>
        <div class="product-detail-item">
          <span class="product-detail-label">Current Stock:</span>
          <span class="product-detail-value" id="modal-product-quantity">-</span>
        </div>
      </div>
      
      <div class="note-text">
        <i class="glyphicon glyphicon-info-sign"></i>
        <strong>Note:</strong> All records will be permanently removed.
      </div>
    </div>
    
    <div class="delete-modal-footer">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
        <i class="glyphicon glyphicon-remove"></i>
        Cancel
      </button>
      <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
        <i class="glyphicon glyphicon-ok"></i>
        Yes, Delete
      </button>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
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
    
    // Delete product button click
    let deleteProductId = '';
    
    $('.delete-product-btn').on('click', function() {
        deleteProductId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const productCategory = $(this).data('product-category');
        const productQuantity = $(this).data('product-quantity');
        
        // Update modal content
        $('#modal-product-name').text(productName);
        $('#modal-product-id').text(deleteProductId);
        $('#modal-product-category').text(productCategory);
        $('#modal-product-quantity').text(productQuantity + ' units');
        
        // Show modal
        openDeleteModal();
    });
    
    // Confirm delete button
    $('#confirmDeleteBtn').on('click', function() {
        if(deleteProductId) {
            // Redirect to delete page
            window.location.href = 'delete_product.php?id=' + encodeURIComponent(deleteProductId);
        }
    });
    
    // Close modal when clicking outside
    $('#deleteModal').on('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    // Close modal with ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#deleteModal').hasClass('active')) {
            closeDeleteModal();
        }
    });
});

function openDeleteModal() {
    $('#deleteModal').addClass('active');
    $('body').css('overflow', 'hidden'); // Prevent background scrolling
}

function closeDeleteModal() {
    $('#deleteModal').removeClass('active');
    $('body').css('overflow', 'auto'); // Re-enable scrolling
}
</script>
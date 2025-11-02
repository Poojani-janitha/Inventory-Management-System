<?php
  $page_title = 'Edit Product';
  require_once('includes/load.php');
  page_require_level(2);
  
  $msg = $session->msg();

  // Get product by ID
  $p_id = $_GET['id'] ?? '';
  $product = find_by_id('product', $p_id);

  if (!$product) {
    $session->msg("d", "Missing or invalid product ID.");
    redirect('product.php');
  }

  $all_categories = find_all('categories');
  $all_suppliers = find_all('supplier_info');

  // ===== When Update button clicked =====
  if (isset($_POST['update_product'])) {
    $req_fields = array('product_name', 'product_categorie', 'product_supplier', 'product_quantity', 'buying_price', 'selling_price');
    validate_fields($req_fields);

    if (empty($errors)) {
      $p_name     = remove_junk($db->escape($_POST['product_name']));
      $p_cat      = remove_junk($db->escape($_POST['product_categorie']));
      $p_supplier = remove_junk($db->escape($_POST['product_supplier']));
      $p_qty      = remove_junk($db->escape($_POST['product_quantity']));
      $p_buy      = remove_junk($db->escape($_POST['buying_price']));
      $p_sale     = remove_junk($db->escape($_POST['selling_price']));
      $p_expire   = remove_junk($db->escape($_POST['expire_date']));

      $query  = "UPDATE product SET ";
      $query .= "product_name='{$p_name}', ";
      $query .= "category_name='{$p_cat}', ";
      $query .= "s_id='{$p_supplier}', ";
      $query .= "quantity='{$p_qty}', ";
      $query .= "buying_price='{$p_buy}', ";
      $query .= "selling_price='{$p_sale}', ";
      $query .= "expire_date='{$p_expire}' ";
      $query .= "WHERE p_id='{$p_id}'";

      if ($db->query($query)) {
        $session->msg("s", "Product updated successfully!");
        // Redirect with success flag to clear fields
        redirect("edit_product.php?id={$p_id}&success=1", false);
      } else {
        $session->msg("d", "Failed to update product!");
        redirect("edit_product.php?id={$p_id}", false);
      }
    } else {
      $session->msg("d", $errors);
      redirect("edit_product.php?id={$p_id}", false);
    }
  }
  
  // Check if we should clear fields after successful update
  $clear_fields = isset($_GET['success']) && $_GET['success'] == '1';
?>

<?php include_once('layouts/header.php'); ?>

<style>
.edit-product-container {
  max-width: 900px;
  margin: 30px auto;
}

.modern-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.modern-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.card-header-modern {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 25px 30px;
  color: white;
}

.card-header-modern h3 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 12px;
}

.card-header-modern .glyphicon {
  font-size: 26px;
}

.card-body-modern {
  padding: 35px 30px;
}

.form-group-modern {
  margin-bottom: 25px;
  position: relative;
}

.form-label-modern {
  display: block;
  margin-bottom: 8px;
  color: #495057;
  font-weight: 600;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-label-modern .required-star {
  color: #dc3545;
  margin-left: 3px;
}

.form-control-modern {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.3s ease;
  background: #f8f9fa;
}

.form-control-modern:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control-modern:hover {
  border-color: #bbb;
}

.input-icon {
  position: absolute;
  right: 16px;
  top: 42px;
  color: #adb5bd;
  pointer-events: none;
}

.price-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.product-info-box {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 20px;
  border-radius: 12px;
  border-left: 4px solid #667eea;
  margin-bottom: 25px;
}

.product-info-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.product-info-item:last-child {
  margin-bottom: 0;
}

.info-label {
  color: #6c757d;
  font-weight: 600;
  font-size: 12px;
  text-transform: uppercase;
}

.info-value {
  color: #212529;
  font-weight: 700;
  font-size: 14px;
}

.button-group-modern {
  display: flex;
  gap: 15px;
  margin-top: 35px;
  padding-top: 25px;
  border-top: 2px solid #e9ecef;
}

.btn-modern {
  flex: 1;
  padding: 14px 30px;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
}

.btn-update {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-update:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
  color: white;
}

.btn-cancel {
  background: #f8f9fa;
  color: #495057;
  border: 2px solid #dee2e6;
}

.btn-cancel:hover {
  background: #e9ecef;
  border-color: #adb5bd;
  color: #495057;
  text-decoration: none;
}

.alert-modern {
  padding: 16px 20px;
  border-radius: 10px;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  gap: 12px;
  animation: slideDown 0.4s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-success-modern {
  background: #d4edda;
  color: #155724;
  border-left: 4px solid #28a745;
}

.alert-danger-modern {
  background: #f8d7da;
  color: #721c24;
  border-left: 4px solid #dc3545;
}

.field-changed {
  animation: fieldHighlight 0.6s ease;
}

@keyframes fieldHighlight {
  0% {
    background: #fff3cd;
  }
  100% {
    background: #f8f9fa;
  }
}

/* Select2 Style Enhancement */
select.form-control-modern {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23495057' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
}

/* Number input styling */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  opacity: 1;
  height: 40px;
}

/* Responsive */
@media (max-width: 768px) {
  .price-grid {
    grid-template-columns: 1fr;
  }
  
  .button-group-modern {
    flex-direction: column;
  }
}
</style>

<div class="edit-product-container">
  <div class="row">
    <div class="col-md-12">
      <?php echo display_msg($msg); ?>
    </div>
  </div>

  <div class="modern-card">
    <div class="card-header-modern">
      <h3>
        <i class="glyphicon glyphicon-edit"></i>
        Update Product Details
      </h3>
    </div>

    <div class="card-body-modern">
      <!-- Product Info Box -->
      <div class="product-info-box">
        <div class="product-info-item">
          <span class="info-label">Product ID:</span>
          <span class="info-value"><?php echo remove_junk($product['p_id']); ?></span>
        </div>
      </div>

      <form method="post" action="edit_product.php?id=<?php echo $product['p_id']; ?>" id="editProductForm">

        <!-- Product Name -->
        <div class="form-group-modern">
          <label class="form-label-modern">
            Product Name
            <span class="required-star">*</span>
          </label>
          <input type="text" 
                 class="form-control-modern" 
                 name="product_name"
                 id="product_name"
                 value="<?php echo $clear_fields ? '' : remove_junk($product['product_name']); ?>" 
                 placeholder="Enter product name"
                 required>
          <i class="glyphicon glyphicon-tag input-icon"></i>
        </div>

        <!-- Category -->
        <div class="form-group-modern">
          <label class="form-label-modern">
            Category
            <span class="required-star">*</span>
          </label>
          <select class="form-control-modern" name="product_categorie" id="product_categorie" required>
            <option value="">Select a category</option>
            <?php foreach ($all_categories as $cat): ?>
              <option value="<?php echo $cat['category_name']; ?>"
                <?php if (!$clear_fields && $product['category_name'] === $cat['category_name']) echo "selected"; ?>>
                <?php echo remove_junk($cat['category_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Supplier -->
        <div class="form-group-modern">
          <label class="form-label-modern">
            Supplier
            <span class="required-star">*</span>
          </label>
          <select class="form-control-modern" name="product_supplier" id="product_supplier" required>
            <option value="">Select a supplier</option>
            <?php foreach ($all_suppliers as $sup): ?>
              <option value="<?php echo $sup['s_id']; ?>"
                <?php if (!$clear_fields && $product['s_id'] === $sup['s_id']) echo "selected"; ?>>
                <?php echo remove_junk($sup['s_id']); ?> 
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Quantity -->
        <div class="form-group-modern">
          <label class="form-label-modern">
            Quantity
            <span class="required-star">*</span>
          </label>
          <input type="number" 
                 class="form-control-modern" 
                 name="product_quantity"
                 id="product_quantity"
                 value="<?php echo $clear_fields ? '' : remove_junk($product['quantity']); ?>" 
                 min="0"
                 placeholder="Enter quantity"
                 required>
          <i class="glyphicon glyphicon-shopping-cart input-icon"></i>
        </div>

        <!-- Buying & Selling Price -->
        <div class="price-grid">
          <div class="form-group-modern">
            <label class="form-label-modern">
              Buying Price (LKR)
              <span class="required-star">*</span>
            </label>
            <input type="number" 
                   step="0.01" 
                   class="form-control-modern" 
                   name="buying_price"
                   id="buying_price"
                   value="<?php echo $clear_fields ? '' : remove_junk($product['buying_price']); ?>"
                   placeholder="0.00"
                   required>
            <i class="glyphicon glyphicon-import input-icon"></i>
          </div>
          
          <div class="form-group-modern">
            <label class="form-label-modern">
              Selling Price (LKR)
              <span class="required-star">*</span>
            </label>
            <input type="number" 
                   step="0.01" 
                   class="form-control-modern" 
                   name="selling_price"
                   id="selling_price"
                   value="<?php echo $clear_fields ? '' : remove_junk($product['selling_price']); ?>"
                   placeholder="0.00"
                   required>
            <i class="glyphicon glyphicon-export input-icon"></i>
          </div>
        </div>

        <!-- Expire Date -->
        <div class="form-group-modern">
          <label class="form-label-modern">Expire Date</label>
          <input type="date" 
                 class="form-control-modern" 
                 name="expire_date"
                 id="expire_date"
                 value="<?php echo $clear_fields ? '' : remove_junk($product['expire_date']); ?>">
          <i class="glyphicon glyphicon-calendar input-icon"></i>
        </div>

        <div class="button-group-modern">
          <button type="submit" name="update_product" class="btn-modern btn-update">
            <i class="glyphicon glyphicon-ok"></i>
            Update Product
          </button>
          <a href="product.php" class="btn-modern btn-cancel">
            <i class="glyphicon glyphicon-remove"></i>
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script>
$(document).ready(function() {
  // Track initial values
  const initialValues = {
    product_name: $('#product_name').val(),
    product_categorie: $('#product_categorie').val(),
    product_supplier: $('#product_supplier').val(),
    product_quantity: $('#product_quantity').val(),
    buying_price: $('#buying_price').val(),
    selling_price: $('#selling_price').val(),
    expire_date: $('#expire_date').val()
  };

  // Highlight changed fields
  $('input, select').on('change', function() {
    const fieldName = $(this).attr('name');
    if (initialValues[fieldName] !== $(this).val()) {
      $(this).addClass('field-changed');
      setTimeout(() => {
        $(this).removeClass('field-changed');
      }, 600);
    }
  });

  // Auto-focus first field
  $('#product_name').focus();

  // Validate prices (selling should be greater than buying)
  $('#selling_price, #buying_price').on('input', function() {
    const buyingPrice = parseFloat($('#buying_price').val()) || 0;
    const sellingPrice = parseFloat($('#selling_price').val()) || 0;
    
    if (sellingPrice < buyingPrice && sellingPrice > 0) {
      $('#selling_price').css('border-color', '#dc3545');
    } else {
      $('#selling_price').css('border-color', '#e0e0e0');
    }
  });

  // Form submission confirmation
  $('#editProductForm').on('submit', function(e) {
    const buyingPrice = parseFloat($('#buying_price').val()) || 0;
    const sellingPrice = parseFloat($('#selling_price').val()) || 0;
    
    if (sellingPrice < buyingPrice) {
      if (!confirm('Warning: Selling price is lower than buying price. Continue?')) {
        e.preventDefault();
        return false;
      }
    }
  });

  // Auto-hide alerts after 5 seconds
  setTimeout(function() {
    $('.alert').fadeOut();
  }, 5000);

  // Add smooth scroll to top after update
  if (window.location.href.indexOf('success=1') > -1) {
    $('html, body').animate({
      scrollTop: 0
    }, 500);
    
    // Show success animation
    $('.modern-card').css('animation', 'slideDown 0.6s ease');
  }
});
</script>
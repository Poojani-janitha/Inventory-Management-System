<?php
  $page_title = 'All Purchase Orders';
  require_once('includes/load.php');
  page_require_level(2);

  $purchase_orders = find_all('purchase_order');
  $msg = $session->msg();
?>

<?php include_once('layouts/header.php'); ?>
<link rel="stylesheet" href="libs/css/purchase_orders.css">

<style>
  /* --- Interactive Panel Styling --- */
  .panel {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    border: none;
  }

  .panel-heading {
    background: #2a3f54;
    color: #fff;
    border-radius: 10px 10px 0 0;
    padding: 15px;
  }

  .table {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
  }

  .table th {
    background-color: #f5f7fa;
    text-align: center;
  }

  .table tr:hover {
    background-color: #f0f8ff;
  }

  /* --- Status Labels --- */
  .status-label {
    display: inline-block;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 20px;
    text-transform: capitalize;
  }
  .status-pending { background-color: #ff9800; color: #fff; }
  .status-accepted { background-color: #4caf50; color: #fff; }
  .status-added { background-color: #e53935; color: #fff; }

  /* --- Buttons --- */
  .btn-group .btn {
    border-radius: 6px;
    margin: 2px;
    padding: 6px 12px;
    font-size: 13px;
    min-width: 80px;       /* 👈 Ensures all buttons have the same width */
    height: 30px;           /* 👈 Ensures same height */
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .btn-info {
    background-color: #007bff;
    border: none;
    color: #fff;
  }

  .btn-info:hover {
    background-color: #0056b3;
  }

  .btn-disabled {
    background-color: #b0bec5;
    border: none;
    color: #fff;
    cursor: not-allowed;
  }

  /* --- Popup Modal --- */
  .popup-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 999;
  }

  .custom-popup {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    color: #333;
    padding: 25px 35px;
    border-radius: 10px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.4);
    z-index: 1000;
    text-align: center;
    width: 350px;
    animation: fadeIn 0.3s ease;
  }

  @keyframes fadeIn {
    from {opacity: 0; transform: translate(-50%, -45%);}
    to {opacity: 1; transform: translate(-50%, -50%);}
  }

  .popup-btn {
    margin-top: 20px;
    background-color: #2a3f54;
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
  }

  .popup-btn:hover {
    background-color: #1e2e3d;
  }
</style>

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
              <?php
                $status = strtolower(trim($order['status']));
                $status_class = '';
                if ($status == 'pending') $status_class = 'status-pending';
                elseif ($status == 'accepted') $status_class = 'status-accepted';
                elseif ($status == 'added') $status_class = 'status-added';
              ?>
              <tr>
                <td class="text-center"><?php echo remove_junk($order['o_id']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['s_id']); ?></td>
                <td><?php echo remove_junk($order['product_name']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['category_name']); ?></td>
                <td class="text-center"><?php echo remove_junk($order['quantity']); ?></td>
                <td class="text-center"><?php echo number_format($order['price'], 2); ?></td>
                <td class="text-center"><?php echo read_date($order['order_date']); ?></td>
                <td class="text-center">
                  <span class="status-label <?php echo $status_class; ?>">
                    <?php echo ucfirst($status); ?>
                  </span>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <?php if ($status == 'accepted' || $status == 'approved'): ?>
                      <a href="add_product.php?o_id=<?php echo $order['o_id'];?>" 
                         class="btn btn-info btn-xs" 
                         data-toggle="tooltip" 
                         title="Add Product to Inventory">
                        <i class="glyphicon glyphicon-plus"></i> Add
                      </a>
                    <?php elseif ($status == 'pending'): ?>
                      <button class="btn btn-disabled btn-xs"
                              onclick="showPopup('Order is still pending.')">
                        <i class="glyphicon glyphicon-ban-circle"></i> Add
                      </button>
                    <?php elseif ($status == 'added'): ?>
                      <button class="btn btn-disabled btn-xs"
                              onclick="showPopup('This product is already added.')">
                        <i class="glyphicon glyphicon-ok-circle"></i> Add
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

<!-- Popup Alert -->
<div class="popup-overlay" id="popupOverlay"></div>
<div class="custom-popup" id="customPopup">
  <p id="popupMessage" style="font-size:15px; font-weight:500;"></p>
  <button class="popup-btn" onclick="closePopup()">OK</button>
</div>

<script>
  function showPopup(message) {
    document.getElementById('popupMessage').innerText = message;
    document.getElementById('popupOverlay').style.display = 'block';
    document.getElementById('customPopup').style.display = 'block';
  }

  function closePopup() {
    document.getElementById('popupOverlay').style.display = 'none';
    document.getElementById('customPopup').style.display = 'none';
  }
</script>

<?php include_once('layouts/footer.php'); ?>

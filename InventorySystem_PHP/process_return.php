<?php
  $page_title = 'Process Return';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
 if(isset($_GET['id']) && isset($_GET['action'])){
   $return_id = (int)$_GET['id'];
   $action = $_GET['action'];
   
   if($action == 'approve' || $action == 'reject'){
     $notes = isset($_POST['notes']) ? remove_junk($db->escape($_POST['notes'])) : '';
     
     if(process_return($return_id, $action, $notes)){
       $session->msg('s', "Return {$action}d successfully.");
     } else {
       $session->msg('d', "Failed to {$action} return.");
     }
   }
   
   redirect('returns.php', false);
 }
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-edit"></span>
          <span>Process Return</span>
        </strong>
      </div>
      <div class="panel-body">
        <?php
        $return_id = (int)$_GET['id'];
        $return = find_by_id('returns', $return_id);
        if($return):
          $product = find_by_id('products', $return['product_id']);
          $user = find_by_id('users', $return['processed_by']);
        ?>
        <div class="row">
          <div class="col-md-6">
            <h4>Return Details</h4>
            <table class="table table-bordered">
              <tr>
                <td><strong>Product:</strong></td>
                <td><?php echo remove_junk($product['name']); ?></td>
              </tr>
              <tr>
                <td><strong>Quantity:</strong></td>
                <td><?php echo (int)$return['quantity']; ?></td>
              </tr>
              <tr>
                <td><strong>Reason:</strong></td>
                <td>
                  <span class="label label-<?php echo get_reason_label_class($return['return_reason']); ?>">
                    <?php echo remove_junk($return['return_reason']); ?>
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td>
                  <span class="label label-<?php echo get_status_label_class($return['status']); ?>">
                    <?php echo remove_junk($return['status']); ?>
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>Refund Amount:</strong></td>
                <td>$<?php echo number_format($return['refund_amount'], 2); ?></td>
              </tr>
              <tr>
                <td><strong>Return Date:</strong></td>
                <td><?php echo remove_junk($return['return_date']); ?></td>
              </tr>
              <tr>
                <td><strong>Processed By:</strong></td>
                <td><?php echo remove_junk($user['name']); ?></td>
              </tr>
            </table>
          </div>
          
          <div class="col-md-6">
            <h4>Product Information</h4>
            <table class="table table-bordered">
              <tr>
                <td><strong>Product ID:</strong></td>
                <td><?php echo $product['id']; ?></td>
              </tr>
              <tr>
                <td><strong>Current Stock:</strong></td>
                <td><?php echo (int)$product['quantity']; ?></td>
              </tr>
              <tr>
                <td><strong>Unit Price:</strong></td>
                <td>$<?php echo number_format($product['sale_price'], 2); ?></td>
              </tr>
              <?php if($product['expiry_date']): ?>
              <tr>
                <td><strong>Expiry Date:</strong></td>
                <td><?php echo $product['expiry_date']; ?></td>
              </tr>
              <?php endif; ?>
            </table>
            
            <?php if($return['status'] == 'Pending'): ?>
            <div class="alert alert-info">
              <h5><i class="glyphicon glyphicon-info-sign"></i> Processing Options</h5>
              <p>Choose an action for this return:</p>
              <div class="btn-group-vertical" style="width: 100%;">
                <a href="process_return.php?id=<?php echo $return_id; ?>&action=approve" 
                   class="btn btn-success btn-lg" 
                   onclick="return confirm('Approve this return? This will process the refund and update stock.');">
                  <span class="glyphicon glyphicon-ok"></span> Approve Return
                </a>
                <a href="process_return.php?id=<?php echo $return_id; ?>&action=reject" 
                   class="btn btn-danger btn-lg" 
                   onclick="return confirm('Reject this return? This will deny the refund request.');">
                  <span class="glyphicon glyphicon-remove"></span> Reject Return
                </a>
              </div>
            </div>
            <?php else: ?>
            <div class="alert alert-<?php echo $return['status'] == 'Approved' ? 'success' : 'danger'; ?>">
              <h5><i class="glyphicon glyphicon-<?php echo $return['status'] == 'Approved' ? 'ok' : 'remove'; ?>-sign"></i> 
                Return <?php echo $return['status']; ?>
              </h5>
              <p>This return has already been processed.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
        
        <?php if($return['notes']): ?>
        <div class="row">
          <div class="col-md-12">
            <h4>Notes</h4>
            <div class="well">
              <?php echo remove_junk($return['notes']); ?>
            </div>
          </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="alert alert-danger">
          <h4>Return Not Found</h4>
          <p>The requested return could not be found.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-lightbulb"></span>
          <span>Processing Tips</span>
        </strong>
      </div>
      <div class="panel-body">
        <ul class="list-unstyled">
          <li><i class="glyphicon glyphicon-ok text-success"></i> Verify product condition before approval</li>
          <li><i class="glyphicon glyphicon-ok text-success"></i> Check return reason validity</li>
          <li><i class="glyphicon glyphicon-ok text-success"></i> Ensure stock levels are accurate</li>
          <li><i class="glyphicon glyphicon-ok text-success"></i> Consider supplier return for expired items</li>
          <li><i class="glyphicon glyphicon-ok text-success"></i> Update inventory after processing</li>
        </ul>
      </div>
    </div>
    
    <?php if($return && $return['return_reason'] == 'Expired'): ?>
    <div class="panel panel-warning">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-exclamation-sign"></span>
          <span>Supplier Return Suggestion</span>
        </strong>
      </div>
      <div class="panel-body">
        <p>This is an expired product return. Consider contacting the supplier:</p>
        <?php
        $supplier_email = get_supplier_email($return['product_id']);
        if($supplier_email):
        ?>
        <p><strong>Supplier Email:</strong> <a href="mailto:<?php echo $supplier_email; ?>"><?php echo $supplier_email; ?></a></p>
        <?php else: ?>
        <p class="text-muted">No supplier email on file for this product.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<style>
.processing-panel {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
}

.btn-group-vertical .btn {
  margin-bottom: 10px;
  border-radius: 5px;
}

.alert-info {
  background-color: #d1ecf1;
  border-color: #bee5eb;
  color: #0c5460;
}

.alert-success {
  background-color: #d4edda;
  border-color: #c3e6cb;
  color: #155724;
}

.alert-danger {
  background-color: #f8d7da;
  border-color: #f5c6cb;
  color: #721c24;
}

.alert-warning {
  background-color: #fff3cd;
  border-color: #ffeaa7;
  color: #856404;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.well {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 5px;
  padding: 15px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Add confirmation dialogs for processing actions
  const approveBtn = document.querySelector('a[href*="action=approve"]');
  const rejectBtn = document.querySelector('a[href*="action=reject"]');
  
  if(approveBtn) {
    approveBtn.addEventListener('click', function(e) {
      if(!confirm('Are you sure you want to approve this return? This will process the refund and update stock levels.')) {
        e.preventDefault();
      }
    });
  }
  
  if(rejectBtn) {
    rejectBtn.addEventListener('click', function(e) {
      if(!confirm('Are you sure you want to reject this return? This will deny the refund request.')) {
        e.preventDefault();
      }
    });
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>

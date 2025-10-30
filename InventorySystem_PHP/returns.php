<?php
  $page_title = 'Returns Management';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
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
          <span class="glyphicon glyphicon-arrow-left"></span>
          <span>Returns Management</span>
        </strong>
        <div class="pull-right">
          <a href="add_return.php" class="btn btn-danger btn-sm">
            <span class="glyphicon glyphicon-plus"></span> Add Return
          </a>
          <a href="return_report.php" class="btn btn-info btn-sm">
            <span class="glyphicon glyphicon-stats"></span> Reports
          </a>
        </div>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <!-- Filter Options -->
            <div class="row" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
              <div class="col-md-3">
                <label style="font-weight: 600; color: #495057; margin-bottom: 5px;">Search by Product</label>
                <input type="text" class="form-control" id="productFilter" placeholder="Search product name...">
              </div>
              <div class="col-md-2">
                <label style="font-weight: 600; color: #495057; margin-bottom: 5px;">Category</label>
                <select class="form-control" id="categoryFilter">
                  <option value="">All Categories</option>
                  <?php
                  $categories = find_all('categories');
                  if($categories):
                    foreach($categories as $cat):
                      $cat_name = isset($cat['category_name']) ? $cat['category_name'] : (isset($cat['name']) ? $cat['name'] : '');
                      if(!empty($cat_name)):
                  ?>
                  <option value="<?php echo htmlspecialchars($cat_name); ?>">
                    <?php echo htmlspecialchars($cat_name); ?>
                  </option>
                  <?php 
                      endif;
                    endforeach;
                  endif; 
                  ?>
                </select>
              </div>
              <div class="col-md-2">
                <label style="font-weight: 600; color: #495057; margin-bottom: 5px;">Supplier</label>
                <input type="text" class="form-control" id="supplierFilter" placeholder="Search supplier...">
              </div>
              <div class="col-md-2">
                <label style="font-weight: 600; color: #495057; margin-bottom: 5px;">From Date</label>
                <input type="date" class="form-control" id="dateFrom">
              </div>
              <div class="col-md-2">
                <label style="font-weight: 600; color: #495057; margin-bottom: 5px;">To Date</label>
                <input type="date" class="form-control" id="dateTo">
              </div>
              <div class="col-md-1">
                <label style="font-weight: 600; color: #495057; margin-bottom: 5px;">&nbsp;</label>
                <button class="btn btn-danger btn-block" onclick="clearFilters()">
                  <span class="glyphicon glyphicon-refresh"></span> Reset
                </button>
              </div>
            </div>
            
            <!-- Returns Table -->
            <table class="table table-bordered table-striped" id="returnsTable">
              <thead>
                <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th>Product Details</th>
                  <th class="text-center">Return Qty</th>
                  <th class="text-center">Buying Price (per unit)</th>
                  <th class="text-center">Return Price (Total)</th>
                  <th class="text-center">Current Stock</th>
                  <th class="text-center">Supplier</th>
                  <th class="text-center">Category</th>
                  <th class="text-center">Date</th>
                  <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $returns = find_all_returns();
                $counter = 1;
                if($returns):
                  foreach($returns as $return):
                ?>
                <tr>
                  <td class="text-center"><?php echo $counter++; ?></td>
                  <td>
                    <strong><?php echo htmlspecialchars($return['product_name']); ?></strong>
                    <br><small class="text-muted">ID: <?php echo htmlspecialchars($return['p_id']); ?></small>
                  </td>
                  <td class="text-center">
                    <span class="label label-warning"><?php echo (int)$return['return_quantity']; ?> units</span>
                  </td>
                  <td class="text-center">Rs. <?php echo number_format($return['buying_price'], 2); ?></td>
                  <td class="text-center" style="font-weight: bold; color: #d9534f; font-size: 16px;">
                    Rs. <?php 
                      $return_price = $return['return_quantity'] * $return['buying_price'];
                      echo number_format($return_price, 2); 
                    ?>
                  </td>
                  <td class="text-center">
                    <span class="label label-info"><?php echo (int)($return['current_stock'] ?? 0); ?> units</span>
                  </td>
                  <td class="text-center">
                    <strong><?php echo htmlspecialchars($return['supplier_name'] ?? 'N/A'); ?></strong>
                    <br><small class="text-muted">ID: <?php echo htmlspecialchars($return['s_id']); ?></small>
                    <?php if(!empty($return['contact_number'])): ?>
                      <br><small class="text-muted"><?php echo htmlspecialchars($return['contact_number']); ?></small>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <span class="label label-default"><?php echo htmlspecialchars($return['category_name'] ?? 'N/A'); ?></span>
                  </td>
                  <td class="text-center">
                    <small><?php echo date('Y-m-d', strtotime($return['return_date'])); ?></small>
                    <br><small class="text-muted"><?php echo date('H:i', strtotime($return['return_date'])); ?></small>
                  </td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_return.php?id=<?php echo (int)$return['return_id'];?>" class="btn btn-info btn-xs" title="Edit" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_return.php?id=<?php echo (int)$return['return_id'];?>" class="btn btn-danger btn-xs" title="Delete" data-toggle="tooltip" onclick="return confirm('Are you sure you want to delete this return? This action cannot be undone.');">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                  <td colspan="10" class="text-center">
                    <div class="alert alert-info">
                      <p><i class="glyphicon glyphicon-info-sign"></i> No returns found in database.</p>
                      <p><a href="add_return.php" class="btn btn-primary btn-sm">Add New Return</a></p>
                    </div>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Returns Summary -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-stats"></span>
          <span>Returns Summary</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <div class="text-center">
              <h3 style="margin: 0; color: #5bc0de;"><?php echo count(find_all_returns()); ?></h3>
              <p class="text-muted">Total Returns</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h3 style="margin: 0; color: #5cb85c;"><?php echo date('Y-m-d'); ?></h3>
              <p class="text-muted">Current Date</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <h3 style="margin: 0; color: #f0ad4e;">Recent</h3>
              <p class="text-muted">Last 30 Days</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <a href="add_return.php" class="btn btn-danger btn-lg">
                <span class="glyphicon glyphicon-plus"></span> Add Return
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.returns-management {
  background: #f8f9fa;
  min-height: 100vh;
}

.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  border: none;
}

.btn-info {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
  border: none;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #495057;
}

.label {
  font-size: 0.85em;
  padding: 0.3em 0.6em;
}

.alert-warning {
  background-color: #fff3cd;
  border-color: #ffeaa7;
  color: #856404;
}

.filter-controls {
  background: #e9ecef;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.returns-stats {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.stat-item {
  text-align: center;
  padding: 10px;
}

.stat-number {
  font-size: 2em;
  font-weight: bold;
  display: block;
}

.stat-label {
  font-size: 0.9em;
  opacity: 0.8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  $('[data-toggle="tooltip"]').tooltip();
  
  // Filter functionality
  const productFilter = document.getElementById('productFilter');
  const categoryFilter = document.getElementById('categoryFilter');
  const supplierFilter = document.getElementById('supplierFilter');
  const dateFrom = document.getElementById('dateFrom');
  const dateTo = document.getElementById('dateTo');
  const table = document.getElementById('returnsTable');
  
  function filterTable() {
    const product = productFilter.value.toLowerCase();
    const category = categoryFilter.value.toLowerCase();
    const supplier = supplierFilter.value.toLowerCase();
    const fromDate = dateFrom.value;
    const toDate = dateTo.value;
    
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) return;
    
    const rows = tbody.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 0; i < rows.length; i++) {
      const row = rows[i];
      const productCell = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
      const categoryCell = row.cells[7] ? row.cells[7].textContent.toLowerCase() : '';
      const supplierCell = row.cells[6] ? row.cells[6].textContent.toLowerCase() : '';
      const dateCell = row.cells[8] ? row.cells[8].textContent.trim() : '';
      
      // Extract date from cell (format: YYYY-MM-DD)
      const dateMatch = dateCell.match(/(\d{4}-\d{2}-\d{2})/);
      const cellDate = dateMatch ? dateMatch[1] : '';
      
      let showRow = true;
      
      if (product && !productCell.includes(product)) showRow = false;
      if (category && !categoryCell.includes(category)) showRow = false;
      if (supplier && !supplierCell.includes(supplier)) showRow = false;
      
      if (fromDate && cellDate && cellDate < fromDate) showRow = false;
      if (toDate && cellDate && cellDate > toDate) showRow = false;
      
      if (showRow) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    }
    
    // Update table header with count
    const thead = table.querySelector('thead tr');
    if (thead && visibleCount > 0) {
      thead.style.display = '';
    }
  }
  
  // Add event listeners
  if (productFilter) productFilter.addEventListener('input', filterTable);
  if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
  if (supplierFilter) supplierFilter.addEventListener('input', filterTable);
  if (dateFrom) dateFrom.addEventListener('change', filterTable);
  if (dateTo) dateTo.addEventListener('change', filterTable);
});

function clearFilters() {
  document.getElementById('productFilter').value = '';
  document.getElementById('categoryFilter').value = '';
  document.getElementById('supplierFilter').value = '';
  document.getElementById('dateFrom').value = '';
  document.getElementById('dateTo').value = '';
  
  // Reset table display
  const table = document.getElementById('returnsTable');
  const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
  for (let i = 0; i < rows.length; i++) {
    rows[i].style.display = '';
  }
}

function updateAlertsDisplay(alerts) {
  // Implementation for updating alerts display
  console.log('New alerts:', alerts);
}
</script>

<?php include_once('layouts/footer.php'); ?>

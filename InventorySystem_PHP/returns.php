<?php
  $page_title = 'Returns Management';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
  //extra add prabashi 
    $msg = $session->msg();
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
          <span>Returns Management - Complete Details</span>
        </strong>
        <div class="pull-right">
          <a href="add_return.php" class="btn btn-danger btn-sm">
            <span class="glyphicon glyphicon-plus"></span> Add New Return
          </a>
          <a href="return_report.php" class="btn btn-info btn-sm">
            <span class="glyphicon glyphicon-stats"></span> Reports
          </a>
        </div>
      </div>
      <div class="panel-body">
        <!-- Filter Section -->
        <div class="filter-section">
          <div class="row">
            <div class="col-md-3">
              <label><i class="glyphicon glyphicon-search"></i> Search Product</label>
              <input type="text" class="form-control" id="productFilter" placeholder="Search by product name or ID...">
            </div>
            <div class="col-md-2">
              <label><i class="glyphicon glyphicon-list"></i> Category</label>
              <select class="form-control" id="categoryFilter">
                <option value="">All Categories</option>
                <?php
                $categories = find_all('categories');
                if($categories):
                  foreach($categories as $cat):
                    $cat_name = isset($cat['category_name']) ? $cat['category_name'] : '';
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
              <label><i class="glyphicon glyphicon-user"></i> Supplier</label>
              <select class="form-control" id="supplierFilter">
                <option value="">All Suppliers</option>
                <?php
                $suppliers = find_all('supplier_info');
                if($suppliers):
                  foreach($suppliers as $supplier):
                ?>
                <option value="<?php echo htmlspecialchars($supplier['s_name']); ?>">
                  <?php echo htmlspecialchars($supplier['s_name']); ?> (<?php echo $supplier['s_id']; ?>)
                </option>
                <?php 
                  endforeach;
                endif; 
                ?>
              </select>
            </div>
            <div class="col-md-2">
              <label><i class="glyphicon glyphicon-calendar"></i> From Date</label>
              <input type="date" class="form-control" id="dateFrom">
            </div>
            <div class="col-md-2">
              <label><i class="glyphicon glyphicon-calendar"></i> To Date</label>
              <input type="date" class="form-control" id="dateTo">
            </div>
            <div class="col-md-1">
              <label>&nbsp;</label>
              <button class="btn btn-warning btn-block" onclick="clearFilters()" title="Reset all filters">
                <span class="glyphicon glyphicon-refresh"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Returns Table -->
        <div class="table-responsive" style="margin-top: 20px;">
          <table class="table table-bordered table-striped table-hover" id="returnsTable">
            <thead>
              <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <th class="text-center" style="width: 40px;">#</th>
                <th class="text-center">Return ID</th>
                <th>Product Details</th>
                <th class="text-center">Return Qty</th>
                <th class="text-center">Buying Price (Unit)</th>
                <th class="text-center">Total Return Amount</th>
                <th class="text-center">Current Stock</th>
                <th>Supplier Details</th>
                <th class="text-center">Category</th>
                <th class="text-center">Return Date</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $returns = find_all_returns();
              $counter = 1;
              $total_return_amount = 0;
              
              if($returns && count($returns) > 0):
                foreach($returns as $return):
                  $return_amount = $return['return_quantity'] * $return['buying_price'];
                  $total_return_amount += $return_amount;
              ?>
              <tr class="return-row" 
                  data-product="<?php echo htmlspecialchars($return['product_name']); ?>"
                  data-category="<?php echo htmlspecialchars($return['category_name'] ?? ''); ?>"
                  data-supplier="<?php echo htmlspecialchars($return['supplier_name'] ?? ''); ?>"
                  data-date="<?php echo date('Y-m-d', strtotime($return['return_date'])); ?>">
                <td class="text-center"><strong><?php echo $counter++; ?></strong></td>
                <td class="text-center">
                  <span class="label label-primary" style="font-size: 13px;">
                    #<?php echo str_pad($return['return_id'], 4, '0', STR_PAD_LEFT); ?>
                  </span>
                </td>
                <td>
                  <strong style="color: #2c3e50; font-size: 14px;">
                    <?php echo htmlspecialchars($return['product_name']); ?>
                  </strong>
                  <br>
                  <small class="text-muted">
                    <i class="glyphicon glyphicon-tag"></i> Product ID: 
                    <strong><?php echo htmlspecialchars($return['p_id']); ?></strong>
                  </small>
                  <br>
                  <small class="text-muted">
                    <i class="glyphicon glyphicon-shopping-cart"></i> Selling Price: 
                    <strong>Rs. <?php echo number_format($return['selling_price'] ?? 0, 2); ?></strong>
                  </small>
                </td>
                <td class="text-center">
                  <span class="label label-warning" style="font-size: 14px; padding: 6px 12px;">
                    <?php echo (int)$return['return_quantity']; ?> units
                  </span>
                </td>
                <td class="text-center" style="font-size: 14px;">
                  <strong>Rs. <?php echo number_format($return['buying_price'], 2); ?></strong>
                </td>
                <td class="text-center" style="background-color: #fff3cd;">
                  <strong style="color: #d9534f; font-size: 16px;">
                    Rs. <?php echo number_format($return_amount, 2); ?>
                  </strong>
                </td>
                <td class="text-center">
                  <span class="label label-info" style="font-size: 13px; padding: 5px 10px;">
                    <?php echo (int)($return['current_stock'] ?? 0); ?> units
                  </span>
                </td>
                <td>
                  <strong style="color: #2980b9;">
                    <?php echo htmlspecialchars($return['supplier_name'] ?? 'N/A'); ?>
                  </strong>
                  <br>
                  <small class="text-muted">
                    <i class="glyphicon glyphicon-user"></i> ID: 
                    <strong><?php echo htmlspecialchars($return['s_id']); ?></strong>
                  </small>
                  <?php if(!empty($return['contact_number'])): ?>
                    <br>
                    <small class="text-muted">
                      <i class="glyphicon glyphicon-phone"></i> 
                      <?php echo htmlspecialchars($return['contact_number']); ?>
                    </small>
                  <?php endif; ?>
                  <?php if(!empty($return['supplier_email'])): ?>
                    <br>
                    <small class="text-muted">
                      <i class="glyphicon glyphicon-envelope"></i> 
                      <?php echo htmlspecialchars($return['supplier_email']); ?>
                    </small>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <span class="label label-default" style="font-size: 12px; padding: 5px 10px;">
                    <?php echo htmlspecialchars($return['category_name'] ?? 'N/A'); ?>
                  </span>
                </td>
                <td class="text-center">
                  <strong><?php echo date('Y-m-d', strtotime($return['return_date'])); ?></strong>
                  <br>
                  <small class="text-muted">
                    <i class="glyphicon glyphicon-time"></i> 
                    <?php echo date('H:i:s', strtotime($return['return_date'])); ?>
                  </small>
                </td>
                <td class="text-center">
                  <div class="btn-group-vertical" style="width: 100%;">
                    <a href="edit_return.php?id=<?php echo (int)$return['return_id'];?>" 
                       class="btn btn-info btn-xs" 
                       title="Edit Return" 
                       data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span> Edit
                    </a>
                    <a href="delete_return.php?id=<?php echo (int)$return['return_id'];?>" 
                       class="btn btn-danger btn-xs" 
                       title="Delete Return" 
                       data-toggle="tooltip"
                       onclick="return confirm('Are you sure you want to delete this return?\n\nReturn ID: #<?php echo $return['return_id']; ?>\nProduct: <?php echo htmlspecialchars($return['product_name']); ?>\nAmount: Rs. <?php echo number_format($return_amount, 2); ?>\n\nThis action cannot be undone!');">
                      <span class="glyphicon glyphicon-trash"></span> Delete
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td colspan="11" class="text-center">
                  <div class="alert alert-info" style="margin: 20px;">
                    <h4><i class="glyphicon glyphicon-info-sign"></i> No Returns Found</h4>
                    <p>There are no product returns in the database yet.</p>
                    <p>
                      <a href="add_return.php" class="btn btn-danger">
                        <span class="glyphicon glyphicon-plus"></span> Add First Return
                      </a>
                    </p>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
            <?php if($returns && count($returns) > 0): ?>
            <tfoot>
              <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="5" class="text-right" style="font-size: 16px;">
                  <strong>TOTAL RETURN VALUE:</strong>
                </td>
                <td class="text-center" style="background-color: #ffe5e5; font-size: 18px;">
                  <strong style="color: #d9534f;">Rs. <?php echo number_format($total_return_amount, 2); ?></strong>
                </td>
                <td colspan="5"></td>
              </tr>
            </tfoot>
            <?php endif; ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Summary Statistics -->
<?php if($returns && count($returns) > 0): ?>
<div class="row">
  <div class="col-md-3">
    <div class="panel panel-primary">
      <div class="panel-heading text-center">
        <h4 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-list-alt"></i> Total Returns
        </h4>
      </div>
      <div class="panel-body text-center">
        <h2 style="color: #3498db; margin: 0; font-weight: bold;">
          <?php echo count($returns); ?>
        </h2>
        <p class="text-muted">Return Transactions</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="panel panel-danger">
      <div class="panel-heading text-center">
        <h4 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-usd"></i> Total Amount
        </h4>
      </div>
      <div class="panel-body text-center">
        <h2 style="color: #e74c3c; margin: 0; font-weight: bold;">
          Rs. <?php echo number_format($total_return_amount, 2); ?>
        </h2>
        <p class="text-muted">Return Value</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="panel panel-success">
      <div class="panel-heading text-center">
        <h4 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-calendar"></i> Latest Return
        </h4>
      </div>
      <div class="panel-body text-center">
        <h2 style="color: #27ae60; margin: 0; font-weight: bold;">
          <?php echo date('M d', strtotime($returns[0]['return_date'])); ?>
        </h2>
        <p class="text-muted"><?php echo date('Y', strtotime($returns[0]['return_date'])); ?></p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="panel panel-warning">
      <div class="panel-heading text-center">
        <h4 style="margin: 10px 0;">
          <i class="glyphicon glyphicon-shopping-cart"></i> Avg Per Return
        </h4>
      </div>
      <div class="panel-body text-center">
        <h2 style="color: #f39c12; margin: 0; font-weight: bold;">
          Rs. <?php echo number_format($total_return_amount / count($returns), 2); ?>
        </h2>
        <p class="text-muted">Average Value</p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<style>
.filter-section {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-section label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 5px;
  display: block;
}

.filter-section .form-control {
  border-radius: 5px;
  border: 1px solid #bdc3c7;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.filter-section .form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
  padding: 15px 20px;
}

.panel-heading strong {
  font-size: 18px;
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table {
  margin-bottom: 0;
}

.table thead th {
  border-bottom: 2px solid #dee2e6;
  vertical-align: middle;
  font-size: 13px;
  padding: 12px 8px;
  white-space: nowrap;
}

.table tbody td {
  vertical-align: middle;
  padding: 10px 8px;
  font-size: 13px;
}

.table-hover tbody tr:hover {
  background-color: #f1f8ff;
  cursor: pointer;
}

.label {
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 3px;
  font-weight: 600;
}

.btn-group-vertical .btn {
  margin-bottom: 3px;
  border-radius: 3px;
  font-size: 11px;
  padding: 4px 8px;
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  border: none;
}

.btn-info {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
  border: none;
}

.btn-warning {
  background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
  border: none;
}

.alert-info {
  background-color: #d1ecf1;
  border-color: #bee5eb;
  color: #0c5460;
}

.panel-primary .panel-heading,
.panel-danger .panel-heading,
.panel-success .panel-heading,
.panel-warning .panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
}

.panel-primary {
  border-color: #3498db;
}

.panel-danger {
  border-color: #e74c3c;
}

.panel-success {
  border-color: #27ae60;
}

.panel-warning {
  border-color: #f39c12;
}

/* Scrollbar styling for webkit browsers */
.table-responsive::-webkit-scrollbar {
  height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
  background: #667eea;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background: #764ba2;
}

/* Print styles */
@media print {
  .filter-section,
  .btn,
  .panel-heading .pull-right {
    display: none !important;
  }
  
  .table {
    font-size: 10px;
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .filter-section .col-md-3,
  .filter-section .col-md-2,
  .filter-section .col-md-1 {
    margin-bottom: 10px;
  }
  
  .table thead th {
    font-size: 11px;
    padding: 8px 4px;
  }
  
  .table tbody td {
    font-size: 11px;
    padding: 8px 4px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  $('[data-toggle="tooltip"]').tooltip();
  
  // Get filter elements
  const productFilter = document.getElementById('productFilter');
  const categoryFilter = document.getElementById('categoryFilter');
  const supplierFilter = document.getElementById('supplierFilter');
  const dateFrom = document.getElementById('dateFrom');
  const dateTo = document.getElementById('dateTo');
  const table = document.getElementById('returnsTable');
  
  // Filter function
  function filterTable() {
    const product = productFilter.value.toLowerCase();
    const category = categoryFilter.value.toLowerCase();
    const supplier = supplierFilter.value.toLowerCase();
    const fromDate = dateFrom.value;
    const toDate = dateTo.value;
    
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) return;
    
    const rows = tbody.getElementsByClassName('return-row');
    let visibleCount = 0;
    let visibleTotal = 0;
    
    for (let i = 0; i < rows.length; i++) {
      const row = rows[i];
      const rowProduct = row.dataset.product.toLowerCase();
      const rowCategory = row.dataset.category.toLowerCase();
      const rowSupplier = row.dataset.supplier.toLowerCase();
      const rowDate = row.dataset.date;
      
      // Get return amount from the row
      const amountCell = row.cells[5];
      const amountText = amountCell.textContent.replace(/[^\d.]/g, '');
      const amount = parseFloat(amountText) || 0;
      
      let showRow = true;
      
      // Apply filters
      if (product && !rowProduct.includes(product)) showRow = false;
      if (category && !rowCategory.includes(category)) showRow = false;
      if (supplier && !rowSupplier.includes(supplier)) showRow = false;
      
      if (fromDate && rowDate && rowDate < fromDate) showRow = false;
      if (toDate && rowDate && rowDate > toDate) showRow = false;
      
      if (showRow) {
        row.style.display = '';
        visibleCount++;
        visibleTotal += amount;
        // Update row number
        row.cells[0].innerHTML = '<strong>' + visibleCount + '</strong>';
      } else {
        row.style.display = 'none';
      }
    }
    
    // Update footer total if it exists
    const tfoot = table.getElementsByTagName('tfoot')[0];
    if (tfoot && visibleCount > 0) {
      const totalCell = tfoot.rows[0].cells[1];
      totalCell.innerHTML = '<strong style="color: #d9534f;">Rs. ' + visibleTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '</strong>';
      tfoot.style.display = '';
    } else if (tfoot) {
      tfoot.style.display = 'none';
    }
    
    // Show message if no results
    if (visibleCount === 0 && rows.length > 0) {
      showNoResultsMessage();
    } else {
      hideNoResultsMessage();
    }
  }
  
  function showNoResultsMessage() {
    let noResultsRow = document.getElementById('no-results-row');
    if (!noResultsRow) {
      const tbody = table.getElementsByTagName('tbody')[0];
      noResultsRow = tbody.insertRow(0);
      noResultsRow.id = 'no-results-row';
      const cell = noResultsRow.insertCell(0);
      cell.colSpan = 11;
      cell.className = 'text-center';
      cell.innerHTML = '<div class="alert alert-warning" style="margin: 20px;"><h4><i class="glyphicon glyphicon-search"></i> No Results Found</h4><p>No returns match your filter criteria. Try adjusting your filters.</p></div>';
    }
    noResultsRow.style.display = '';
  }
  
  function hideNoResultsMessage() {
    const noResultsRow = document.getElementById('no-results-row');
    if (noResultsRow) {
      noResultsRow.style.display = 'none';
    }
  }
  
  // Add event listeners
  if (productFilter) productFilter.addEventListener('input', filterTable);
  if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
  if (supplierFilter) supplierFilter.addEventListener('change', filterTable);
  if (dateFrom) dateFrom.addEventListener('change', filterTable);
  if (dateTo) dateTo.addEventListener('change', filterTable);
  
  // Add real-time search highlighting
  if (productFilter) {
    productFilter.addEventListener('input', function() {
      highlightSearchTerm(this.value);
    });
  }
  
  function highlightSearchTerm(term) {
    const rows = table.getElementsByClassName('return-row');
    for (let row of rows) {
      const cells = row.cells;
      // Only highlight visible rows
      if (row.style.display !== 'none') {
        // Product name cell
        const productCell = cells[2];
        if (productCell) {
          const originalText = productCell.dataset.originalText || productCell.innerHTML;
          if (!productCell.dataset.originalText) {
            productCell.dataset.originalText = originalText;
          }
          
          if (term) {
            const regex = new RegExp(`(${term})`, 'gi');
            productCell.innerHTML = originalText.replace(regex, '<mark>$1</mark>');
          } else {
            productCell.innerHTML = originalText;
          }
        }
      }
    }
  }
});

// Clear all filters function
function clearFilters() {
  document.getElementById('productFilter').value = '';
  document.getElementById('categoryFilter').value = '';
  document.getElementById('supplierFilter').value = '';
  document.getElementById('dateFrom').value = '';
  document.getElementById('dateTo').value = '';
  
  // Reset table display
  const table = document.getElementById('returnsTable');
  const tbody = table.getElementsByTagName('tbody')[0];
  const rows = tbody.getElementsByClassName('return-row');
  
  let counter = 1;
  for (let i = 0; i < rows.length; i++) {
    rows[i].style.display = '';
    rows[i].cells[0].innerHTML = '<strong>' + counter++ + '</strong>';
  }
  
  // Show footer
  const tfoot = table.getElementsByTagName('tfoot')[0];
  if (tfoot) {
    tfoot.style.display = '';
    // Reset to original total
    location.reload(); // Simple reload to reset totals
  }
  
  // Hide no results message
  const noResultsRow = document.getElementById('no-results-row');
  if (noResultsRow) {
    noResultsRow.style.display = 'none';
  }
}

// Export to Excel function (bonus feature)
function exportToExcel() {
  const table = document.getElementById('returnsTable');
  const wb = XLSX.utils.table_to_book(table, {sheet: "Returns"});
  XLSX.writeFile(wb, 'Returns_Report_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

// Print table function
function printTable() {
  window.print();
}
</script>

<?php include_once('layouts/footer.php'); ?>

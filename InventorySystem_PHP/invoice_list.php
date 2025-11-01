<?php
  $page_title = 'Invoice List';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  //extra add prabashi 
  $msg = $session->msg();

  // Handle bulk delete by date range (POST)
  if (isset($_POST['delete_range'])) {
      // sanitize inputs (assumes $db->escape exists)
      $from_date = !empty($_POST['from_date']) ? $db->escape($_POST['from_date']) : '';
      $to_date   = !empty($_POST['to_date']) ? $db->escape($_POST['to_date']) : '';

      if ($from_date && $to_date) {
          $from_dt = $from_date . ' 00:00:00';
          $to_dt   = $to_date . ' 23:59:59';

          $delete_sql = "DELETE FROM sales WHERE created_at BETWEEN '{$from_dt}' AND '{$to_dt}'";
          $delete_res = $db->query($delete_sql);

          if ($delete_res) {
              $session->msg('s', "Invoices between {$from_date} and {$to_date} deleted successfully.");
          } else {
              $session->msg('d', 'Failed to delete invoices for the given range.');
          }
      } else {
          $session->msg('d', 'Please provide both From and To dates to delete.');
      }
      redirect('invoice_list.php', false);
  }

  // Prepare filters (GET)
  $search   = isset($_GET['search']) ? $db->escape(trim($_GET['search'])) : '';
  $from_ft  = isset($_GET['from_date']) ? $db->escape($_GET['from_date']) : '';
  $to_ft    = isset($_GET['to_date']) ? $db->escape($_GET['to_date']) : '';
?>
<?php include_once('layouts/header.php'); ?>

<link rel="stylesheet" href="assets/css/professional-styles.css">

<!-- Page Header -->


<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-list-alt"></span>
          <span>Invoice List</span>
        </strong>
        <div class="pull-right">
          <a href="add_sales.php" class="btn btn-primary">
            <span class="glyphicon glyphicon-plus"></span> Add New Sale
          </a>
        </div>
      </div>
      <div class="panel-body">

        <!-- Filters / Search -->
        <div class="filter-section">
          <h4><span class="glyphicon glyphicon-filter"></span> Search & Filter</h4>
          <form class="form-inline" method="get">
            <div class="form-group">
              <label for="search">Search:</label>
              <input type="text" name="search" class="form-control" placeholder="Invoice # or Customer Name" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
              <label for="from_date">From Date:</label>
              <input type="date" id="from_date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_ft); ?>">
            </div>
            <div class="form-group">
              <label for="to_date">To Date:</label>
              <input type="date" id="to_date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_ft); ?>">
            </div>
            <div class="form-group" >
              <div class = "buttonGroup">
                  <button type="submit" class="btn btn-info">
                    <span class="glyphicon glyphicon-search"></span> Filter
                  </button>
                  <a href="invoice_list.php" class="btn btn-warning">
                    <span class="glyphicon glyphicon-refresh"></span> Clear
                  </a>
                </div>
            </div>
          </form>
           <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Invoice Number</th>
              <th>Customer Name</th>
              <th>Phone Number</th>
              <th>Total Amount</th>
              <th>Date</th>
              <th style="width: 180px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Build invoice query with optional filters
            $where_clauses = [];

            if (!empty($search)) {
                $like = $db->escape('%' . $search . '%');
                $where_clauses[] = "(s.invoice_number LIKE '{$like}' OR s.name LIKE '{$like}')";
            }

            if (!empty($from_ft) && !empty($to_ft)) {
                $from_dt = $from_ft . ' 00:00:00';
                $to_dt   = $to_ft . ' 23:59:59';
                $where_clauses[] = "(s.created_at BETWEEN '{$from_dt}' AND '{$to_dt}')";
            }

            $where_sql = '';
            if (!empty($where_clauses)) {
                $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
            }

            // Get all unique invoices with their details (with filters applied)
            $invoice_sql = "SELECT DISTINCT s.invoice_number, s.name, s.pNumber, s.created_at, 
                           SUM(s.total) as total_amount
                           FROM sales s
                           {$where_sql}
                           GROUP BY s.invoice_number, s.name, s.pNumber, s.created_at
                           ORDER BY s.created_at DESC";

            $invoice_result = $db->query($invoice_sql);
            $counter = 1;

            if ($invoice_result && $db->num_rows($invoice_result) > 0) {
                while ($invoice = $db->fetch_assoc($invoice_result)) {
                    echo "<tr>";
                    echo "<td class='text-center'>" . $counter . "</td>";
                    echo "<td>" . htmlspecialchars($invoice['invoice_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($invoice['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($invoice['pNumber']) . "</td>";
                    echo "<td>LKR " . number_format($invoice['total_amount'], 2) . "</td>";
                    echo "<td>" . date('Y-m-d H:i', strtotime($invoice['created_at'])) . "</td>";
                    echo "<td>";
                    echo "<div class='action-buttons'>";

                    // View/download invoice
                    echo "<a href='generate_invoice.php?invoice_number=" . urlencode($invoice['invoice_number']) . "' class='btn btn-info btn-xs' title='View/Download Invoice'>";
                    echo "<span class='glyphicon glyphicon-eye-open'></span> View";
                    echo "</a>";

                    // Edit invoice - points to edit_invoice.php (implement that file to allow editing)
                    echo "<a href='edit_invoice.php?invoice_number=" . urlencode($invoice['invoice_number']) . "' class='btn btn-warning btn-xs' title='Edit Invoice'>";
                    echo "<span class='glyphicon glyphicon-edit'></span> Edit";
                    echo "</a>";

                    // Delete single invoice (with confirmation)
                    echo "<a href='delete_invoice.php?invoice_number=" . urlencode($invoice['invoice_number']) . "' class='btn btn-danger btn-xs' title='Delete Invoice' onclick=\"return confirm('Delete invoice " . htmlspecialchars($invoice['invoice_number']) . "? This cannot be undone.');\">";
                    echo "<span class='glyphicon glyphicon-trash'></span> Delete";
                    echo "</a>";

                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                    $counter++;
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No invoices found</td></tr>";
            }
            ?>
          </tbody>
        </table>
        </div>

       
        
        <!-- Bulk delete form (by date range) -->
        <div class="bulk-delete-section">
          <div class="">
            <span class="glyphicon glyphicon-warning-sign"></span> Bulk Delete Operations
          </div>
          <form method="post" id="deleteRangeForm" onsubmit="return confirmDeleteRange();">
            <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($from_ft); ?>">
            <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($to_ft); ?>">
            <button type="submit" name="delete_range" class="btn btn-danger" <?php echo ($from_ft && $to_ft) ? '' : 'disabled'; ?>>
              <span class="glyphicon glyphicon-trash"></span> Delete Invoices in Date Range
            </button>
            <small class="help-block">Deletes all invoices where invoice date is between the selected From and To dates. This action cannot be undone.</small>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
// confirm bulk delete
function confirmDeleteRange() {
    var from = "<?php echo htmlspecialchars($from_ft); ?>";
    var to   = "<?php echo htmlspecialchars($to_ft); ?>";
    if (!from || !to) {
        alert('Please set both From and To dates before deleting.');
        return false;
    }
    return confirm('Are you sure you want to delete all invoices from ' + from + ' to ' + to + '? This action cannot be undone.');
}
</script>

<?php include_once('layouts/footer.php'); ?>

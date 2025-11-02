<?php
  $page_title = 'Return Reports';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
  
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
          <span class="glyphicon glyphicon-stats"></span>
          <span>Return Reports & Analytics</span>
        </strong>
        <div class="pull-right">
          <a href="returns.php" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-arrow-left"></span> Back to Returns
          </a>
        </div>
      </div>
      <div class="panel-body">
        <?php
        $stats = get_return_statistics();
        ?>
        
        <!-- Statistics Overview -->
        <div class="row">
          <div class="col-md-3">
            <div class="panel panel-primary">
              <div class="panel-body text-center">
                <h3 class="text-primary"><?php echo $stats['monthly_returns']; ?></h3>
                <p>Returns This Month</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-info">
              <div class="panel-body text-center">
                <h3 class="text-info"><?php echo count($stats['most_returned']); ?></h3>
                <p>Products with Returns</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-warning">
              <div class="panel-body text-center">
                <h3 class="text-warning"><?php echo count($stats['reason_breakdown']); ?></h3>
                <p>Return Reasons</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel panel-success">
              <div class="panel-body text-center">
                <h3 class="text-success"><?php echo count(find_active_return_alerts()); ?></h3>
                <p>Active Alerts</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Most Returned Products -->
        <div class="row">
          <div class="col-md-6">
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>
                  <span class="glyphicon glyphicon-warning-sign"></span>
                  <span>Most Returned Products</span>
                </strong>
              </div>
              <div class="panel-body">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Product Name</th>
                      <th class="text-center">Return Count</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($stats['most_returned'] as $product): ?>
                    <tr>
                      <td><?php echo remove_junk($product['name']); ?></td>
                      <td class="text-center">
                        <span class="badge badge-danger"><?php echo (int)$product['return_count']; ?></span>
                      </td>
                      <td class="text-center">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-info btn-xs">
                          <span class="glyphicon glyphicon-eye-open"></span> View
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <!-- Return Reasons Breakdown -->
          <div class="col-md-6">
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>
                  <span class="glyphicon glyphicon-list"></span>
                  <span>Return Reasons Breakdown</span>
                </strong>
              </div>
              <div class="panel-body">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Reason</th>
                      <th class="text-center">Count</th>
                      <th class="text-center">Percentage</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $total_reasons = array_sum(array_column($stats['reason_breakdown'], 'count'));
                    foreach($stats['reason_breakdown'] as $reason): 
                      $percentage = $total_reasons > 0 ? round(($reason['count'] / $total_reasons) * 100, 1) : 0;
                    ?>
                    <tr>
                      <td>
                        <span class="label label-<?php echo get_reason_label_class($reason['return_reason']); ?>">
                          <?php echo remove_junk($reason['return_reason']); ?>
                        </span>
                      </td>
                      <td class="text-center"><?php echo (int)$reason['count']; ?></td>
                      <td class="text-center"><?php echo $percentage; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Monthly Return Trends -->
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                <strong>
                  <span class="glyphicon glyphicon-signal"></span>
                  <span>Monthly Return Trends</span>
                </strong>
              </div>
              <div class="panel-body">
                <canvas id="returnTrendsChart" width="400" height="100"></canvas>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Return Alerts -->
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <strong>
                  <span class="glyphicon glyphicon-exclamation-sign"></span>
                  <span>Active Return Alerts</span>
                </strong>
              </div>
              <div class="panel-body">
                <?php
                $alerts = find_active_return_alerts();
                if($alerts):
                ?>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Alert Type</th>
                        <th>Product</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($alerts as $alert): ?>
                      <tr>
                        <td>
                          <span class="label label-<?php echo $alert['alert_type'] == 'Frequent Returns' ? 'danger' : 'warning'; ?>">
                            <?php echo remove_junk($alert['alert_type']); ?>
                          </span>
                        </td>
                        <td><?php echo remove_junk($alert['product_name']); ?></td>
                        <td><?php echo remove_junk($alert['alert_message']); ?></td>
                        <td><?php echo remove_junk($alert['alert_date']); ?></td>
                        <td>
                          <button class="btn btn-success btn-xs" onclick="resolveAlert(<?php echo $alert['id']; ?>)">
                            <span class="glyphicon glyphicon-ok"></span> Resolve
                          </button>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                  <h4><i class="glyphicon glyphicon-ok"></i> No Active Alerts</h4>
                  <p>There are currently no active return alerts.</p>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Export Options -->
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-info">
              <div class="panel-heading">
                <strong>
                  <span class="glyphicon glyphicon-download"></span>
                  <span>Export Reports</span>
                </strong>
              </div>
              <div class="panel-body">
                <div class="btn-group">
                  <a href="export_returns.php?format=pdf" class="btn btn-danger">
                    <span class="glyphicon glyphicon-file"></span> Export PDF
                  </a>
                  <a href="export_returns.php?format=excel" class="btn btn-success">
                    <span class="glyphicon glyphicon-file"></span> Export Excel
                  </a>
                  <a href="export_returns.php?format=csv" class="btn btn-info">
                    <span class="glyphicon glyphicon-file"></span> Export CSV
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.reports-dashboard {
  background: #f8f9fa;
  min-height: 100vh;
}

.panel-primary .panel-body {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 5px;
}

.panel-info .panel-body {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
  color: white;
  border-radius: 5px;
}

.panel-warning .panel-body {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
  border-radius: 5px;
}

.panel-success .panel-body {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  border-radius: 5px;
}

.badge-danger {
  background-color: #dc3545;
  font-size: 0.9em;
}

.chart-container {
  position: relative;
  height: 400px;
  width: 100%;
}

.alert-success {
  background-color: #d4edda;
  border-color: #c3e6cb;
  color: #155724;
  border-radius: 5px;
}

.btn-group .btn {
  margin-right: 10px;
  border-radius: 5px;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #495057;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize return trends chart
  const ctx = document.getElementById('returnTrendsChart').getContext('2d');
  
  // Sample data - in real implementation, this would come from PHP
  const returnTrendsChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Returns',
        data: [12, 19, 3, 5, 2, 3],
        borderColor: 'rgb(75, 192, 192)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        tension: 0.1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});

function resolveAlert(alertId) {
  if(confirm('Are you sure you want to resolve this alert?')) {
    fetch('ajax.php?action=resolve_alert&id=' + alertId, {
      method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        location.reload();
      } else {
        alert('Failed to resolve alert');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error resolving alert');
    });
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>

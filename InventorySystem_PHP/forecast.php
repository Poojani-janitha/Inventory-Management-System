<?php
$page_title = 'Forecast Data';
// include_once('layouts/header.php');
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(1);

// ---------- DATABASE CONNECTION ----------
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "inventory_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// ---------- GENERATE LAST 12 MONTHS ----------
$months = [];
$monthKeys = []; // '2025-11', '2025-10', etc.
for ($i = 11; $i >= 0; $i--) {
  $monthKeys[] = date('Y-m', strtotime("-$i month"));
  $months[] = date('M Y', strtotime("-$i month"));
}

// ---------- FETCH SALES DATA ----------
$sql = "
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') AS month, 
    SUM(total) AS total_sales
FROM sales
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC;
";
$result = $conn->query($sql);

// ---------- MAP SALES ----------
$salesMap = [];
while ($row = $result->fetch_assoc()) {
  $salesMap[$row['month']] = floatval($row['total_sales']);
}

// ---------- FILL ZEROES FOR MISSING MONTHS ----------
$salesData = [];
foreach ($monthKeys as $key) {
  $salesData[] = $salesMap[$key] ?? 0;
}

// // ---------- SOME STATIC METRICS ----------
// $salesTarget = 80.80;
// $avgSalesTarget = 600.34;
// $avgItems = 8;

// // Determine trend
// $trend = ($salesData[count($salesData)-1] > $salesData[0]) ? 'up' : 'down';

// circular
$sql_1 = "
    SELECT SUM(quantity) AS all_available_stock
    FROM product
    WHERE expire_date >= CURDATE();
";
$result1 = $conn->query($sql_1);
$all_available_stock = ($result1 && $result1->num_rows > 0) ? $result1->fetch_assoc()['all_available_stock'] : 0;


$sql_2 = "
    SELECT SUM(quantity) AS this_year_expiring_stock
    FROM product
    WHERE YEAR(expire_date) = YEAR(CURDATE())
      AND expire_date >= CURDATE();
";
$result2 = $conn->query($sql_2);
$this_year_expiring_stock = ($result2 && $result2->num_rows > 0) ? $result2->fetch_assoc()['this_year_expiring_stock'] : 0;

$percentage = ($all_available_stock > 0)
  ? round(($this_year_expiring_stock / $all_available_stock) * 100, 2)
  : 0;

$goalChange = 30;
$trendIcon = ($percentage >= $goalChange) ? '↓' : '↑';
$trendClass = ($percentage >= $goalChange) ? 'text-danger' : 'text-success';
$trendText = ($percentage >= $goalChange) ? 'Below Target' : 'On Track';

// 
$sql_3 = "
    SELECT 
        YEAR(return_date) AS year,
        SUM(return_quantity) AS total_returns
    FROM return_details
    GROUP BY YEAR(return_date)
    ORDER BY YEAR(return_date);
";

$result = $conn->query($sql_3);

// Prepare data arrays
$data = [];
$years = [];
$returns = [];

while ($row = $result->fetch_assoc()) {
  $data[(int) $row['year']] = (int) $row['total_returns'];
}

// ---------- ENSURE ALL YEARS ARE PRESENT ----------
$minYear = min(array_keys($data));
$maxYear = max(array_keys($data));

for ($y = $minYear; $y <= $maxYear; $y++) {
  $years[] = $y;
  $returns[] = $data[$y] ?? 0; // if no data, use 0
}

$sql_4 = "SELECT COUNT(s_id) AS supplier_count FROM supplier_info";

$result = $conn->query($sql_4);

if ($result && $row = $result->fetch_assoc()) {
  $kpis['total_units'] = $row['supplier_count'];
} else {
  $kpis['total_units'] = 0; // default if query fails or no data
}

$sql = "
    SELECT 
        SUM(total) AS last_month_sales_value
    FROM sales
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
      AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH);
";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
  $kpis['total_inventory_value'] = $row['last_month_sales_value'] ?? 0;
} else {
  $kpis['total_inventory_value'] = 0;
}

$sql = "
    SELECT 
        SUM(discount) AS last_month_discount_value
    FROM sales
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
      AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH);
";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
  $kpis['potential_profit'] = $row['last_month_discount_value'] ?? 0;
} else {
  $kpis['potential_profit'] = 0;
}

$kpis['total_items'] = $all_available_stock;

$conn->close();
?>
<?php include_once('layouts/header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="libs/css/forecast.css">

<div class="container my-4">

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm kpi-card border-0">
        <div class="card-body text-center">
          <h6 class="text-uppercase text-muted mb-2 fs-5">Total Products</h6>
          <h3 class="fw-bold fs-1"><?= number_format($kpis['total_items'] ?? 0) ?></h3>
          <span class="text-success fs-5">+2.5% from last month</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm kpi-card border-0">
        <div class="card-body text-center">
          <h6 class="text-uppercase text-muted mb-2 fs-5">Number Of Suppliers</h6>
          <h3 class="fw-bold fs-1"><?= number_format($kpis['total_units'] ?? 0) ?></h3>
          <span class="text-success fs-5">from last month</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm kpi-card border-0">
        <div class="card-body text-center">
          <h6 class="text-uppercase text-muted mb-2 fs-5">Inventory Value</h6>
          <h3 class="fw-bold fs-1">LKR <?= number_format($kpis['total_inventory_value'] ?? 0, 2) ?></h3>
          <span class="text-success fs-5"><?php echo date('F Y', strtotime('-1 month')); ?></span>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm kpi-card border-0">
        <div class="card-body text-center">
          <h6 class="text-uppercase text-muted mb-2 fs-5">Last Month Discount Value</h6>
          <h3 class="fw-bold fs-1">LKR <?= number_format($kpis['potential_profit'] ?? 0, 2) ?></h3>
          <span class="text-success fs-5"><?php echo date('F Y', strtotime('-1 month')); ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- SALES OVERVIEW -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-9">
      <div class="dashboard-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fs-3 fw-bold text-dark" style="margin-left:25px;color: #2c2626b1;">Sales Overview (Last 12 Months)</h4>
          <button class="btn btn-outline-secondary btn-sm fs-6" id="exportBtn">
            <i class="bi bi-download"></i> Export PNG
          </button>
        </div>

        <div class="chart-container">
          <canvas id="salesChart"></canvas>
        </div>

        <div class="mt-4 text-end text-success fs-6">
          📊 Updated: <?php echo date('M d, Y'); ?>
        </div>
      </div>
    </div>

    <!-- EXPIRING STOCK -->
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-lg text-center border-0">
        <div class="card-body">
          <h6 class="text-secondary text-uppercase mb-4 fs-4">
            <strong>This Year Expiring Stock (<?= date('Y'); ?>)</strong>
          </h6>

          <div class="circular-progress" id="progressCircle">
            <div class="value fs-1" id="progressValue">0%</div>
          </div>

          <div class="mt-3 text-secondary">
            <p class="mb-1 fs-5">All Available Stock:
              <strong><?= number_format($all_available_stock); ?></strong>
            </p>
            <p class="mb-3 fs-5">This Year Expiring Stock:
              <strong><?= number_format($this_year_expiring_stock); ?></strong>
            </p>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top fs-5">
            <div>
              Status:
              <span class="fw-bold <?= $trendClass; ?>">
                <?= $trendIcon . ' ' . $trendText; ?>
              </span>
            </div>
            <div>
              Goal <span class="fw-bold"><?= number_format($goalChange, 1); ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- RETURN QUANTITIES -->
  <div class="row g-3 mb-4">
    <div class="card shadow-sm mt-4 col-12 col-md-6 col-lg-9">
      <h4 class="mt-4 fs-3 fw-bold" style="margin-left:25px;color: #2c2626b1;">
        Yearly Return Quantities
      </h4>
      <canvas id="returnChart" height="400"></canvas>
    </div>
  </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Histogram chart
  document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const labels = <?php echo json_encode($months); ?>;
    const salesData = <?php echo json_encode($salesData); ?>;

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(13,110,253,0.4)');
    gradient.addColorStop(1, 'rgba(13,110,253,0.05)');

    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Monthly Sales (Rs)',
          data: salesData,
          borderColor: '#0d6efd',
          backgroundColor: gradient,
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 5,
          pointBackgroundColor: '#0d6efd',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (context) {
                return 'Sales: Rs ' + context.raw.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return '' + value.toLocaleString();
              }
            }
          },
          x: {
            ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 }
          }
        }
      }
    });

    // Export Button
    document.getElementById('exportBtn').addEventListener('click', function () {
      const link = document.createElement('a');
      link.href = chart.toBase64Image();
      link.download = '12-month-sales-<?php echo date('Y-m-d'); ?>.png';
      link.click();
    });
  });

  // Circular progress
  const finalPercentage = <?php echo (int) $percentage; ?>;
  const circle = document.getElementById('progressCircle');
  const valueDisplay = document.getElementById('progressValue');
  let current = 0;
  const timer = setInterval(() => {
    if (current >= finalPercentage) {
      clearInterval(timer);
      valueDisplay.textContent = finalPercentage + '%';
      return;
    }
    current++;
    valueDisplay.textContent = current + '%';
    const angle = current * 3.6;
    circle.style.background = `conic-gradient(var(--bs-primary) ${angle}deg, var(--bs-light) 0deg)`;
  }, 25);

  // Return chart
  const returnCtx = document.getElementById('returnChart').getContext('2d');
  const returnChart = new Chart(returnCtx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($years); ?>,
      datasets: [{
        label: 'Return Quantity',
        data: <?php echo json_encode($returns); ?>,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        fill: true,
        tension: 0,
        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
        pointRadius: 6,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, position: 'top' },
        title: { display: true, text: 'Yearly Return Quantities', font: { size: 16, weight: 'bold' } },
        tooltip: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function (context) {
              return 'Return Quantity: ' + context.formattedValue;
            }
          }
        }
      },
      scales: {
        x: { title: { display: true, text: 'Year', font: { size: 14, weight: 'bold' } }, grid: { color: '#e0e0e0' } },
        y: { title: { display: true, text: 'Return Quantity', font: { size: 14, weight: 'bold' } }, beginAtZero: true, grid: { color: '#e0e0e0' } }
      }
    }
  });
</script>

<?php include_once('layouts/footer.php'); ?>
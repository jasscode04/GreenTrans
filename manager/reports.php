<?php
/**
 * GreenTrans - Manager Reports
 */
require_once __DIR__ . '/../config/config.php';
requireRole('manager');
define('PAGE_TITLE', 'Reports');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Driver.php';

$shipmentModel = new Shipment();
$driverModel = new Driver();

$deliveryStats = $shipmentModel->getDeliveryStats();
$monthlyData = $shipmentModel->getMonthlyRevenue();
$driverPerf = $driverModel->getPerformanceStats();

$revenueChartData = array_fill(0, 12, 0);
foreach ($monthlyData as $m) { $revenueChartData[$m['month'] - 1] = (float)$m['revenue']; }

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1><i class="bi bi-file-earmark-bar-graph" style="color:var(--gt-primary)"></i> Reports</h1>
            <p>Operations analytics and performance reports</p>
        </div>
        <div>
            <a href="<?= APP_URL ?>/api/export-excel.php" class="btn-gt-primary">
                <i class="bi bi-file-earmark-excel"></i> Export to Excel
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="gt-chart-card animate-slide-up">
                <div class="chart-header"><h6 class="chart-title"><i class="bi bi-graph-up text-success me-2"></i>Revenue Trend</h6></div>
                <div style="height:300px"><canvas id="revenueChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="gt-chart-card animate-slide-up delay-1">
                <div class="chart-header"><h6 class="chart-title"><i class="bi bi-pie-chart text-primary me-2"></i>Deliveries</h6></div>
                <div style="height:300px"><canvas id="deliveryChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="gt-section-card animate-slide-up">
        <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-people text-info me-2"></i>Driver Performance</h6>
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Driver</th><th>Total</th><th>Completed</th><th>Rate</th><th>Earnings</th></tr></thead>
                <tbody>
                    <?php foreach ($driverPerf as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['full_name']) ?></strong></td>
                        <td><?= $d['total_deliveries'] ?></td>
                        <td><?= $d['completed'] ?></td>
                        <td><?= $d['total_deliveries'] > 0 ? round(($d['completed']/$d['total_deliveries'])*100) : 0 ?>%</td>
                        <td><strong><?= formatIndianCurrency($d['total_earnings']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<script>
    window.revenueData = <?= json_encode($revenueChartData) ?>;
    window.deliveryData = [<?= $deliveryStats['delivered'] ?? 0 ?>, <?= $deliveryStats['in_transit'] ?? 0 ?>, <?= $deliveryStats['pending'] ?? 0 ?>, <?= $deliveryStats['picked_up'] ?? 0 ?>, <?= $deliveryStats['cancelled'] ?? 0 ?>];
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>

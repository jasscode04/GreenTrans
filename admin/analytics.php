<?php
/**
 * GreenTrans - Admin Analytics
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

define('PAGE_TITLE', 'Analytics & Reports');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Vehicle.php';
require_once CLASSES_PATH . 'Driver.php';
require_once CLASSES_PATH . 'User.php';

$shipmentModel = new Shipment();
$vehicleModel = new Vehicle();
$driverModel = new Driver();
$userModel = new User();

$monthlyData = $shipmentModel->getMonthlyRevenue();
$deliveryStats = $shipmentModel->getDeliveryStats();
$driverPerf = $driverModel->getPerformanceStats();
$vehicleStats = $vehicleModel->getUtilizationStats();

$totalRevenue = $shipmentModel->getTotalRevenue();
$monthlyRevenue = $shipmentModel->getTotalRevenue('monthly');

$revenueChartData = array_fill(0, 12, 0);
$shipmentChartData = array_fill(0, 12, 0);
foreach ($monthlyData as $m) {
    $revenueChartData[$m['month'] - 1] = (float)$m['revenue'];
    $shipmentChartData[$m['month'] - 1] = (int)$m['total_shipments'];
}

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1><i class="bi bi-graph-up-arrow" style="color:var(--gt-primary)"></i> Analytics & Reports</h1>
            <p>Comprehensive business intelligence dashboard</p>
        </div>
        <div>
            <a href="<?= APP_URL ?>/api/export-excel.php" class="btn-gt-primary">
                <i class="bi bi-file-earmark-excel"></i> Export to Excel
            </a>
        </div>
    </div>

    <!-- Revenue KPIs -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-green animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-currency-rupee"></i></div>
                <div class="kpi-value"><?= formatIndianCurrency($totalRevenue) ?></div>
                <div class="kpi-label">Total Revenue</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-blue animate-slide-up delay-2">
                <div class="kpi-icon"><i class="bi bi-calendar-month"></i></div>
                <div class="kpi-value"><?= formatIndianCurrency($monthlyRevenue) ?></div>
                <div class="kpi-label">This Month</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-orange animate-slide-up delay-3">
                <div class="kpi-icon"><i class="bi bi-check-circle"></i></div>
                <div class="kpi-value"><?= $deliveryStats['total'] > 0 ? round(($deliveryStats['delivered']/$deliveryStats['total'])*100) : 0 ?>%</div>
                <div class="kpi-label">Delivery Rate</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-purple animate-slide-up delay-4">
                <div class="kpi-icon"><i class="bi bi-truck"></i></div>
                <div class="kpi-value"><?= $vehicleStats['total'] > 0 ? round((($vehicleStats['total'] - $vehicleStats['available'])/$vehicleStats['total'])*100) : 0 ?>%</div>
                <div class="kpi-label">Fleet Utilization</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="gt-chart-card animate-slide-up">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-graph-up text-success me-2"></i>Monthly Revenue</h6>
                </div>
                <div style="height:350px"><canvas id="revenueChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="gt-chart-card animate-slide-up delay-1">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-pie-chart text-primary me-2"></i>Delivery Breakdown</h6>
                </div>
                <div style="height:350px"><canvas id="deliveryChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="gt-chart-card animate-slide-up">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-people text-info me-2"></i>Driver Performance</h6>
                </div>
                <div style="height:300px"><canvas id="driverPerformanceChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="gt-chart-card animate-slide-up delay-1">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-activity text-warning me-2"></i>Monthly Shipments</h6>
                </div>
                <div style="height:300px"><canvas id="shipmentTrendChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Driver Leaderboard -->
    <div class="gt-section-card animate-slide-up">
        <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-trophy text-warning me-2"></i>Driver Leaderboard</h6>
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>#</th><th>Driver</th><th>Total Deliveries</th><th>Completed</th><th>Completion Rate</th><th>Earnings</th></tr></thead>
                <tbody>
                    <?php foreach ($driverPerf as $i => $d): ?>
                    <tr>
                        <td>
                            <?php if ($i < 3): ?>
                            <span style="font-size:1.2rem"><?= ['🥇','🥈','🥉'][$i] ?></span>
                            <?php else: ?>
                            <?= $i + 1 ?>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($d['full_name']) ?></strong></td>
                        <td><?= $d['total_deliveries'] ?></td>
                        <td><?= $d['completed'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:80px;height:6px;background:var(--gt-bg-tertiary);border-radius:3px;overflow:hidden">
                                    <div style="height:100%;width:<?= $d['total_deliveries'] > 0 ? round(($d['completed']/$d['total_deliveries'])*100) : 0 ?>%;background:var(--gt-gradient-primary);border-radius:3px"></div>
                                </div>
                                <span style="font-size:0.85rem;font-weight:600"><?= $d['total_deliveries'] > 0 ? round(($d['completed']/$d['total_deliveries'])*100) : 0 ?>%</span>
                            </div>
                        </td>
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
    window.trendData = <?= json_encode($shipmentChartData) ?>;
    <?php if (!empty($driverPerf)): ?>
    window.driverLabels = <?= json_encode(array_column(array_slice($driverPerf, 0, 5), 'full_name')) ?>;
    window.driverCompleted = <?= json_encode(array_map('intval', array_column(array_slice($driverPerf, 0, 5), 'completed'))) ?>;
    window.driverPending = <?= json_encode(array_map(function($d) { return $d['total_deliveries'] - $d['completed']; }, array_slice($driverPerf, 0, 5))) ?>;
    <?php endif; ?>
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>

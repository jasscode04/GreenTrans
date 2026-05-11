<?php
/**
 * GreenTrans - Admin Dashboard
 * Enterprise-level admin panel with analytics
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

define('PAGE_TITLE', 'Admin Dashboard');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'User.php';
require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Vehicle.php';
require_once CLASSES_PATH . 'Driver.php';

$userModel = new User();
$shipmentModel = new Shipment();
$vehicleModel = new Vehicle();
$driverModel = new Driver();

// Dashboard KPIs
$totalUsers = $userModel->countByRole();
$totalCustomers = $userModel->countByRole('customer');
$totalDrivers = $userModel->countByRole('driver');
$totalVehicles = $vehicleModel->countByStatus();
$activeVehicles = $vehicleModel->countByStatus('available');

$totalRevenue = $shipmentModel->getTotalRevenue();
$monthlyRevenue = $shipmentModel->getTotalRevenue('monthly');
$deliveryStats = $shipmentModel->getDeliveryStats();
$recentShipments = $shipmentModel->getRecent(5);
$monthlyData = $shipmentModel->getMonthlyRevenue();
$driverPerf = $driverModel->getPerformanceStats();

// Prepare chart data
$revenueChartData = array_fill(0, 12, 0);
foreach ($monthlyData as $m) {
    $revenueChartData[$m['month'] - 1] = (float)$m['revenue'];
}

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>

<!-- Main Content -->
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">

    <!-- Page Header -->
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-grid-1x2-fill" style="color:var(--gt-primary)"></i> Admin Dashboard</h1>
        <p>Overview of your transport & logistics operations</p>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-green animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-currency-rupee"></i></div>
                <div class="kpi-value" data-count="<?= (int)$totalRevenue ?>" data-prefix="₹"><?= formatIndianCurrency($totalRevenue) ?></div>
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-change up"><i class="bi bi-arrow-up"></i> 12.5%</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-blue animate-slide-up delay-2">
                <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
                <div class="kpi-value" data-count="<?= $deliveryStats['total'] ?? 0 ?>"><?= $deliveryStats['total'] ?? 0 ?></div>
                <div class="kpi-label">Total Shipments</div>
                <div class="kpi-change up"><i class="bi bi-arrow-up"></i> 8.3%</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-orange animate-slide-up delay-3">
                <div class="kpi-icon"><i class="bi bi-truck"></i></div>
                <div class="kpi-value" data-count="<?= $totalVehicles ?>"><?= $totalVehicles ?></div>
                <div class="kpi-label">Fleet Vehicles</div>
                <span class="badge-gt badge-available"><?= $activeVehicles ?> Active</span>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-purple animate-slide-up delay-4">
                <div class="kpi-icon"><i class="bi bi-people"></i></div>
                <div class="kpi-value" data-count="<?= $totalUsers ?>"><?= $totalUsers ?></div>
                <div class="kpi-label">Total Users</div>
                <span class="badge-gt badge-available"><?= $totalDrivers ?> Drivers</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="gt-chart-card animate-slide-up">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-graph-up-arrow text-success me-2"></i>Revenue Overview</h6>
                    <span class="badge-gt badge-available">This Year</span>
                </div>
                <div style="height:320px">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="gt-chart-card animate-slide-up delay-1">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Delivery Status</h6>
                </div>
                <div style="height:320px">
                    <canvas id="deliveryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="gt-chart-card animate-slide-up">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-people-fill text-info me-2"></i>Driver Performance</h6>
                </div>
                <div style="height:280px">
                    <canvas id="driverPerformanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="gt-chart-card animate-slide-up delay-1">
                <div class="chart-header">
                    <h6 class="chart-title"><i class="bi bi-activity text-warning me-2"></i>Shipment Trends</h6>
                </div>
                <div style="height:280px">
                    <canvas id="shipmentTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats + Recent Shipments -->
    <div class="row g-4 mb-4">
        <!-- Delivery Breakdown -->
        <div class="col-lg-4">
            <div class="gt-section-card animate-slide-up">
                <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Quick Stats</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:rgba(16,185,129,0.06)">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(16,185,129,0.15);display:flex;align-items:center;justify-content:center;color:#10b981">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <span style="font-weight:600;font-size:0.9rem">Delivered</span>
                        </div>
                        <strong style="font-size:1.2rem"><?= $deliveryStats['delivered'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:rgba(99,102,241,0.06)">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;color:#6366f1">
                                <i class="bi bi-truck"></i>
                            </div>
                            <span style="font-weight:600;font-size:0.9rem">In Transit</span>
                        </div>
                        <strong style="font-size:1.2rem"><?= $deliveryStats['in_transit'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:rgba(245,158,11,0.06)">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(245,158,11,0.15);display:flex;align-items:center;justify-content:center;color:#f59e0b">
                                <i class="bi bi-clock"></i>
                            </div>
                            <span style="font-weight:600;font-size:0.9rem">Pending</span>
                        </div>
                        <strong style="font-size:1.2rem"><?= $deliveryStats['pending'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:rgba(239,68,68,0.06)">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(239,68,68,0.15);display:flex;align-items:center;justify-content:center;color:#ef4444">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <span style="font-weight:600;font-size:0.9rem">Cancelled</span>
                        </div>
                        <strong style="font-size:1.2rem"><?= $deliveryStats['cancelled'] ?? 0 ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="col-lg-8">
            <div class="gt-section-card animate-slide-up delay-1">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 style="font-weight:700"><i class="bi bi-clock-history text-success me-2"></i>Recent Shipments</h6>
                    <a href="<?= APP_URL ?>/admin/shipments.php" class="btn-gt-outline" style="padding:6px 16px;font-size:0.8rem">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="gt-table">
                        <thead>
                            <tr>
                                <th>Tracking ID</th>
                                <th>Customer</th>
                                <th>Route</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentShipments as $s): ?>
                            <tr>
                                <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                                <td><?= htmlspecialchars($s['customer_name'] ?? 'N/A') ?></td>
                                <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                                <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_', ' ', $s['status'])) ?></span></td>
                                <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentShipments)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No shipments found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div> <!-- end gt-content -->

<!-- Chart Data -->
<script>
    window.revenueData = <?= json_encode($revenueChartData) ?>;
    window.deliveryData = [
        <?= $deliveryStats['delivered'] ?? 0 ?>,
        <?= $deliveryStats['in_transit'] ?? 0 ?>,
        <?= $deliveryStats['pending'] ?? 0 ?>,
        <?= $deliveryStats['picked_up'] ?? 0 ?>,
        <?= $deliveryStats['cancelled'] ?? 0 ?>
    ];
    <?php if (!empty($driverPerf)): ?>
    window.driverLabels = <?= json_encode(array_column(array_slice($driverPerf, 0, 5), 'full_name')) ?>;
    window.driverCompleted = <?= json_encode(array_map('intval', array_column(array_slice($driverPerf, 0, 5), 'completed'))) ?>;
    window.driverPending = <?= json_encode(array_map(function($d) { return $d['total_deliveries'] - $d['completed']; }, array_slice($driverPerf, 0, 5))) ?>;
    <?php endif; ?>
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>

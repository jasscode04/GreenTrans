<?php
/**
 * GreenTrans - Transport Manager Dashboard
 */
require_once __DIR__ . '/../config/config.php';
requireRole('manager');

define('PAGE_TITLE', 'Manager Dashboard');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Vehicle.php';
require_once CLASSES_PATH . 'Driver.php';

$shipmentModel = new Shipment();
$vehicleModel = new Vehicle();
$driverModel = new Driver();

$activeVehicles = $vehicleModel->countByStatus('available');
$inTransit = $shipmentModel->countByStatus('in_transit');
$delayed = $shipmentModel->countByStatus('pending');
$revenue = $shipmentModel->getTotalRevenue('monthly');
$recentShipments = $shipmentModel->getRecent(5);
$vehicleStats = $vehicleModel->getUtilizationStats();
$drivers = $driverModel->getAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-grid-1x2-fill" style="color:var(--gt-primary)"></i> Manager Dashboard</h1>
        <p>Fleet operations & delivery monitoring</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-green animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-truck"></i></div>
                <div class="kpi-value"><?= $activeVehicles ?></div>
                <div class="kpi-label">Active Vehicles</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-blue animate-slide-up delay-2">
                <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
                <div class="kpi-value"><?= $inTransit ?></div>
                <div class="kpi-label">Deliveries In Progress</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-orange animate-slide-up delay-3">
                <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="kpi-value"><?= $delayed ?></div>
                <div class="kpi-label">Pending Shipments</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-purple animate-slide-up delay-4">
                <div class="kpi-icon"><i class="bi bi-currency-rupee"></i></div>
                <div class="kpi-value"><?= formatIndianCurrency($revenue) ?></div>
                <div class="kpi-label">Monthly Revenue</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Fleet Utilization -->
        <div class="col-lg-5">
            <div class="gt-section-card animate-slide-up">
                <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-truck text-primary me-2"></i>Fleet Overview</h6>
                <div class="d-flex flex-column gap-3">
                    <?php
                    $fleetItems = [
                        ['label' => 'Available', 'count' => $vehicleStats['available'] ?? 0, 'color' => '#10b981', 'icon' => 'bi-check-circle-fill'],
                        ['label' => 'In Transit', 'count' => $vehicleStats['in_transit'] ?? 0, 'color' => '#6366f1', 'icon' => 'bi-truck'],
                        ['label' => 'Maintenance', 'count' => $vehicleStats['maintenance'] ?? 0, 'color' => '#f59e0b', 'icon' => 'bi-tools'],
                        ['label' => 'Retired', 'count' => $vehicleStats['retired'] ?? 0, 'color' => '#ef4444', 'icon' => 'bi-x-circle'],
                    ];
                    foreach ($fleetItems as $fi):
                    ?>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:<?= $fi['color'] ?>10">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi <?= $fi['icon'] ?>" style="color:<?= $fi['color'] ?>;font-size:1.2rem"></i>
                            <span style="font-weight:600"><?= $fi['label'] ?></span>
                        </div>
                        <strong style="font-size:1.2rem"><?= $fi['count'] ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Driver Status -->
        <div class="col-lg-7">
            <div class="gt-section-card animate-slide-up delay-1">
                <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-people-fill text-success me-2"></i>Driver Status</h6>
                <div class="table-responsive">
                    <table class="gt-table">
                        <thead><tr><th>Driver</th><th>Location</th><th>Status</th><th>Deliveries</th></tr></thead>
                        <tbody>
                            <?php foreach (array_slice($drivers, 0, 5) as $d): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($d['full_name']) ?></strong></td>
                                <td style="font-size:0.85rem"><?= htmlspecialchars($d['current_location'] ?? 'Unknown') ?></td>
                                <td>
                                    <span class="badge-gt <?= ($d['is_available'] ?? 0) ? 'badge-available' : 'badge-inactive' ?>">
                                        <?= ($d['is_available'] ?? 0) ? 'Available' : 'Busy' ?>
                                    </span>
                                </td>
                                <td><?= $driverModel->getDeliveryCount($d['id']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Shipments -->
    <div class="gt-section-card animate-slide-up">
        <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-clock-history text-warning me-2"></i>Recent Shipments</h6>
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Tracking ID</th><th>Customer</th><th>Route</th><th>Driver</th><th>Status</th><th>Amount</th></tr></thead>
                <tbody>
                    <?php foreach ($recentShipments as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td><?= htmlspecialchars($s['customer_name'] ?? 'N/A') ?></td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td><?= htmlspecialchars($s['driver_name'] ?? '<em class="text-muted">Unassigned</em>') ?></td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

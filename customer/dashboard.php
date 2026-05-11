<?php
/**
 * GreenTrans - Customer Dashboard
 */
require_once __DIR__ . '/../config/config.php';
requireRole('customer');

define('PAGE_TITLE', 'Customer Dashboard');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'Shipment.php';

$shipmentModel = new Shipment();
$userId = $_SESSION['user_id'];

$activeShipments = $shipmentModel->countByStatus('in_transit', $userId, 'customer') + $shipmentModel->countByStatus('picked_up', $userId, 'customer');
$completedDeliveries = $shipmentModel->countByStatus('delivered', $userId, 'customer');
$pendingDeliveries = $shipmentModel->countByStatus('pending', $userId, 'customer');

$pdo = getDBConnection();
$totalSpent = $pdo->query("SELECT COALESCE(SUM(shipping_cost),0) FROM shipments WHERE customer_id = $userId AND payment_status = 'paid'")->fetchColumn();

$recentShipments = $shipmentModel->getByCustomer($userId);

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-grid-1x2-fill" style="color:var(--gt-primary)"></i> My Dashboard</h1>
        <p>Track your shipments and delivery status</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-blue animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
                <div class="kpi-value"><?= $activeShipments ?></div>
                <div class="kpi-label">Active Shipments</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-green animate-slide-up delay-2">
                <div class="kpi-icon"><i class="bi bi-check-circle"></i></div>
                <div class="kpi-value"><?= $completedDeliveries ?></div>
                <div class="kpi-label">Completed Deliveries</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-orange animate-slide-up delay-3">
                <div class="kpi-icon"><i class="bi bi-clock"></i></div>
                <div class="kpi-value"><?= $pendingDeliveries ?></div>
                <div class="kpi-label">Pending Deliveries</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-purple animate-slide-up delay-4">
                <div class="kpi-icon"><i class="bi bi-currency-rupee"></i></div>
                <div class="kpi-value"><?= formatIndianCurrency($totalSpent) ?></div>
                <div class="kpi-label">Total Spent</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <a href="<?= APP_URL ?>/customer/book-shipment.php" class="neo-card d-block text-center animate-slide-up" style="border-left:4px solid var(--gt-primary)">
                <i class="bi bi-plus-circle-fill" style="font-size:2.5rem;color:var(--gt-primary)"></i>
                <h5 class="mt-3 mb-1" style="font-weight:700">Book Shipment</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem">Create a new transport booking</p>
            </a>
        </div>
        <div class="col-lg-4">
            <a href="<?= APP_URL ?>/customer/track-shipment.php" class="neo-card d-block text-center animate-slide-up delay-1" style="border-left:4px solid #6366f1">
                <i class="bi bi-geo-alt-fill" style="font-size:2.5rem;color:#6366f1"></i>
                <h5 class="mt-3 mb-1" style="font-weight:700">Track Shipment</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem">Track your active deliveries</p>
            </a>
        </div>
        <div class="col-lg-4">
            <a href="<?= APP_URL ?>/customer/support.php" class="neo-card d-block text-center animate-slide-up delay-2" style="border-left:4px solid #f59e0b">
                <i class="bi bi-headset" style="font-size:2.5rem;color:#f59e0b"></i>
                <h5 class="mt-3 mb-1" style="font-weight:700">Get Support</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem">Raise a support ticket</p>
            </a>
        </div>
    </div>

    <!-- Recent Shipments -->
    <div class="gt-section-card animate-slide-up">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 style="font-weight:700"><i class="bi bi-clock-history text-success me-2"></i>My Shipments</h6>
            <a href="<?= APP_URL ?>/customer/history.php" class="btn-gt-outline" style="padding:6px 16px;font-size:0.8rem">View All</a>
        </div>
        <div class="table-responsive">
            <table class="gt-table">
                <thead>
                    <tr><th>Tracking ID</th><th>Route</th><th>Driver</th><th>Status</th><th>Amount</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($recentShipments, 0, 5) as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td><?= htmlspecialchars($s['driver_name'] ?? 'Not Assigned') ?></td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentShipments)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No shipments yet. <a href="<?= APP_URL ?>/customer/book-shipment.php" style="color:var(--gt-primary)">Book your first shipment!</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

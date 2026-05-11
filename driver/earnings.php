<?php
/**
 * GreenTrans - Driver Earnings
 */
require_once __DIR__ . '/../config/config.php';
requireRole('driver');
define('PAGE_TITLE', 'Earnings');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'Driver.php';
$driverModel = new Driver();
$userId = $_SESSION['user_id'];

$totalEarnings = $driverModel->getEarnings($userId);
$monthlyEarnings = $driverModel->getEarnings($userId, 'monthly');
$totalDeliveries = $driverModel->getDeliveryCount($userId, 'delivered');

$pdo = getDBConnection();
$deliveryEarnings = $pdo->prepare("SELECT d.*, s.tracking_id, s.pickup_city, s.delivery_city FROM deliveries d JOIN shipments s ON d.shipment_id = s.id WHERE d.driver_id = ? AND d.status = 'delivered' ORDER BY d.created_at DESC");
$deliveryEarnings->execute([$userId]);
$deliveryEarnings = $deliveryEarnings->fetchAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';

// Check for approval
if (!($_SESSION['user_approved'] ?? 0)): ?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-section-card text-center animate-slide-up" style="padding: 80px 20px;">
        <i class="bi bi-lock-fill text-muted" style="font-size: 3rem;"></i>
        <h2 class="mt-3">Access Restricted</h2>
        <p class="text-muted">Verification required to view earnings data.</p>
    </div>
</div>
<?php include INCLUDES_PATH . 'footer.php'; exit(); endif; ?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-wallet2" style="color:var(--gt-primary)"></i> Earnings</h1>
        <p>Track your delivery earnings</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="kpi-card kpi-green animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-currency-rupee"></i></div>
                <div class="kpi-value"><?= formatIndianCurrency($totalEarnings) ?></div>
                <div class="kpi-label">Total Earnings</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-blue animate-slide-up delay-2">
                <div class="kpi-icon"><i class="bi bi-calendar-month"></i></div>
                <div class="kpi-value"><?= formatIndianCurrency($monthlyEarnings) ?></div>
                <div class="kpi-label">This Month</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-purple animate-slide-up delay-3">
                <div class="kpi-icon"><i class="bi bi-check-circle"></i></div>
                <div class="kpi-value"><?= $totalDeliveries ?></div>
                <div class="kpi-label">Completed Deliveries</div>
            </div>
        </div>
    </div>

    <div class="gt-section-card animate-slide-up">
        <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-receipt text-success me-2"></i>Earnings Breakdown</h6>
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Shipment</th><th>Route</th><th>Distance</th><th>Earnings</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($deliveryEarnings as $de): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $de['tracking_id'] ?></strong></td>
                        <td style="font-size:0.85rem"><?= $de['pickup_city'] ?> → <?= $de['delivery_city'] ?></td>
                        <td><?= $de['distance_km'] ?> KM</td>
                        <td><strong style="color:var(--gt-success)"><?= formatIndianCurrency($de['earnings']) ?></strong></td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($de['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($deliveryEarnings)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No earnings yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

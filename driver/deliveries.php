<?php
/**
 * GreenTrans - Driver Deliveries
 */
require_once __DIR__ . '/../config/config.php';
requireRole('driver');
define('PAGE_TITLE', 'My Deliveries');

require_once CLASSES_PATH . 'Shipment.php';
$shipmentModel = new Shipment();
$shipments = $shipmentModel->getByDriver($_SESSION['user_id']);

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
        <p class="text-muted">Verification required to view deliveries.</p>
    </div>
</div>
<?php include INCLUDES_PATH . 'footer.php'; exit(); endif; ?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-box-seam-fill" style="color:var(--gt-primary)"></i> My Deliveries</h1>
        <p>View all assigned delivery tasks</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Tracking ID</th><th>Customer</th><th>Route</th><th>Weight</th><th>Status</th><th>Amount</th></tr></thead>
                <tbody>
                    <?php foreach ($shipments as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td>
                            <div style="font-weight:600"><?= htmlspecialchars($s['customer_name'] ?? 'N/A') ?></div>
                            <small class="text-muted"><?= $s['customer_phone'] ?? '' ?></small>
                        </td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td><?= $s['weight_kg'] ?> KG</td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($shipments)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No deliveries assigned</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

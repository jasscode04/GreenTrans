<?php
/**
 * GreenTrans - Customer History
 */
require_once __DIR__ . '/../config/config.php';
requireRole('customer');
define('PAGE_TITLE', 'Delivery History');

require_once CLASSES_PATH . 'Shipment.php';
$shipmentModel = new Shipment();
$shipments = $shipmentModel->getByCustomer($_SESSION['user_id']);

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-clock-history" style="color:var(--gt-primary)"></i> Delivery History</h1>
        <p>View all your past and current shipments</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Tracking ID</th><th>Route</th><th>Package</th><th>Weight</th><th>Status</th><th>Amount</th><th>Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($shipments as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td><?= ucfirst($s['package_type']) ?></td>
                        <td><?= $s['weight_kg'] ?> KG</td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                        <td><a href="track-shipment.php?id=<?= $s['tracking_id'] ?>" class="btn-gt-outline" style="padding:4px 12px;font-size:0.8rem">Track</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($shipments)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No shipments yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

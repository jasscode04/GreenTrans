<?php
/**
 * GreenTrans - Track Shipment (Customer)
 */
require_once __DIR__ . '/../config/config.php';
requireRole('customer');

define('PAGE_TITLE', 'Track Shipment');

require_once CLASSES_PATH . 'Shipment.php';
$shipmentModel = new Shipment();

$shipment = null;
$tracking = [];
$trackingId = $_GET['id'] ?? '';

if ($trackingId) {
    $shipment = $shipmentModel->getByTrackingId($trackingId);
    if ($shipment) {
        $tracking = $shipmentModel->getTrackingHistory($shipment['id']);
    }
}

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-geo-alt-fill" style="color:var(--gt-primary)"></i> Track Shipment</h1>
        <p>Enter your tracking ID to see delivery progress</p>
    </div>

    <!-- Search Box -->
    <div class="gt-section-card animate-slide-up" style="max-width:600px;margin:0 auto 32px">
        <form method="GET" class="d-flex gap-3">
            <input type="text" name="id" class="gt-input" placeholder="Enter Tracking ID (e.g., GT20260001)" value="<?= htmlspecialchars($trackingId) ?>" style="flex:1">
            <button type="submit" class="btn-gt-primary" style="padding:14px 28px">
                <i class="bi bi-search"></i> Track
            </button>
        </form>
    </div>

    <?php if ($trackingId && !$shipment): ?>
    <div class="gt-section-card text-center animate-slide-up">
        <i class="bi bi-search" style="font-size:3rem;color:var(--gt-text-muted)"></i>
        <h5 class="mt-3">Shipment Not Found</h5>
        <p class="text-muted">No shipment found with tracking ID: <strong><?= htmlspecialchars($trackingId) ?></strong></p>
    </div>
    <?php elseif ($shipment): ?>

    <!-- Shipment Info -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="gt-section-card animate-slide-up">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div>
                        <h5 style="font-weight:700;margin-bottom:4px">Shipment <?= $shipment['tracking_id'] ?></h5>
                        <span class="text-muted" style="font-size:0.85rem">Created: <?= date('d M Y, h:i A', strtotime($shipment['created_at'])) ?></span>
                    </div>
                    <span class="badge-gt badge-<?= $shipment['status'] ?>" style="font-size:0.85rem;padding:6px 16px">
                        <?= ucwords(str_replace('_',' ',$shipment['status'])) ?>
                    </span>
                </div>

                <!-- Progress Bar -->
                <?php
                $statusSteps = ['pending', 'picked_up', 'in_transit', 'delivered'];
                $currentIdx = array_search($shipment['status'], $statusSteps);
                if ($currentIdx === false) $currentIdx = 0;
                $progress = ($currentIdx / (count($statusSteps)-1)) * 100;
                ?>
                <div style="margin-bottom:32px">
                    <div style="height:6px;background:var(--gt-bg-tertiary);border-radius:3px;position:relative;overflow:hidden">
                        <div style="height:100%;width:<?= $progress ?>%;background:var(--gt-gradient-primary);border-radius:3px;transition:width 1s ease"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <?php foreach (['Pending','Picked Up','In Transit','Delivered'] as $i => $label): ?>
                        <span style="font-size:0.75rem;font-weight:<?= $i <= $currentIdx ? '700' : '500' ?>;color:<?= $i <= $currentIdx ? 'var(--gt-primary)' : 'var(--gt-text-muted)' ?>"><?= $label ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Route Info -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:rgba(var(--gt-primary-rgb),0.05)">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-geo-alt-fill text-success"></i>
                                <strong style="font-size:0.85rem">PICKUP</strong>
                            </div>
                            <div style="font-size:0.9rem"><?= htmlspecialchars($shipment['pickup_address']) ?></div>
                            <div class="text-muted" style="font-size:0.85rem"><?= $shipment['pickup_city'] ?> - <?= $shipment['pickup_pincode'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:rgba(99,102,241,0.05)">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-pin-map-fill text-primary"></i>
                                <strong style="font-size:0.85rem">DELIVERY</strong>
                            </div>
                            <div style="font-size:0.9rem"><?= htmlspecialchars($shipment['delivery_address']) ?></div>
                            <div class="text-muted" style="font-size:0.85rem"><?= $shipment['delivery_city'] ?> - <?= $shipment['delivery_pincode'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipment Details -->
        <div class="col-lg-4">
            <div class="gt-section-card animate-slide-up delay-1">
                <h6 style="font-weight:700;margin-bottom:16px">Shipment Details</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between"><span class="text-muted">Package</span><strong><?= ucfirst($shipment['package_type']) ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Weight</span><strong><?= $shipment['weight_kg'] ?> KG</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Priority</span><strong><?= ucfirst($shipment['priority']) ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Driver</span><strong><?= htmlspecialchars($shipment['driver_name'] ?? 'Not Assigned') ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Vehicle</span><strong><?= $shipment['vehicle_number'] ?? 'N/A' ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">ETA</span><strong><?= $shipment['estimated_delivery'] ? date('d M Y', strtotime($shipment['estimated_delivery'])) : 'TBD' ?></strong></div>
                    <hr style="border-color:var(--gt-border-color);margin:4px 0">
                    <div class="d-flex justify-content-between"><span style="font-weight:700">Cost</span><strong style="color:var(--gt-primary);font-size:1.1rem"><?= formatIndianCurrency($shipment['shipping_cost']) ?></strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Timeline -->
    <div class="gt-section-card animate-slide-up">
        <h6 style="font-weight:700;margin-bottom:24px"><i class="bi bi-clock-history text-primary me-2"></i>Tracking Timeline</h6>
        <div class="gt-timeline">
            <?php foreach (array_reverse($tracking) as $i => $t): ?>
            <div class="gt-timeline-item <?= $i === 0 ? 'active' : 'completed' ?>">
                <div style="font-weight:700;font-size:0.9rem"><?= ucwords(str_replace('_',' ',$t['status'])) ?></div>
                <div class="text-muted" style="font-size:0.85rem"><?= htmlspecialchars($t['location'] ?? '') ?> — <?= htmlspecialchars($t['remarks'] ?? '') ?></div>
                <div class="text-muted" style="font-size:0.8rem"><?= date('d M Y, h:i A', strtotime($t['created_at'])) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<?php include INCLUDES_PATH . 'footer.php'; ?>

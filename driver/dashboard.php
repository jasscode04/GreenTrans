<?php
/**
 * GreenTrans - Driver Dashboard
 */
require_once __DIR__ . '/../config/config.php';
requireRole('driver');

define('PAGE_TITLE', 'Driver Dashboard');
define('LOAD_CHART_JS', true);

require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Driver.php';

$shipmentModel = new Shipment();
$driverModel = new Driver();
$userId = $_SESSION['user_id'];

$todayDeliveries = $shipmentModel->countByStatus('in_transit', $userId, 'driver') + $shipmentModel->countByStatus('picked_up', $userId, 'driver');
$completedDeliveries = $driverModel->getDeliveryCount($userId, 'delivered');
$pendingDeliveries = $shipmentModel->countByStatus('pending', $userId, 'driver');
$totalEarnings = $driverModel->getEarnings($userId);
$monthlyEarnings = $driverModel->getEarnings($userId, 'monthly');

$assignedShipments = $shipmentModel->getByDriver($userId);

// Get availability status
$pdo = getDBConnection();
$availability = $pdo->query("SELECT is_available FROM driver_availability WHERE driver_id = $userId")->fetch();
$isAvailable = $availability ? $availability['is_available'] : 0;

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';

// Check for approval
$isApproved = $_SESSION['user_approved'] ?? 0;

if (!$isApproved): ?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-section-card text-center animate-slide-up" style="padding: 80px 20px; border: 1px dashed var(--gt-primary); background: rgba(var(--gt-primary-rgb), 0.02);">
        <div class="mb-4">
            <div style="width:100px; height:100px; background: rgba(var(--gt-primary-rgb), 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--gt-primary); font-size: 3rem;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
        <h2 style="font-weight:800; margin-bottom:15px">Verification in Progress</h2>
        <p class="text-muted mx-auto" style="max-width: 500px; font-size: 1.1rem; line-height: 1.7">
            Your driver account has been created successfully! Our team is currently reviewing your **Driving License** and **ID Proof** for verification. 
        </p>
        
        <div class="mt-4 p-3 d-inline-block" style="background:var(--gt-bg-tertiary); border-radius:12px; font-weight:600">
            <i class="bi bi-info-circle-fill text-primary me-2"></i> Status: <span class="badge-gt badge-pending">Pending Approval</span>
        </div>

        <!-- Upload Documents Section -->
        <div class="mt-5 mx-auto" style="max-width: 450px; text-align: left; background: #fff; padding: 30px; border-radius: 20px; box-shadow: var(--gt-shadow-sm);">
            <h5 class="mb-4" style="font-weight:700;"><i class="bi bi-cloud-upload text-primary me-2"></i> Update Documents</h5>
            <form action="<?= APP_URL ?>/api/upload-driver-docs.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Driving License Image</label>
                    <input type="file" name="driving_license" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">ID Proof Image</label>
                    <input type="file" name="id_proof" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn-gt-primary w-100">
                    <i class="bi bi-upload me-2"></i> Upload & Submit for Review
                </button>
            </form>
        </div>

        <p class="mt-4 text-muted small">You will receive full access to your dashboard once the admin approves your documents.</p>
        <a href="<?= APP_URL ?>/auth/logout.php" class="btn-gt-outline mt-3">Sign Out</a>
    </div>
</div>
<?php include INCLUDES_PATH . 'footer.php'; exit(); endif; ?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-grid-1x2-fill" style="color:var(--gt-primary)"></i> Driver Dashboard</h1>
                <p>Manage your deliveries and track earnings</p>
            </div>
            <!-- Availability Toggle -->
            <div class="d-flex align-items-center gap-3">
                <span style="font-weight:600;font-size:0.9rem">Availability:</span>
                <div class="form-check form-switch" style="transform:scale(1.4)">
                    <input class="form-check-input" type="checkbox" id="availabilityToggle" <?= $isAvailable ? 'checked' : '' ?>
                           style="cursor:pointer;<?= $isAvailable ? 'background-color:var(--gt-primary);border-color:var(--gt-primary)' : '' ?>">
                </div>
                <span class="badge-gt <?= $isAvailable ? 'badge-available' : 'badge-inactive' ?>" id="availabilityStatus">
                    <?= $isAvailable ? 'Online' : 'Offline' ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-blue animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-truck"></i></div>
                <div class="kpi-value"><?= $todayDeliveries ?></div>
                <div class="kpi-label">Today's Deliveries</div>
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
                <div class="kpi-value"><?= formatIndianCurrency($totalEarnings) ?></div>
                <div class="kpi-label">Total Earnings</div>
                <div class="kpi-change up"><i class="bi bi-arrow-up"></i> This month: <?= formatIndianCurrency($monthlyEarnings) ?></div>
            </div>
        </div>
    </div>

    <!-- Assigned Deliveries -->
    <div class="gt-section-card animate-slide-up">
        <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-box-seam-fill text-primary me-2"></i>Assigned Deliveries</h6>
        <div class="table-responsive">
            <table class="gt-table">
                <thead>
                    <tr><th>Tracking ID</th><th>Customer</th><th>Route</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($assignedShipments, 0, 10) as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td>
                            <div style="font-weight:600"><?= htmlspecialchars($s['customer_name'] ?? 'N/A') ?></div>
                            <small class="text-muted"><?= $s['customer_phone'] ?? '' ?></small>
                        </td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td>
                            <?php if ($s['status'] !== 'delivered' && $s['status'] !== 'cancelled'): ?>
                            <button class="btn-gt-primary" style="padding:6px 14px;font-size:0.8rem;border-radius:8px" onclick="updateStatus(<?= $s['id'] ?>)">
                                Update Status
                            </button>
                            <?php else: ?>
                            <span class="text-muted" style="font-size:0.85rem">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($assignedShipments)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No deliveries assigned yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<script>
// Availability toggle
document.getElementById('availabilityToggle')?.addEventListener('change', function() {
    fetch('<?= APP_URL ?>/api/driver-availability.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({driver_id: <?= $userId ?>})
    }).then(r => r.json()).then(data => {
        const badge = document.getElementById('availabilityStatus');
        if (data.is_available) {
            badge.className = 'badge-gt badge-available';
            badge.textContent = 'Online';
            this.style.backgroundColor = 'var(--gt-primary)';
            GTToast.success('You are now online');
        } else {
            badge.className = 'badge-gt badge-inactive';
            badge.textContent = 'Offline';
            this.style.backgroundColor = '';
            GTToast.warning('You are now offline');
        }
    });
});

function updateStatus(shipmentId) {
    const status = prompt('Enter new status:\n1. picked_up\n2. in_transit\n3. delivered');
    if (status && ['picked_up', 'in_transit', 'delivered'].includes(status)) {
        window.location.href = '<?= APP_URL ?>/api/shipment-status.php?id=' + shipmentId + '&status=' + status;
    }
}
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>

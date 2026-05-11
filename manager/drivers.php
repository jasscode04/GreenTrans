<?php
/**
 * GreenTrans - Manager Drivers
 */
require_once __DIR__ . '/../config/config.php';
requireRole('manager');
define('PAGE_TITLE', 'Driver Management');

require_once CLASSES_PATH . 'Driver.php';
$driverModel = new Driver();
$drivers = $driverModel->getAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-people-fill" style="color:var(--gt-primary)"></i> Driver Management</h1>
        <p>Monitor driver status and performance</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Driver</th><th>Phone</th><th>Location</th><th>Status</th><th>Deliveries</th><th>Earnings</th></tr></thead>
                <tbody>
                    <?php foreach ($drivers as $d): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= UPLOADS_URL ?>/profiles/<?= htmlspecialchars($d['profile_image']) ?>" class="gt-avatar" alt=""
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($d['full_name']) ?>&background=10b981&color=fff&size=40'">
                                <strong><?= htmlspecialchars($d['full_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($d['phone']) ?></td>
                        <td><?= htmlspecialchars($d['current_location'] ?? 'Unknown') ?></td>
                        <td><span class="badge-gt <?= ($d['is_available'] ?? 0) ? 'badge-available' : 'badge-inactive' ?>"><?= ($d['is_available'] ?? 0) ? 'Available' : 'Busy' ?></span></td>
                        <td><?= $driverModel->getDeliveryCount($d['id']) ?></td>
                        <td><strong><?= formatIndianCurrency($driverModel->getEarnings($d['id'])) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

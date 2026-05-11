<?php
/**
 * GreenTrans - Admin Driver Approvals
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

define('PAGE_TITLE', 'Driver Approvals');

require_once CLASSES_PATH . 'User.php';
$userModel = new User();
$pdo = getDBConnection();

// Handle approval
if (isset($_GET['approve'])) {
    $userModel->approveDriver((int)$_GET['approve']);
    setFlash('success', 'Driver approved successfully');
    redirect(APP_URL . '/admin/driver-approvals.php');
}

// Fetch unapproved drivers
$pendingDrivers = $pdo->query("SELECT * FROM users WHERE role = 'driver' AND is_approved = 0 ORDER BY created_at DESC")->fetchAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-shield-check" style="color:var(--gt-primary)"></i> Driver Approvals</h1>
        <p>Review and verify new driver documents</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <?php if (empty($pendingDrivers)): ?>
        <div class="gt-empty-state">
            <i class="bi bi-check-all"></i>
            <h5>All Clear!</h5>
            <p class="text-muted">No pending driver approvals at the moment.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="gt-table">
                <thead>
                    <tr>
                        <th>Driver Details</th>
                        <th>Documents</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingDrivers as $d): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <div style="font-weight:700; font-size:1rem"><?= htmlspecialchars($d['full_name']) ?></div>
                                    <div class="text-muted small"><?= $d['email'] ?></div>
                                    <div class="text-muted small"><?= $d['phone'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if (isset($d['driving_license']) && $d['driving_license']): ?>
                                <a href="<?= APP_URL ?>/uploads/documents/<?= $d['driving_license'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem">
                                    <i class="bi bi-card-heading"></i> License
                                </a>
                                <?php else: ?>
                                <span class="badge bg-light text-muted border">No License</span>
                                <?php endif; ?>

                                <?php if (isset($d['id_proof']) && $d['id_proof']): ?>
                                <a href="<?= APP_URL ?>/uploads/documents/<?= $d['id_proof'] ?>" target="_blank" class="btn btn-sm btn-outline-info" style="font-size:0.75rem">
                                    <i class="bi bi-person-badge"></i> ID Proof
                                </a>
                                <?php else: ?>
                                <span class="badge bg-light text-muted border">No ID Proof</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:0.85rem"><?= date('d M Y', strtotime($d['created_at'])) ?></div>
                            <div class="text-muted" style="font-size:0.75rem"><?= timeAgo($d['created_at']) ?></div>
                        </td>
                        <td>
                            <a href="?approve=<?= $d['id'] ?>" class="btn-gt-primary" 
                               style="padding:6px 16px; font-size:0.85rem; border-radius:8px" 
                               onclick="return confirm('Verify and approve this driver?')">
                                <i class="bi bi-check2"></i> Approve
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

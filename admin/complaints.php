<?php
/**
 * GreenTrans - Admin Complaints
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');
define('PAGE_TITLE', 'Complaints');

$pdo = getDBConnection();
$complaints = $pdo->query("SELECT c.*, u.full_name as user_name, s.tracking_id FROM complaints c LEFT JOIN users u ON c.user_id = u.id LEFT JOIN shipments s ON c.shipment_id = s.id ORDER BY c.created_at DESC")->fetchAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-chat-square-text-fill" style="color:var(--gt-primary)"></i> Complaint Management</h1>
        <p>Track and resolve customer complaints</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <?php if (empty($complaints)): ?>
        <div class="gt-empty-state">
            <i class="bi bi-chat-square-check"></i>
            <h5>No Complaints</h5>
            <p class="text-muted">All clear! No complaints filed yet.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>ID</th><th>User</th><th>Category</th><th>Shipment</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td><strong><?= $c['complaint_number'] ?></strong></td>
                        <td><?= htmlspecialchars($c['user_name'] ?? 'Unknown') ?></td>
                        <td><span class="badge-gt badge-pending"><?= ucwords(str_replace('_',' ',$c['category'])) ?></span></td>
                        <td><?= $c['tracking_id'] ?? 'N/A' ?></td>
                        <td><span class="badge-gt badge-<?= $c['status'] === 'resolved' ? 'delivered' : 'pending' ?>"><?= ucfirst($c['status']) ?></span></td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

<?php
/**
 * GreenTrans - Admin User Management
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

define('PAGE_TITLE', 'User Management');

require_once CLASSES_PATH . 'User.php';
$userModel = new User();

// Handle toggle status
if (isset($_GET['toggle'])) {
    $userModel->toggleStatus((int)$_GET['toggle']);
    setFlash('success', 'User status updated');
    redirect(APP_URL . '/admin/users.php');
}

$roleFilter = $_GET['role'] ?? '';
$users = $userModel->getAll($roleFilter ?: null);

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-people-fill" style="color:var(--gt-primary)"></i> User Management</h1>
                <p>Manage all registered users</p>
            </div>
        </div>
    </div>

    <!-- Role Filter Tabs -->
    <div class="d-flex gap-2 mb-4 flex-wrap animate-slide-up">
        <a href="?" class="btn-gt-<?= !$roleFilter ? 'primary' : 'outline' ?>" style="padding:8px 20px;font-size:0.85rem">All</a>
        <a href="?role=customer" class="btn-gt-<?= $roleFilter==='customer' ? 'primary' : 'outline' ?>" style="padding:8px 20px;font-size:0.85rem">Customers</a>
        <a href="?role=driver" class="btn-gt-<?= $roleFilter==='driver' ? 'primary' : 'outline' ?>" style="padding:8px 20px;font-size:0.85rem">Drivers</a>
        <a href="?role=manager" class="btn-gt-<?= $roleFilter==='manager' ? 'primary' : 'outline' ?>" style="padding:8px 20px;font-size:0.85rem">Managers</a>
        <a href="?role=admin" class="btn-gt-<?= $roleFilter==='admin' ? 'primary' : 'outline' ?>" style="padding:8px 20px;font-size:0.85rem">Admins</a>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead>
                    <tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= UPLOADS_URL ?>/profiles/<?= htmlspecialchars($u['profile_image']) ?>" 
                                     class="gt-avatar" alt="" 
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($u['full_name']) ?>&background=10b981&color=fff&size=40'">
                                <strong><?= htmlspecialchars($u['full_name']) ?></strong>
                            </div>
                        </td>
                        <td style="font-size:0.85rem"><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone']) ?></td>
                        <td><span class="badge-gt badge-available" style="text-transform:capitalize"><?= $u['role'] ?></span></td>
                        <td>
                            <span class="badge-gt <?= $u['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                               style="font-size:0.8rem;border-radius:8px" onclick="return confirm('Toggle user status?')">
                                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

<?php
/**
 * GreenTrans - Admin Settings
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');
define('PAGE_TITLE', 'System Settings');

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8);
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([sanitize($value), $settingKey]);
        }
    }
    setFlash('success', 'Settings updated successfully');
    redirect(APP_URL . '/admin/settings.php');
}

$settings = $pdo->query("SELECT * FROM settings ORDER BY setting_group, setting_key")->fetchAll();
$grouped = [];
foreach ($settings as $s) { $grouped[$s['setting_group']][] = $s; }

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-gear-fill" style="color:var(--gt-primary)"></i> System Settings</h1>
        <p>Configure application parameters</p>
    </div>

    <form method="POST">
        <?php foreach ($grouped as $group => $items): ?>
        <div class="gt-section-card animate-slide-up mb-4">
            <h6 style="font-weight:700;margin-bottom:20px;text-transform:capitalize">
                <i class="bi bi-sliders me-2" style="color:var(--gt-primary)"></i><?= $group ?> Settings
            </h6>
            <div class="row g-3">
                <?php foreach ($items as $item): ?>
                <div class="col-md-6">
                    <div class="gt-form-group">
                        <label class="gt-label"><?= ucwords(str_replace('_', ' ', $item['setting_key'])) ?></label>
                        <input type="text" name="setting_<?= $item['setting_key'] ?>" class="gt-input" value="<?= htmlspecialchars($item['setting_value']) ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-gt-primary"><i class="bi bi-check-circle"></i> Save Settings</button>
    </form>
<?php include INCLUDES_PATH . 'footer.php'; ?>

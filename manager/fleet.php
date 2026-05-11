<?php
/**
 * GreenTrans - Manager Fleet
 */
require_once __DIR__ . '/../config/config.php';
requireRole('manager');
define('PAGE_TITLE', 'Fleet Management');

require_once CLASSES_PATH . 'Vehicle.php';
$vehicleModel = new Vehicle();
$vehicles = $vehicleModel->getAll();
$stats = $vehicleModel->getUtilizationStats();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-truck" style="color:var(--gt-primary)"></i> Fleet Management</h1>
        <p>Monitor and manage all fleet vehicles</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="kpi-card kpi-green animate-slide-up"><div class="kpi-value"><?= $stats['total'] ?? 0 ?></div><div class="kpi-label">Total</div></div></div>
        <div class="col-md-3"><div class="kpi-card kpi-blue animate-slide-up delay-1"><div class="kpi-value"><?= $stats['available'] ?? 0 ?></div><div class="kpi-label">Available</div></div></div>
        <div class="col-md-3"><div class="kpi-card kpi-purple animate-slide-up delay-2"><div class="kpi-value"><?= $stats['in_transit'] ?? 0 ?></div><div class="kpi-label">In Transit</div></div></div>
        <div class="col-md-3"><div class="kpi-card kpi-orange animate-slide-up delay-3"><div class="kpi-value"><?= $stats['maintenance'] ?? 0 ?></div><div class="kpi-label">Maintenance</div></div></div>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Vehicle</th><th>Type</th><th>Capacity</th><th>Driver</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($v['vehicle_number']) ?></strong><br><small class="text-muted"><?= $v['brand'] ?> <?= $v['model'] ?></small></td>
                        <td><?= ucwords(str_replace('_',' ',$v['vehicle_type'])) ?></td>
                        <td><?= number_format($v['capacity_kg']) ?> KG</td>
                        <td><?= htmlspecialchars($v['driver_name'] ?? 'Unassigned') ?></td>
                        <td><span class="badge-gt badge-<?= $v['status'] ?>"><?= ucwords(str_replace('_',' ',$v['status'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

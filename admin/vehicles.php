<?php
/**
 * GreenTrans - Admin Vehicle Management
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

define('PAGE_TITLE', 'Vehicle Management');

require_once CLASSES_PATH . 'Vehicle.php';
require_once CLASSES_PATH . 'Driver.php';

$vehicleModel = new Vehicle();
$driverModel = new Driver();

// Handle add vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $vehicleModel->create([
        'vehicle_number' => sanitize($_POST['vehicle_number']),
        'vehicle_type' => sanitize($_POST['vehicle_type']),
        'brand' => sanitize($_POST['brand']),
        'model' => sanitize($_POST['model']),
        'capacity_kg' => (float)$_POST['capacity_kg'],
        'fuel_type' => sanitize($_POST['fuel_type']),
    ]);
    setFlash('success', 'Vehicle added successfully');
    redirect(APP_URL . '/admin/vehicles.php');
}

// Handle status update
if (isset($_GET['status_update']) && isset($_GET['vid'])) {
    $vehicleModel->updateStatus((int)$_GET['vid'], sanitize($_GET['status_update']));
    setFlash('success', 'Vehicle status updated');
    redirect(APP_URL . '/admin/vehicles.php');
}

$vehicles = $vehicleModel->getAll();
$availableDrivers = $driverModel->getAvailable();
$stats = $vehicleModel->getUtilizationStats();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-truck" style="color:var(--gt-primary)"></i> Vehicle Management</h1>
                <p>Manage fleet vehicles</p>
            </div>
            <button class="btn-gt-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <i class="bi bi-plus-circle"></i> Add Vehicle
            </button>
        </div>
    </div>

    <!-- Fleet Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="kpi-card kpi-green animate-slide-up"><div class="kpi-value"><?= $stats['total'] ?? 0 ?></div><div class="kpi-label">Total Vehicles</div></div></div>
        <div class="col-md-3"><div class="kpi-card kpi-blue animate-slide-up delay-1"><div class="kpi-value"><?= $stats['available'] ?? 0 ?></div><div class="kpi-label">Available</div></div></div>
        <div class="col-md-3"><div class="kpi-card kpi-purple animate-slide-up delay-2"><div class="kpi-value"><?= $stats['in_transit'] ?? 0 ?></div><div class="kpi-label">In Transit</div></div></div>
        <div class="col-md-3"><div class="kpi-card kpi-orange animate-slide-up delay-3"><div class="kpi-value"><?= $stats['maintenance'] ?? 0 ?></div><div class="kpi-label">Maintenance</div></div></div>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Vehicle</th><th>Type</th><th>Capacity</th><th>Fuel</th><th>Driver</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                    <tr>
                        <td>
                            <div><strong><?= htmlspecialchars($v['vehicle_number']) ?></strong></div>
                            <small class="text-muted"><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></small>
                        </td>
                        <td><span class="badge-gt badge-available"><?= ucwords(str_replace('_',' ',$v['vehicle_type'])) ?></span></td>
                        <td><?= number_format($v['capacity_kg']) ?> KG</td>
                        <td style="text-transform:uppercase;font-size:0.85rem"><?= $v['fuel_type'] ?></td>
                        <td><?= htmlspecialchars($v['driver_name'] ?? '<em class="text-muted">Unassigned</em>') ?></td>
                        <td><span class="badge-gt badge-<?= $v['status'] ?>"><?= ucwords(str_replace('_',' ',$v['status'])) ?></span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" style="font-size:0.8rem;border-radius:8px" data-bs-toggle="dropdown">Status</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?vid=<?= $v['id'] ?>&status_update=available">Available</a></li>
                                    <li><a class="dropdown-item" href="?vid=<?= $v['id'] ?>&status_update=maintenance">Maintenance</a></li>
                                    <li><a class="dropdown-item" href="?vid=<?= $v['id'] ?>&status_update=retired">Retired</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--gt-radius-lg);border:none">
            <div class="modal-header" style="border-bottom:1px solid var(--gt-border-color)">
                <h5 class="modal-title" style="font-weight:700">Add New Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="gt-form-group"><label class="gt-label">Vehicle Number</label><input type="text" name="vehicle_number" class="gt-input" placeholder="MH12AB1234" required></div>
                    <div class="gt-form-group"><label class="gt-label">Vehicle Type</label>
                        <select name="vehicle_type" class="gt-input" required>
                            <option value="truck">Truck</option><option value="mini_truck">Mini Truck</option>
                            <option value="van">Van</option><option value="trailer">Trailer</option><option value="tempo">Tempo</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><div class="gt-form-group"><label class="gt-label">Brand</label><input type="text" name="brand" class="gt-input" placeholder="Tata"></div></div>
                        <div class="col-6"><div class="gt-form-group"><label class="gt-label">Model</label><input type="text" name="model" class="gt-input" placeholder="Prima"></div></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><div class="gt-form-group"><label class="gt-label">Capacity (KG)</label><input type="number" name="capacity_kg" class="gt-input" placeholder="16000"></div></div>
                        <div class="col-6"><div class="gt-form-group"><label class="gt-label">Fuel Type</label>
                            <select name="fuel_type" class="gt-input"><option value="diesel">Diesel</option><option value="petrol">Petrol</option><option value="cng">CNG</option><option value="electric">Electric</option></select>
                        </div></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--gt-border-color)">
                    <button type="button" class="btn-gt-outline" data-bs-dismiss="modal" style="padding:8px 20px">Cancel</button>
                    <button type="submit" class="btn-gt-primary" style="padding:8px 20px">Add Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

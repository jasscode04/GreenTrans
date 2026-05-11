<?php
/**
 * GreenTrans - Admin Shipments Management
 */
require_once __DIR__ . '/../config/config.php';
requireRole('admin');
define('PAGE_TITLE', 'Shipments');

require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Driver.php';
require_once CLASSES_PATH . 'Vehicle.php';

$shipmentModel = new Shipment();
$driverModel = new Driver();
$vehicleModel = new Vehicle();

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $shipmentModel->assignDriverVehicle((int)$_POST['shipment_id'], (int)$_POST['driver_id'], (int)$_POST['vehicle_id']);
    $shipmentModel->updateStatus((int)$_POST['shipment_id'], 'picked_up', '', 'Assigned to driver', $_SESSION['user_id']);
    setFlash('success', 'Driver & vehicle assigned');
    redirect(APP_URL . '/admin/shipments.php');
}

$statusFilter = $_GET['status'] ?? '';
$filters = $statusFilter ? ['status' => $statusFilter] : [];
$shipments = $shipmentModel->getAll($filters);
$drivers = $driverModel->getAvailable();
$vehicles = $vehicleModel->getAvailable();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-box-seam-fill" style="color:var(--gt-primary)"></i> Shipment Management</h1>
        <p>Monitor and manage all shipments</p>
    </div>

    <!-- Status Filters -->
    <div class="d-flex gap-2 mb-4 flex-wrap animate-slide-up">
        <?php foreach (['' => 'All', 'pending' => 'Pending', 'picked_up' => 'Picked Up', 'in_transit' => 'In Transit', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label): ?>
        <a href="?status=<?= $val ?>" class="btn-gt-<?= $statusFilter === $val ? 'primary' : 'outline' ?>" style="padding:8px 18px;font-size:0.85rem"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Tracking ID</th><th>Customer</th><th>Route</th><th>Type</th><th>Driver</th><th>Status</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($shipments as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td><?= htmlspecialchars($s['customer_name'] ?? 'N/A') ?></td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td style="font-size:0.85rem"><?= ucfirst($s['package_type']) ?></td>
                        <td><?= htmlspecialchars($s['driver_name'] ?? '<em class="text-muted">Unassigned</em>') ?></td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                        <td>
                            <?php if ($s['status'] === 'pending' && !$s['driver_id']): ?>
                            <button class="btn-gt-primary" style="padding:5px 12px;font-size:0.8rem;border-radius:8px" 
                                    data-bs-toggle="modal" data-bs-target="#assignModal<?= $s['id'] ?>">Assign</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- Assign Modal -->
                    <?php if ($s['status'] === 'pending' && !$s['driver_id']): ?>
                    <div class="modal fade" id="assignModal<?= $s['id'] ?>" tabindex="-1">
                        <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--gt-radius-lg);border:none">
                            <div class="modal-header"><h6 class="modal-title" style="font-weight:700">Assign Driver — <?= $s['tracking_id'] ?></h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                            <form method="POST"><input type="hidden" name="assign" value="1"><input type="hidden" name="shipment_id" value="<?= $s['id'] ?>">
                                <div class="modal-body">
                                    <div class="gt-form-group"><label class="gt-label">Driver</label><select name="driver_id" class="gt-input" required>
                                        <option value="">Select Driver</option>
                                        <?php foreach ($drivers as $d): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['full_name']) ?> (<?= $d['current_location'] ?? 'N/A' ?>)</option><?php endforeach; ?>
                                    </select></div>
                                    <div class="gt-form-group"><label class="gt-label">Vehicle</label><select name="vehicle_id" class="gt-input" required>
                                        <option value="">Select Vehicle</option>
                                        <?php foreach ($vehicles as $v): ?><option value="<?= $v['id'] ?>"><?= $v['vehicle_number'] ?> (<?= ucwords(str_replace('_',' ',$v['vehicle_type'])) ?>)</option><?php endforeach; ?>
                                    </select></div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn-gt-primary">Assign</button></div>
                            </form>
                        </div></div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

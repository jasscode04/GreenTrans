<?php
/**
 * GreenTrans - Manager Shipments
 */
require_once __DIR__ . '/../config/config.php';
requireRole('manager');
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
    $shipmentModel->updateStatus((int)$_POST['shipment_id'], 'picked_up', '', 'Assigned by manager', $_SESSION['user_id']);
    setFlash('success', 'Shipment assigned');
    redirect(APP_URL . '/manager/shipments.php');
}

$shipments = $shipmentModel->getAll();
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
        <p>Assign drivers and manage shipments</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Tracking ID</th><th>Customer</th><th>Route</th><th>Driver</th><th>Status</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($shipments as $s): ?>
                    <tr>
                        <td><strong style="color:var(--gt-primary)"><?= $s['tracking_id'] ?></strong></td>
                        <td><?= htmlspecialchars($s['customer_name'] ?? 'N/A') ?></td>
                        <td style="font-size:0.85rem"><?= $s['pickup_city'] ?> → <?= $s['delivery_city'] ?></td>
                        <td><?= htmlspecialchars($s['driver_name'] ?? '<em class="text-muted">Unassigned</em>') ?></td>
                        <td><span class="badge-gt badge-<?= $s['status'] ?>"><?= ucwords(str_replace('_',' ',$s['status'])) ?></span></td>
                        <td><strong><?= formatIndianCurrency($s['shipping_cost']) ?></strong></td>
                        <td>
                            <?php if ($s['status'] === 'pending' && !$s['driver_id']): ?>
                            <button class="btn-gt-primary" style="padding:5px 12px;font-size:0.8rem;border-radius:8px" data-bs-toggle="modal" data-bs-target="#assignM<?= $s['id'] ?>">Assign</button>
                            <!-- Modal -->
                            <div class="modal fade" id="assignM<?= $s['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="border-radius:var(--gt-radius-lg);border:none">
                                <div class="modal-header"><h6 class="modal-title" style="font-weight:700">Assign — <?= $s['tracking_id'] ?></h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                                <form method="POST"><input type="hidden" name="assign" value="1"><input type="hidden" name="shipment_id" value="<?= $s['id'] ?>">
                                    <div class="modal-body">
                                        <div class="gt-form-group"><label class="gt-label">Driver</label><select name="driver_id" class="gt-input" required><option value="">Select</option><?php foreach($drivers as $d): ?><option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['full_name']) ?></option><?php endforeach; ?></select></div>
                                        <div class="gt-form-group"><label class="gt-label">Vehicle</label><select name="vehicle_id" class="gt-input" required><option value="">Select</option><?php foreach($vehicles as $v): ?><option value="<?= $v['id'] ?>"><?= $v['vehicle_number'] ?></option><?php endforeach; ?></select></div>
                                    </div>
                                    <div class="modal-footer"><button type="submit" class="btn-gt-primary">Assign</button></div>
                                </form>
                            </div></div></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

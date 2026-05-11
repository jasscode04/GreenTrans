<?php
/**
 * GreenTrans - Shipment Status API
 */
require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/auth/login.php');
}

require_once CLASSES_PATH . 'Shipment.php';
require_once CLASSES_PATH . 'Notification.php';

$shipmentModel = new Shipment();
$notifModel = new Notification();

$id = (int)($_GET['id'] ?? 0);
$status = sanitize($_GET['status'] ?? '');

if ($id && in_array($status, ['picked_up', 'in_transit', 'delivered', 'cancelled'])) {
    $shipment = $shipmentModel->getById($id);
    
    if ($shipment) {
        $location = $status === 'delivered' ? $shipment['delivery_city'] : ($shipment['pickup_city'] ?? '');
        $remarks = ucwords(str_replace('_', ' ', $status));
        
        $shipmentModel->updateStatus($id, $status, $location, $remarks, $_SESSION['user_id']);
        
        // Notify customer
        $notifModel->create(
            $shipment['customer_id'],
            'Shipment ' . ucwords(str_replace('_', ' ', $status)),
            "Your shipment {$shipment['tracking_id']} is now " . str_replace('_', ' ', $status) . ".",
            'shipment'
        );
        
        // If delivered, update delivery record
        if ($status === 'delivered') {
            $pdo = getDBConnection();
            $pdo->prepare("UPDATE deliveries SET status = 'delivered', delivery_time = NOW() WHERE shipment_id = ?")->execute([$id]);
        }
        
        setFlash('success', 'Status updated to ' . ucwords(str_replace('_', ' ', $status)));
    }
}

// Redirect back
$role = getUserRole();
redirect(APP_URL . "/$role/dashboard.php");

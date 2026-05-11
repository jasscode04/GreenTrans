<?php
/**
 * GreenTrans - Driver Availability API
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn() || getUserRole() !== 'driver') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once CLASSES_PATH . 'Driver.php';
$driverModel = new Driver();

$input = json_decode(file_get_contents('php://input'), true);
$driverId = $input['driver_id'] ?? $_SESSION['user_id'];

$newStatus = $driverModel->toggleAvailability($driverId);

echo json_encode([
    'success' => true,
    'is_available' => $newStatus,
    'message' => $newStatus ? 'You are now available' : 'You are now offline'
]);

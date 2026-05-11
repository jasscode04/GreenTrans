<?php
/**
 * GreenTrans - Mark Notifications as Read
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'Notification.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$notifModel = new Notification();
$notifModel->markAllRead($_SESSION['user_id']);

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode(['success' => true]);
} else {
    redirect($_SERVER['HTTP_REFERER'] ?? APP_URL);
}

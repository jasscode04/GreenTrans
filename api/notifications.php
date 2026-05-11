<?php
/**
 * GreenTrans - Notifications API
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once CLASSES_PATH . 'Notification.php';
$notifModel = new Notification();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        $notifications = $notifModel->getByUser($_SESSION['user_id'], 20);
        $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['notifications' => $notifications, 'unread_count' => $unread]);
        break;
        
    case 'mark_read':
        $id = (int)($_GET['id'] ?? 0);
        if ($id) $notifModel->markAsRead($id);
        echo json_encode(['success' => true]);
        break;
        
    case 'mark_all_read':
        $notifModel->markAllRead($_SESSION['user_id']);
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

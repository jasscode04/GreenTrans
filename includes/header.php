<?php
/**
 * GreenTrans - Common Header Include
 * Used across all dashboard pages
 */
if (!defined('PAGE_TITLE')) define('PAGE_TITLE', 'GreenTrans');

// Global notification logic
require_once CLASSES_PATH . 'Notification.php';
$notifModel = new Notification();
$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $unreadCount = $notifModel->getUnreadCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GreenTrans - Smart Transport & Logistics Management System">
    <title><?= PAGE_TITLE ?> | GreenTrans</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- GreenTrans CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/dashboard.css">
    
    <!-- Theme JS (load early to prevent flash) -->
    <script src="<?= APP_URL ?>/assets/js/theme.js"></script>
</head>
<body>
<?php
// Flash message data attribute
$flash = getFlash();
if ($flash): ?>
<div data-flash="<?= htmlspecialchars($flash['message']) ?>" data-flash-type="<?= $flash['type'] ?>" style="display:none"></div>
<?php endif; ?>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay"></div>

<div class="gt-layout">

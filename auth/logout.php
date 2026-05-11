<?php
/**
 * GreenTrans - Logout Handler
 */
require_once __DIR__ . '/../config/config.php';

if (isLoggedIn()) {
    require_once CLASSES_PATH . 'User.php';
    $user = new User();
    $user->logActivity($_SESSION['user_id'], 'logout', 'Logged out');
    $user->logout();
}

redirect(APP_URL . '/auth/login.php');

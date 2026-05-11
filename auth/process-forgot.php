<?php
/**
 * GreenTrans - Process Forgot Password
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/auth/forgot-password.php');
}

$email = sanitize($_POST['email'] ?? '');

if (empty($email) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    redirect(APP_URL . '/auth/forgot-password.php?error=' . urlencode('Please enter a valid Gmail address'));
}

$user = new User();
$result = $user->generateResetToken($email);

if ($result['success']) {
    // In production, send email with reset link
    // For now, show success message
    redirect(APP_URL . '/auth/forgot-password.php?success=' . urlencode('Password reset instructions have been sent to your email.'));
} else {
    redirect(APP_URL . '/auth/forgot-password.php?error=' . urlencode($result['message']));
}

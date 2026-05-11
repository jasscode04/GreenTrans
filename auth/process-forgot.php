<?php
/**
 * GreenTrans - Process Forgot Password
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'User.php';
require_once CLASSES_PATH . 'Mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/auth/forgot-password.php');
}

$email = sanitize($_POST['email'] ?? '');

if (empty($email)) {
    redirect(APP_URL . '/auth/forgot-password.php?error=' . urlencode('Email is required'));
}

$user = new User();
$userData = $user->getByEmail($email);

if ($userData) {
    $otp = $user->setOTP($email, 'reset');
    $mailer = new Mailer();
    
    if ($mailer->sendPasswordReset($email, $otp, $userData['full_name'])) {
        $_SESSION['pending_email'] = $email;
        $_SESSION['otp_type'] = 'reset';
        redirect(APP_URL . '/auth/verify-otp.php');
    } else {
        redirect(APP_URL . '/auth/forgot-password.php?error=' . urlencode('Failed to send reset email. Please try again.'));
    }
} else {
    // For security, don't reveal if email exists or not
    // But for this project, we can show a general message
    redirect(APP_URL . '/auth/forgot-password.php?error=' . urlencode('No account found with this email.'));
}

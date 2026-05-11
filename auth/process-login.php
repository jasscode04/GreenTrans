<?php
/**
 * GreenTrans - Process Login
 * Backend authentication handler
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/auth/login.php');
}

$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Backend Validation
$errors = [];

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    $errors[] = 'Only Gmail accounts are allowed';
}

if (empty($password)) {
    $errors[] = 'Password is required';
}

if (!empty($errors)) {
    redirect(APP_URL . '/auth/login.php?error=' . urlencode(implode('. ', $errors)));
}

// Attempt login
$user = new User();
$result = $user->login($email, $password);

if ($result['success']) {
    $user->logActivity($result['user']['id'], 'login', 'Logged in successfully');
    
    // Redirect based on role
    $role = $result['user']['role'];
    redirect(APP_URL . "/$role/dashboard.php");
} else {
    redirect(APP_URL . '/auth/login.php?error=' . urlencode($result['message']));
}

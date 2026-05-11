<?php
/**
 * GreenTrans - Process Registration
 * Backend registration handler with full validation
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/auth/register.php');
}

$fullName = sanitize($_POST['full_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$role = sanitize($_POST['role'] ?? 'customer');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Backend Validation
$errors = [];

// Name: only alphabets
if (empty($fullName) || !preg_match('/^[A-Za-z\s]{2,100}$/', $fullName)) {
    $errors[] = 'Name must contain only alphabets (min 2 characters)';
}

// Email: only @gmail.com
if (empty($email) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    $errors[] = 'Only Gmail accounts are allowed (@gmail.com)';
}

// Phone: exactly 10 digits
if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
    $errors[] = 'Phone must be exactly 10 digits';
}

// Role: valid values only
if (!in_array($role, ['customer', 'driver'])) {
    $role = 'customer';
}

// Password validation
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must contain at least 1 uppercase letter';
}
if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
    $errors[] = 'Password must contain at least 1 special character';
}
if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match';
}

if (!empty($errors)) {
    redirect(APP_URL . '/auth/register.php?error=' . urlencode(implode('. ', $errors)));
}

// Handle File Uploads for Drivers
$licenseImage = null;
$idProofImage = null;

if ($role === 'driver') {
    $uploadDir = ROOT_PATH . 'uploads/documents/';
    
    // License Image
    if (isset($_FILES['license_image']) && $_FILES['license_image']['error'] === 0) {
        $ext = pathinfo($_FILES['license_image']['name'], PATHINFO_EXTENSION);
        $licenseImage = 'license_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['license_image']['tmp_name'], $uploadDir . $licenseImage);
    }
    
    // ID Proof Image
    if (isset($_FILES['id_proof_image']) && $_FILES['id_proof_image']['error'] === 0) {
        $ext = pathinfo($_FILES['id_proof_image']['name'], PATHINFO_EXTENSION);
        $idProofImage = 'id_proof_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['id_proof_image']['tmp_name'], $uploadDir . $idProofImage);
    }

    if (!$licenseImage || !$idProofImage) {
        redirect(APP_URL . '/auth/register.php?error=' . urlencode('Drivers must upload License and ID proof images.'));
    }
}

// Register user
$user = new User();
$result = $user->register([
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'password' => $password,
    'role' => $role,
    'driving_license' => $licenseImage,
    'id_proof' => $idProofImage,
    'is_approved' => ($role === 'driver' ? 0 : 1), // Drivers need admin approval
    'is_active' => 0 // Everyone starts inactive until OTP verified
]);

if ($result['success']) {
    require_once CLASSES_PATH . 'Mailer.php';
    $otp = $user->setOTP($email, 'register');
    $mailer = new Mailer();
    
    if ($mailer->sendOTP($email, $otp, $fullName)) {
        $_SESSION['pending_email'] = $email;
        $_SESSION['otp_type'] = 'register';
        redirect(APP_URL . '/auth/verify-otp.php');
    } else {
        // Fallback if mail fails - in a real app, maybe log this or show a different error
        $errors[] = 'Account created but failed to send verification email. Please contact support.';
        redirect(APP_URL . '/auth/register.php?error=' . urlencode(implode('. ', $errors)));
    }
} else {
    redirect(APP_URL . '/auth/register.php?error=' . urlencode($result['message']));
}

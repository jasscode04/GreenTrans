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
    'is_approved' => ($role === 'driver' ? 0 : 1) // Drivers need approval, customers are auto-approved
]);

if ($result['success']) {
    // Create driver availability record if driver
    if ($role === 'driver') {
        require_once CLASSES_PATH . 'Database.php';
        $db = new Database();
        $db->insert('driver_availability', [
            'driver_id' => $result['user_id'],
            'is_available' => 0 // Not available until approved
        ]);
        
        $user->logActivity($result['user_id'], 'register', 'Driver account created - pending approval');
        redirect(APP_URL . '/auth/login.php?success=' . urlencode('Registration successful! Your driver account is pending admin approval.'));
    } else {
        $user->logActivity($result['user_id'], 'register', 'New customer account created');
        redirect(APP_URL . '/auth/login.php?success=' . urlencode('Registration successful! Please login.'));
    }
} else {
    redirect(APP_URL . '/auth/register.php?error=' . urlencode($result['message']));
}

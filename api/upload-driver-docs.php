<?php
/**
 * GreenTrans - Upload Driver Documents
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn() || getUserRole() !== 'driver') {
    redirect(APP_URL . '/auth/login.php');
}

$userId = $_SESSION['user_id'];
$uploadDir = ROOT_PATH . 'uploads/documents/';

// Create directory if not exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$licenseImage = null;
$idProofImage = null;

// Handle License Upload
if (isset($_FILES['driving_license']) && $_FILES['driving_license']['error'] === 0) {
    $ext = pathinfo($_FILES['driving_license']['name'], PATHINFO_EXTENSION);
    $licenseImage = 'license_' . $userId . '_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['driving_license']['tmp_name'], $uploadDir . $licenseImage);
}

// Handle ID Proof Upload
if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] === 0) {
    $ext = pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION);
    $idProofImage = 'id_proof_' . $userId . '_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['id_proof']['tmp_name'], $uploadDir . $idProofImage);
}

if ($licenseImage || $idProofImage) {
    $userModel = new User();
    $updateData = [];
    if ($licenseImage) $updateData['driving_license'] = $licenseImage;
    if ($idProofImage) $updateData['id_proof'] = $idProofImage;
    
    $userModel->updateProfile($userId, $updateData);
    $userModel->logActivity($userId, 'upload_docs', 'Updated verification documents');
    
    setFlash('success', 'Documents uploaded successfully! Admin will review them soon.');
} else {
    setFlash('error', 'Please select valid images to upload.');
}

redirect(APP_URL . '/driver/dashboard.php');

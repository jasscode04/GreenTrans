<?php
/**
 * GreenTrans - Application Configuration
 * Central configuration file for the application
 */

// Application Settings
define('APP_NAME', 'GreenTrans');
define('APP_TAGLINE', 'Smart Transport & Logistics Management');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/tms');

// Directory Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('CLASSES_PATH', ROOT_PATH . 'classes/');
define('ASSETS_PATH', APP_URL . '/assets/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('UPLOADS_URL', APP_URL . '/uploads/');

// Database Configuration
require_once __DIR__ . '/database.php';

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// Currency
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');

// Pagination
define('ITEMS_PER_PAGE', 10);

// File Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Password Requirements
define('MIN_PASSWORD_LENGTH', 8);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');

/**
 * Format amount in Indian currency format
 * Example: 1250000 => ₹12,50,000
 */
function formatIndianCurrency($amount) {
    $amount = number_format((float)$amount, 2, '.', '');
    $parts = explode('.', $amount);
    $whole = $parts[0];
    $decimal = isset($parts[1]) ? '.' . $parts[1] : '';
    
    $sign = '';
    if ($whole[0] === '-') {
        $sign = '-';
        $whole = substr($whole, 1);
    }
    
    $len = strlen($whole);
    if ($len <= 3) {
        return CURRENCY_SYMBOL . $sign . $whole;
    }
    
    $last3 = substr($whole, -3);
    $remaining = substr($whole, 0, -3);
    $formatted = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining) . ',' . $last3;
    
    return CURRENCY_SYMBOL . $sign . $formatted;
}

/**
 * Generate unique tracking ID
 */
function generateTrackingId() {
    return 'GT' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Generate unique invoice number
 */
function generateInvoiceNumber() {
    return 'INV' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user role
 */
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check user role access
 */
function requireRole($roles) {
    if (!isLoggedIn()) {
        redirect(APP_URL . '/auth/login.php');
    }
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    if (!in_array(getUserRole(), $roles)) {
        redirect(APP_URL . '/auth/login.php?error=unauthorized');
    }
}

/**
 * Flash message system
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Time ago helper
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

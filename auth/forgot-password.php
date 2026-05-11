<?php
/**
 * GreenTrans - Forgot Password Page
 */
require_once __DIR__ . '/../config/config.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/' . getUserRole() . '/dashboard.php');
}
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | GreenTrans</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/auth.css">
    <script src="<?= APP_URL ?>/assets/js/theme.js"></script>
</head>
<body>
<button class="gt-theme-toggle" style="position:fixed;top:20px;right:20px;width:45px;height:45px;border-radius:50%;z-index:1000;box-shadow:0 4px 15px rgba(0,0,0,0.1);font-size:1.2rem;display:flex;align-items:center;justify-content:center" title="Toggle theme">
    <i class="bi bi-moon-fill"></i>
</button>
<div class="auth-wrapper" style="justify-content:center">
    <div class="auth-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="auth-right" style="width:auto">
        <div class="auth-card">
            <div class="auth-card-header">
                <a href="<?= APP_URL ?>/index.php" class="auth-logo" style="text-decoration:none;color:inherit;cursor:pointer;display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:15px">
                    <div class="logo-icon"><i class="bi bi-truck"></i></div>
                    <span class="logo-text">GreenTrans</span>
                </a>
                <h3>Forgot Password?</h3>
                <p>Enter your email and we'll send you a reset link</p>
            </div>

            <?php if ($success): ?>
            <div class="auth-alert alert-success">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="auth-alert alert-error">
                <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form action="process-forgot.php" method="POST">
                <div class="gt-form-group">
                    <label class="gt-label">Email Address</label>
                    <input type="email" name="email" class="gt-input" placeholder="you@gmail.com" required>
                    <div class="gt-form-error">Enter a valid Gmail address</div>
                </div>

                <button type="submit" class="btn-gt-primary w-100" style="padding:14px;font-size:1rem">
                    <i class="bi bi-envelope"></i> Send Reset Link
                </button>
            </form>

            <div class="auth-footer">
                <p>Remember your password? <a href="login.php">Sign In</a></p>
            </div>


        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</body>
</html>

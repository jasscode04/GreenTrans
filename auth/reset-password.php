<?php
/**
 * GreenTrans - Reset Password Page
 */
require_once __DIR__ . '/../config/config.php';
require_once CLASSES_PATH . 'User.php';

if (!isset($_SESSION['can_reset_password']) || !isset($_SESSION['pending_email'])) {
    redirect(APP_URL . '/auth/login.php');
}

define('PAGE_TITLE', 'Reset Password');
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $user = new User();
        $userData = $user->getByEmail($_SESSION['pending_email']);
        
        if ($userData) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo = getDBConnection();
            $pdo->prepare("UPDATE users SET password = ?, otp = NULL, otp_expiry = NULL WHERE id = ?")
                ->execute([$hashed, $userData['id']]);
            
            unset($_SESSION['can_reset_password'], $_SESSION['pending_email'], $_SESSION['otp_type']);
            setFlash('success', 'Password reset successfully! Please login with your new password.');
            redirect(APP_URL . '/auth/login.php');
        }
    }
}

include INCLUDES_PATH . 'header.php';
?>

<div class="auth-container">
    <div class="auth-card animate-slide-up">
        <div class="auth-header">
            <div class="auth-logo" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="bi bi-key-fill"></i>
            </div>
            <h1>New Password</h1>
            <p>Set a strong password for <br><strong><?= htmlspecialchars($_SESSION['pending_email']) ?></strong></p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="gt-form-group">
                <label>New Password</label>
                <input type="password" name="password" class="gt-input" required placeholder="••••••••">
            </div>
            <div class="gt-form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="gt-input" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-gt-primary w-100 mt-4" style="background: #ef4444;">Reset Password</button>
        </form>
    </div>
</div>

<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    padding: 20px;
}
.auth-card {
    background: white;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.05);
    width: 100%;
    max-width: 400px;
}
.auth-logo {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 20px;
}
</style>

<?php include INCLUDES_PATH . 'footer.php'; ?>

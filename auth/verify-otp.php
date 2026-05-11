<?php
/**
 * GreenTrans - OTP Verification Page
 */
require_once __DIR__ . '/../config/config.php';
define('PAGE_TITLE', 'Verify OTP');

$email = $_SESSION['pending_email'] ?? '';
$type = $_SESSION['otp_type'] ?? 'register'; // register or reset

if (empty($email)) {
    redirect(APP_URL . '/auth/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = sanitize($_POST['otp']);
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND otp = ? AND otp_expiry > NOW()");
    $stmt->execute([$email, $otp]);
    $user = $stmt->fetch();
    
    if ($user) {
        if ($type === 'register') {
            // Activate account
            $pdo->prepare("UPDATE users SET is_active = 1, otp = NULL, otp_expiry = NULL WHERE id = ?")
                ->execute([$user['id']]);
            setFlash('success', 'Account verified successfully! You can now login.');
            unset($_SESSION['pending_email'], $_SESSION['otp_type']);
            redirect(APP_URL . '/auth/login.php');
        } else {
            // Redirect to password reset page
            $_SESSION['can_reset_password'] = true;
            redirect(APP_URL . '/auth/reset-password.php');
        }
    } else {
        $error = "Invalid or expired OTP. Please try again.";
    }
}

include INCLUDES_PATH . 'header.php';
?>

<div class="auth-container">
    <div class="auth-card animate-slide-up">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1>Verification Required</h1>
            <p>Please enter the 6-digit OTP sent to <br><strong><?= htmlspecialchars($email) ?></strong></p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="gt-form-group text-center">
                <input type="text" name="otp" class="gt-input" maxlength="6" placeholder="Enter 6-digit OTP" 
                       style="text-align:center; font-size: 24px; letter-spacing: 10px; font-weight: 700;" required autofocus>
            </div>

            <button type="submit" class="btn-gt-primary w-100 mt-4">Verify OTP</button>
        </form>

        <div class="auth-footer mt-4">
            <p>Didn't receive code? <a href="#" id="resendOTP">Resend OTP</a></p>
            <a href="<?= APP_URL ?>/auth/login.php" class="text-muted small"><i class="bi bi-arrow-left"></i> Back to Login</a>
        </div>
    </div>
</div>

<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 20px;
}
.auth-card {
    background: white;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.05);
    width: 100%;
    max-width: 400px;
    text-align: center;
}
.auth-logo {
    width: 60px;
    height: 60px;
    background: rgba(16,185,129,0.1);
    color: #10b981;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 20px;
}
</style>

<?php include INCLUDES_PATH . 'footer.php'; ?>

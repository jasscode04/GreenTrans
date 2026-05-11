<?php
/**
 * GreenTrans - Login Page
 * Premium glassmorphic login with logistics visuals
 */
require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getUserRole();
    redirect(APP_URL . "/$role/dashboard.php");
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to GreenTrans - Smart Transport & Logistics Management System">
    <title>Login | GreenTrans</title>
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
<div class="auth-wrapper">
    <!-- Animated Background Shapes -->
    <div class="auth-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Left Side - Illustration -->
    <div class="auth-left">
        <div class="auth-illustration">
            <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                <!-- Road -->
                <rect x="0" y="280" width="500" height="40" rx="8" fill="#e2e8f0"/>
                <rect x="40" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="120" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="200" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="280" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="360" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="440" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <!-- Truck Body -->
                <rect x="120" y="200" width="200" height="80" rx="8" fill="#10b981"/>
                <rect x="125" y="205" width="190" height="70" rx="6" fill="#059669"/>
                <!-- Truck Cab -->
                <rect x="320" y="220" width="80" height="60" rx="8" fill="#10b981"/>
                <rect x="340" y="232" width="40" height="30" rx="4" fill="#a7f3d0"/>
                <!-- Wheels -->
                <circle cx="180" cy="280" r="22" fill="#334155"/>
                <circle cx="180" cy="280" r="10" fill="#94a3b8"/>
                <circle cx="280" cy="280" r="22" fill="#334155"/>
                <circle cx="280" cy="280" r="10" fill="#94a3b8"/>
                <circle cx="370" cy="280" r="22" fill="#334155"/>
                <circle cx="370" cy="280" r="10" fill="#94a3b8"/>
                <!-- Packages -->
                <rect x="145" y="215" width="40" height="40" rx="4" fill="#fbbf24" opacity="0.9"/>
                <rect x="195" y="225" width="35" height="35" rx="4" fill="#f59e0b" opacity="0.9"/>
                <rect x="240" y="218" width="38" height="38" rx="4" fill="#fbbf24" opacity="0.9"/>
                <!-- Logo on truck -->
                <text x="200" y="258" font-family="Outfit" font-size="16" fill="#fff" font-weight="700" text-anchor="middle">GT</text>
                <!-- GPS Pin -->
                <g transform="translate(420, 140)">
                    <circle cx="0" cy="0" r="20" fill="#ef4444" opacity="0.2">
                        <animate attributeName="r" values="20;30;20" dur="2s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.2;0.05;0.2" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <path d="M0-14 C-7.7-14 -14-7.7 -14 0 C-14 10 0 22 0 22 S14 10 14 0 C14-7.7 7.7-14 0-14z" fill="#ef4444"/>
                    <circle cx="0" cy="-1" r="5" fill="#fff"/>
                </g>
                <!-- Cloud -->
                <g opacity="0.3">
                    <ellipse cx="80" cy="100" rx="40" ry="20" fill="#94a3b8"/>
                    <ellipse cx="60" cy="105" rx="25" ry="15" fill="#94a3b8"/>
                    <ellipse cx="105" cy="105" rx="30" ry="15" fill="#94a3b8"/>
                </g>
            </svg>
        </div>
        <h2>Smart Logistics Made Simple</h2>
        <p>Track shipments, manage fleet, and optimize delivery routes with GreenTrans</p>
        <div class="auth-features">
            <div class="auth-feature-item">
                <i class="bi bi-shield-check"></i>
                <span>Secure</span>
            </div>
            <div class="auth-feature-item">
                <i class="bi bi-lightning-charge"></i>
                <span>Fast</span>
            </div>
            <div class="auth-feature-item">
                <i class="bi bi-geo-alt"></i>
                <span>Tracking</span>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="auth-right">
        <div class="auth-card">
            <div class="auth-card-header">
                <a href="<?= APP_URL ?>/index.php" class="auth-logo" style="text-decoration:none;color:inherit;cursor:pointer;display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:15px">
                    <div class="logo-icon"><i class="bi bi-truck"></i></div>
                    <span class="logo-text">GreenTrans</span>
                </a>
                <h3>Welcome Back</h3>
                <p>Sign in to your account to continue</p>
            </div>

            <?php if ($error === 'unauthorized'): ?>
            <div class="auth-alert alert-error">
                <i class="bi bi-exclamation-circle"></i> You don't have access to that page.
            </div>
            <?php elseif ($error): ?>
            <div class="auth-alert alert-error">
                <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="auth-alert alert-success">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <form action="process-login.php" method="POST" id="loginForm">
                <div class="gt-form-group">
                    <label class="gt-label">Email Address</label>
                    <div style="position:relative">
                        <input type="email" name="email" class="gt-input" placeholder="you@gmail.com" required autocomplete="email">
                        <span class="validation-icon valid-icon"><i class="bi bi-check-circle-fill"></i></span>
                        <span class="validation-icon invalid-icon"><i class="bi bi-x-circle-fill"></i></span>
                    </div>
                    <div class="gt-form-error">Please enter a valid Gmail address</div>
                </div>

                <div class="gt-form-group">
                    <label class="gt-label">Password</label>
                    <div style="position:relative">
                        <input type="password" name="password" class="gt-input" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="gt-form-error">Password is required</div>
                </div>

                <div class="auth-options">
                    <label>
                        <input type="checkbox" name="remember" style="accent-color:var(--gt-primary)"> Remember me
                    </label>
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-gt-primary w-100" style="padding:14px;font-size:1rem">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Create Account</a></p>
            </div>


        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</body>
</html>

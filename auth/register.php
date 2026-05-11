<?php
/**
 * GreenTrans - Registration Page
 */
require_once __DIR__ . '/../config/config.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/' . getUserRole() . '/dashboard.php');
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | GreenTrans</title>
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
    <div class="auth-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="auth-left">
        <div class="auth-illustration">
            <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                <!-- Warehouse -->
                <rect x="50" y="150" width="200" height="130" rx="4" fill="#e2e8f0"/>
                <polygon points="50,150 150,80 250,150" fill="#94a3b8"/>
                <rect x="110" y="210" width="50" height="70" rx="4" fill="#10b981"/>
                <rect x="70" y="180" width="30" height="30" rx="2" fill="#a7f3d0"/>
                <rect x="170" y="180" width="30" height="30" rx="2" fill="#a7f3d0"/>
                <!-- Boxes -->
                <rect x="300" y="230" width="50" height="50" rx="4" fill="#fbbf24"/>
                <rect x="360" y="240" width="40" height="40" rx="4" fill="#f59e0b"/>
                <rect x="320" y="200" width="35" height="35" rx="4" fill="#fbbf24" opacity="0.7"/>
                <!-- Delivery person -->
                <circle cx="420" cy="200" r="20" fill="#10b981"/>
                <rect x="408" y="220" width="24" height="40" rx="8" fill="#059669"/>
                <rect x="405" y="260" width="10" height="20" rx="4" fill="#334155"/>
                <rect x="425" y="260" width="10" height="20" rx="4" fill="#334155"/>
                <!-- Road -->
                <rect x="0" y="280" width="500" height="40" rx="8" fill="#e2e8f0"/>
                <rect x="30" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="110" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="190" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="270" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
                <rect x="350" y="296" width="40" height="8" rx="4" fill="#fff" opacity="0.8"/>
            </svg>
        </div>
        <h2>Join GreenTrans Today</h2>
        <p>Start managing your transport and logistics operations efficiently</p>
    </div>

    <div class="auth-right" style="width:560px">
        <div class="auth-card" style="max-width:480px">
            <div class="auth-card-header">
                <a href="<?= APP_URL ?>/index.php" class="auth-logo" style="text-decoration:none;color:inherit;cursor:pointer;display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:15px">
                    <div class="logo-icon"><i class="bi bi-truck"></i></div>
                    <span class="logo-text">GreenTrans</span>
                </a>
                <h3>Create Account</h3>
                <p>Fill in your details to get started</p>
            </div>

            <?php if ($error): ?>
            <div class="auth-alert alert-error">
                <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form action="process-register.php" method="POST" id="registerForm" enctype="multipart/form-data">
                <div class="gt-form-group">
                    <label class="gt-label">Full Name</label>
                    <input type="text" name="full_name" class="gt-input" placeholder="Enter your full name" required>
                    <div class="gt-form-error">Only alphabets allowed</div>
                </div>

                <div class="gt-form-group">
                    <label class="gt-label">Email Address</label>
                    <input type="email" name="email" class="gt-input" placeholder="you@gmail.com" required>
                    <div class="gt-form-error">Only Gmail accounts allowed</div>
                </div>

                <div class="gt-form-group">
                    <label class="gt-label">Phone Number</label>
                    <input type="text" name="phone" class="gt-input" placeholder="10-digit mobile number" required>
                    <div class="gt-form-error">Enter exactly 10 digits</div>
                </div>

                <div class="gt-form-group">
                    <label class="gt-label">Register As</label>
                    <div class="role-selector">
                        <div class="role-option selected" onclick="toggleDriverFields('customer')">
                            <input type="radio" name="role" value="customer" checked>
                            <i class="bi bi-person"></i>
                            <div class="role-name">Customer</div>
                        </div>
                        <div class="role-option" onclick="toggleDriverFields('driver')">
                            <input type="radio" name="role" value="driver">
                            <i class="bi bi-truck"></i>
                            <div class="role-name">Driver</div>
                        </div>
                    </div>
                </div>

                <!-- Driver Documents (Hidden by default) -->
                <div id="driverDocsSection" style="display:none; padding: 15px; background: rgba(var(--gt-primary-rgb), 0.05); border-radius: 12px; border: 1px dashed var(--gt-primary); margin-bottom: 20px;">
                    <h6 class="mb-3" style="font-weight:700; color:var(--gt-primary); font-size: 0.9rem;">Verification Documents</h6>
                    <div class="gt-form-group">
                        <label class="gt-label">Driving License (Image)</label>
                        <input type="file" name="license_image" class="gt-input" accept="image/*">
                        <div class="gt-form-error">Please upload a clear photo of your license</div>
                    </div>
                    <div class="gt-form-group mb-0">
                        <label class="gt-label">ID Proof (Aadhar/Voter ID Image)</label>
                        <input type="file" name="id_proof_image" class="gt-input" accept="image/*">
                        <div class="gt-form-error">Required for background verification</div>
                    </div>
                    <p class="mt-2 text-muted" style="font-size:0.75rem"><i class="bi bi-info-circle"></i> Note: Driver accounts require manual approval by admin.</p>
                </div>

                <div class="gt-form-group">
                    <label class="gt-label">Password</label>
                    <div style="position:relative">
                        <input type="password" name="password" class="gt-input" placeholder="Min 8 chars, 1 uppercase, 1 special" required>
                        <button type="button" class="password-toggle"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="password-strength-bar"><div class="strength-fill"></div></div>
                    <div class="password-strength-text"></div>
                    <div class="gt-form-error">Password doesn't meet requirements</div>
                </div>

                <div class="gt-form-group">
                    <label class="gt-label">Confirm Password</label>
                    <div style="position:relative">
                        <input type="password" name="confirm_password" class="gt-input" placeholder="Re-enter your password" required>
                        <button type="button" class="password-toggle"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="gt-form-error">Passwords do not match</div>
                </div>

                <button type="submit" class="btn-gt-primary w-100" style="padding:14px;font-size:1rem">
                    <i class="bi bi-person-plus-fill"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>


        </div>
    </div>
</div>

    <script>
        function toggleDriverFields(role) {
            const driverDocs = document.getElementById('driverDocsSection');
            const licenseInput = document.querySelector('input[name="license_image"]');
            const idProofInput = document.querySelector('input[name="id_proof_image"]');
            
            if (role === 'driver') {
                driverDocs.style.display = 'block';
                licenseInput.required = true;
                idProofInput.required = true;
            } else {
                driverDocs.style.display = 'none';
                licenseInput.required = false;
                idProofInput.required = false;
            }
        }
    </script>
    <script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</body>
</html>

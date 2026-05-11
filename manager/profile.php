<?php
/**
 * GreenTrans - Profile Page (Shared across roles)
 * Auto-detects current user role for proper routing
 */
$roleDir = basename(dirname(__FILE__));
require_once __DIR__ . '/../config/config.php';
requireRole($roleDir === 'admin' ? 'admin' : ($roleDir === 'manager' ? 'manager' : ($roleDir === 'driver' ? 'driver' : 'customer')));

define('PAGE_TITLE', 'My Profile');

require_once CLASSES_PATH . 'User.php';
$userModel = new User();
$user = $userModel->getById($_SESSION['user_id']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $data = [
            'full_name' => sanitize($_POST['full_name']),
            'phone' => sanitize($_POST['phone']),
            'address' => sanitize($_POST['address'] ?? ''),
            'city' => sanitize($_POST['city'] ?? ''),
            'state' => sanitize($_POST['state'] ?? ''),
            'pincode' => sanitize($_POST['pincode'] ?? ''),
        ];
        
        // Validate
        if (!preg_match('/^[A-Za-z\s]{2,100}$/', $data['full_name'])) {
            setFlash('error', 'Name must contain only alphabets');
        } elseif (!preg_match('/^[0-9]{10}$/', $data['phone'])) {
            setFlash('error', 'Phone must be exactly 10 digits');
        } else {
            $userModel->updateProfile($_SESSION['user_id'], $data);
            setFlash('success', 'Profile updated successfully');
        }
        redirect(APP_URL . "/$roleDir/profile.php");
    }
    
    if (isset($_POST['change_password'])) {
        $result = $userModel->changePassword(
            $_SESSION['user_id'],
            $_POST['current_password'],
            $_POST['new_password']
        );
        setFlash($result['success'] ? 'success' : 'error', $result['message']);
        redirect(APP_URL . "/$roleDir/profile.php");
    }
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $result = $userModel->uploadProfileImage($_SESSION['user_id'], $_FILES['profile_image']);
        setFlash($result['success'] ? 'success' : 'error', $result['message']);
        redirect(APP_URL . "/$roleDir/profile.php");
    }
}

$activities = $userModel->getActivityLog($_SESSION['user_id'], 10);

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-person-circle" style="color:var(--gt-primary)"></i> My Profile</h1>
        <p>Manage your account information</p>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="gt-section-card text-center animate-slide-up">
                <div style="position:relative;display:inline-block;margin-bottom:20px">
                    <img src="<?= UPLOADS_URL ?>/profiles/<?= htmlspecialchars($user['profile_image']) ?>" 
                         class="gt-avatar-xl" alt="Profile"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=10b981&color=fff&size=120'">
                </div>
                <h5 style="font-weight:700"><?= htmlspecialchars($user['full_name']) ?></h5>
                <span class="badge-gt badge-available" style="text-transform:capitalize"><?= $user['role'] ?></span>
                <p class="text-muted mt-2" style="font-size:0.85rem"><?= htmlspecialchars($user['email']) ?></p>
                
                <!-- Upload Image -->
                <form method="POST" enctype="multipart/form-data" class="mt-3">
                    <label class="btn-gt-outline d-block" style="padding:8px;font-size:0.85rem;cursor:pointer">
                        <i class="bi bi-camera me-1"></i> Change Photo
                        <input type="file" name="profile_image" accept="image/*" style="display:none" onchange="this.form.submit()">
                    </label>
                </form>

                <!-- Account Info -->
                <div class="mt-4 text-start">
                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--gt-border-light) !important">
                        <span class="text-muted" style="font-size:0.85rem">Phone</span>
                        <strong style="font-size:0.85rem"><?= htmlspecialchars($user['phone']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:var(--gt-border-light) !important">
                        <span class="text-muted" style="font-size:0.85rem">Joined</span>
                        <strong style="font-size:0.85rem"><?= date('d M Y', strtotime($user['created_at'])) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted" style="font-size:0.85rem">Last Login</span>
                        <strong style="font-size:0.85rem"><?= $user['last_login'] ? timeAgo($user['last_login']) : 'N/A' ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="col-lg-8">
            <div class="gt-section-card animate-slide-up delay-1">
                <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Profile</h6>
                <form method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="gt-form-group">
                                <label class="gt-label">Full Name</label>
                                <input type="text" name="full_name" class="gt-input" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="gt-form-group">
                                <label class="gt-label">Phone</label>
                                <input type="text" name="phone" class="gt-input" value="<?= htmlspecialchars($user['phone']) ?>" required maxlength="10">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="gt-form-group">
                                <label class="gt-label">Address</label>
                                <textarea name="address" class="gt-input" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">City</label>
                                <input type="text" name="city" class="gt-input" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">State</label>
                                <input type="text" name="state" class="gt-input" value="<?= htmlspecialchars($user['state'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Pincode</label>
                                <input type="text" name="pincode" class="gt-input" value="<?= htmlspecialchars($user['pincode'] ?? '') ?>" maxlength="6">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-gt-primary mt-2"><i class="bi bi-check-circle"></i> Save Changes</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="gt-section-card animate-slide-up delay-2 mt-4">
                <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-shield-lock text-warning me-2"></i>Change Password</h6>
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Current Password</label>
                                <input type="password" name="current_password" class="gt-input" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">New Password</label>
                                <input type="password" name="new_password" class="gt-input" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Confirm New Password</label>
                                <input type="password" name="confirm_new_password" class="gt-input" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-gt-secondary mt-2"><i class="bi bi-key"></i> Update Password</button>
                </form>
            </div>

            <!-- Activity Log -->
            <div class="gt-section-card animate-slide-up delay-3 mt-4">
                <h6 style="font-weight:700;margin-bottom:20px"><i class="bi bi-clock-history text-info me-2"></i>Recent Activity</h6>
                <div class="gt-timeline">
                    <?php foreach ($activities as $a): ?>
                    <div class="gt-timeline-item completed">
                        <div style="font-weight:600;font-size:0.9rem"><?= htmlspecialchars($a['action']) ?></div>
                        <div class="text-muted" style="font-size:0.8rem"><?= htmlspecialchars($a['description']) ?> — <?= timeAgo($a['created_at']) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($activities)): ?>
                    <p class="text-muted">No activity yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

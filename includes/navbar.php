<?php
/**
 * GreenTrans - Top Navbar
 */
$userName = $_SESSION['user_name'] ?? 'User';
$userImage = $_SESSION['user_image'] ?? 'default.png';
$userRole = $_SESSION['user_role'] ?? 'customer';

// Fetch notifications
$notifications = [];
if (isset($_SESSION['user_id'])) {
    $notifications = $notifModel->getByUser($_SESSION['user_id'], 10);
}
?>

<!-- TOP NAVBAR -->
<nav class="gt-navbar">
    <div class="gt-navbar-left">
        <button class="gt-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="gt-search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search shipments, drivers..." id="globalSearch">
        </div>
    </div>

    <div class="gt-navbar-right">
        <!-- Theme Toggle -->
        <button class="gt-theme-toggle" title="Toggle theme" aria-label="Toggle dark mode">
            <i class="bi bi-moon-fill"></i>
        </button>

        <!-- Notifications -->
        <div style="position:relative">
            <button class="gt-notif-btn" aria-label="Notifications">
                <i class="bi bi-bell-fill"></i>
                <?php if ($unreadCount > 0): ?>
                <span class="gt-notif-badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </button>

            <div class="gt-notif-dropdown">
                <div class="notif-header">
                    <h6>Notifications</h6>
                    <?php if ($unreadCount > 0): ?>
                    <a href="<?= APP_URL ?>/api/mark-read.php" style="font-size:0.8rem;color:var(--gt-primary);font-weight:600;">Mark all read</a>
                    <?php endif; ?>
                </div>
                <div class="notif-list">
                    <?php if (empty($notifications)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-bell-slash text-muted" style="font-size:2rem"></i>
                        <p class="text-muted mt-2" style="font-size:0.85rem">No notifications yet</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($notifications as $notif): 
                        $iconMap = [
                            'shipment' => ['icon' => 'bi-box-seam', 'bg' => 'rgba(99,102,241,0.1)', 'color' => '#6366f1'],
                            'delivery' => ['icon' => 'bi-truck', 'bg' => 'rgba(16,185,129,0.1)', 'color' => '#10b981'],
                            'payment' => ['icon' => 'bi-credit-card', 'bg' => 'rgba(245,158,11,0.1)', 'color' => '#f59e0b'],
                            'system' => ['icon' => 'bi-gear', 'bg' => 'rgba(59,130,246,0.1)', 'color' => '#3b82f6'],
                            'alert' => ['icon' => 'bi-exclamation-triangle', 'bg' => 'rgba(239,68,68,0.1)', 'color' => '#ef4444'],
                        ];
                        $ni = $iconMap[$notif['type']] ?? $iconMap['system'];
                    ?>
                    <div class="gt-notif-item <?= !$notif['is_read'] ? 'unread' : '' ?>">
                        <div class="notif-icon" style="background:<?= $ni['bg'] ?>;color:<?= $ni['color'] ?>">
                            <i class="bi <?= $ni['icon'] ?>"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title"><?= htmlspecialchars($notif['title']) ?></div>
                            <div class="notif-text"><?= htmlspecialchars($notif['message']) ?></div>
                            <div class="notif-time"><?= timeAgo($notif['created_at']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <a href="<?= APP_URL ?>/<?= $userRole ?>/profile.php" class="gt-navbar-user">
            <img src="<?= UPLOADS_URL ?>/profiles/<?= htmlspecialchars($userImage) ?>" 
                 alt="<?= htmlspecialchars($userName) ?>"
                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&background=10b981&color=fff'">
            <span class="user-name"><?= htmlspecialchars($userName) ?></span>
        </a>
    </div>
</nav>

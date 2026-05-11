<?php
/**
 * GreenTrans - Sidebar Navigation
 * Dynamic sidebar based on user role
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userRole = $_SESSION['user_role'] ?? 'customer';
$userName = $_SESSION['user_name'] ?? 'User';
$userImage = $_SESSION['user_image'] ?? 'default.png';

// Navigation items per role
$navItems = [
    'admin' => [
        'main' => [
            ['icon' => 'bi-grid-1x2-fill', 'label' => 'Dashboard', 'url' => APP_URL . '/admin/dashboard.php', 'page' => 'dashboard'],
        ],
        'management' => [
            ['icon' => 'bi-shield-check', 'label' => 'Driver Approvals', 'url' => APP_URL . '/admin/driver-approvals.php', 'page' => 'driver-approvals'],
            ['icon' => 'bi-people-fill', 'label' => 'Users', 'url' => APP_URL . '/admin/users.php', 'page' => 'users'],
            ['icon' => 'bi-truck', 'label' => 'Vehicles', 'url' => APP_URL . '/admin/vehicles.php', 'page' => 'vehicles'],
            ['icon' => 'bi-box-seam-fill', 'label' => 'Shipments', 'url' => APP_URL . '/admin/shipments.php', 'page' => 'shipments'],
            ['icon' => 'bi-chat-square-text-fill', 'label' => 'Complaints', 'url' => APP_URL . '/admin/complaints.php', 'page' => 'complaints'],
        ],
        'analytics' => [
            ['icon' => 'bi-graph-up-arrow', 'label' => 'Analytics', 'url' => APP_URL . '/admin/analytics.php', 'page' => 'analytics'],
        ],
        'settings' => [
            ['icon' => 'bi-gear-fill', 'label' => 'Settings', 'url' => APP_URL . '/admin/settings.php', 'page' => 'settings'],
            ['icon' => 'bi-person-circle', 'label' => 'Profile', 'url' => APP_URL . '/admin/profile.php', 'page' => 'profile'],
        ]
    ],
    'manager' => [
        'main' => [
            ['icon' => 'bi-grid-1x2-fill', 'label' => 'Dashboard', 'url' => APP_URL . '/manager/dashboard.php', 'page' => 'dashboard'],
        ],
        'operations' => [
            ['icon' => 'bi-truck', 'label' => 'Fleet', 'url' => APP_URL . '/manager/fleet.php', 'page' => 'fleet'],
            ['icon' => 'bi-people-fill', 'label' => 'Drivers', 'url' => APP_URL . '/manager/drivers.php', 'page' => 'drivers'],
            ['icon' => 'bi-box-seam-fill', 'label' => 'Shipments', 'url' => APP_URL . '/manager/shipments.php', 'page' => 'shipments'],
        ],
        'reports' => [
            ['icon' => 'bi-file-earmark-bar-graph', 'label' => 'Reports', 'url' => APP_URL . '/manager/reports.php', 'page' => 'reports'],
            ['icon' => 'bi-person-circle', 'label' => 'Profile', 'url' => APP_URL . '/manager/profile.php', 'page' => 'profile'],
        ]
    ],
    'driver' => [
        'main' => [
            ['icon' => 'bi-grid-1x2-fill', 'label' => 'Dashboard', 'url' => APP_URL . '/driver/dashboard.php', 'page' => 'dashboard'],
        ],
        'deliveries' => [
            ['icon' => 'bi-box-seam-fill', 'label' => 'Deliveries', 'url' => APP_URL . '/driver/deliveries.php', 'page' => 'deliveries'],
            ['icon' => 'bi-truck', 'label' => 'My Vehicle', 'url' => APP_URL . '/driver/vehicle-info.php', 'page' => 'vehicle-info'],
            ['icon' => 'bi-wallet2', 'label' => 'Earnings', 'url' => APP_URL . '/driver/earnings.php', 'page' => 'earnings'],
        ],
        'account' => [
            ['icon' => 'bi-speedometer2', 'label' => 'Performance', 'url' => APP_URL . '/driver/performance.php', 'page' => 'performance'],
            ['icon' => 'bi-headset', 'label' => 'Support', 'url' => APP_URL . '/driver/support.php', 'page' => 'support'],
            ['icon' => 'bi-person-circle', 'label' => 'Profile', 'url' => APP_URL . '/driver/profile.php', 'page' => 'profile'],
        ]
    ],
    'customer' => [
        'main' => [
            ['icon' => 'bi-grid-1x2-fill', 'label' => 'Dashboard', 'url' => APP_URL . '/customer/dashboard.php', 'page' => 'dashboard'],
        ],
        'shipments' => [
            ['icon' => 'bi-plus-circle-fill', 'label' => 'Book Shipment', 'url' => APP_URL . '/customer/book-shipment.php', 'page' => 'book-shipment'],
            ['icon' => 'bi-geo-alt-fill', 'label' => 'Track Shipment', 'url' => APP_URL . '/customer/track-shipment.php', 'page' => 'track-shipment'],
            ['icon' => 'bi-clock-history', 'label' => 'History', 'url' => APP_URL . '/customer/history.php', 'page' => 'history'],
        ],
        'billing' => [
            ['icon' => 'bi-receipt', 'label' => 'Invoices', 'url' => APP_URL . '/customer/invoices.php', 'page' => 'invoices'],
            ['icon' => 'bi-headset', 'label' => 'Support', 'url' => APP_URL . '/customer/support.php', 'page' => 'support'],
        ],
        'account' => [
            ['icon' => 'bi-person-circle', 'label' => 'Profile', 'url' => APP_URL . '/customer/profile.php', 'page' => 'profile'],
        ]
    ]
];

$roleNav = $navItems[$userRole] ?? $navItems['customer'];
?>

<!-- SIDEBAR -->
<aside class="gt-sidebar" id="sidebar">
    <!-- Brand -->
    <div class="gt-sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-truck"></i>
        </div>
        <div class="brand-text">
            <div class="brand-name">GreenTrans</div>
            <div class="brand-tagline">Transport & Logistics</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="gt-sidebar-nav">
        <?php foreach ($roleNav as $section => $items): ?>
        <div class="nav-section">
            <div class="nav-section-title"><?= ucfirst($section) ?></div>
            <?php foreach ($items as $item): ?>
            <a href="<?= $item['url'] ?>" class="gt-nav-link <?= $currentPage === $item['page'] ? 'active' : '' ?>">
                <i class="bi <?= $item['icon'] ?>"></i>
                <span><?= $item['label'] ?></span>
                <?php if (isset($item['badge'])): ?>
                <span class="nav-badge"><?= $item['badge'] ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="gt-sidebar-footer">
        <a href="<?= APP_URL ?>/auth/logout.php" class="gt-nav-link" style="color:#ef4444;">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
        <div class="gt-sidebar-user">
            <img src="<?= UPLOADS_URL ?>/profiles/<?= htmlspecialchars($userImage) ?>" 
                 alt="<?= htmlspecialchars($userName) ?>" 
                 class="user-avatar"
                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&background=10b981&color=fff'">
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                <div class="user-role"><?= ucfirst($userRole) ?></div>
            </div>
        </div>
    </div>
</aside>

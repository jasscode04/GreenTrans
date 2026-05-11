<?php
/**
 * GreenTrans - Driver Performance
 */
require_once __DIR__ . '/../config/config.php';
requireRole('driver');
define('PAGE_TITLE', 'Performance');
define('LOAD_CHART_JS', true);

$userId = $_SESSION['user_id'];
$pdo = getDBConnection();

// Check for approval
$isApproved = $_SESSION['user_approved'] ?? 0;

// Stats
$delivered = $pdo->query("SELECT COUNT(*) FROM shipments WHERE driver_id = $userId AND status = 'delivered'")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM shipments WHERE driver_id = $userId AND status IN ('pending', 'picked_up', 'in_transit')")->fetchColumn();
$total = $delivered + $pending;
$rating = 4.8; // Hardcoded for demo

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <?php if (!$isApproved): ?>
    <div class="gt-section-card text-center animate-slide-up" style="padding: 80px 20px;">
        <i class="bi bi-lock-fill text-muted" style="font-size: 3rem;"></i>
        <h2 class="mt-3">Access Restricted</h2>
        <p class="text-muted">Verification required to view performance metrics.</p>
    </div>
    <?php else: ?>
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-speedometer2" style="color:var(--gt-primary)"></i> My Performance</h1>
        <p>Track your efficiency and delivery statistics</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="kpi-card kpi-green animate-slide-up delay-1">
                <div class="kpi-icon"><i class="bi bi-star-fill"></i></div>
                <div class="kpi-value"><?= $rating ?> / 5.0</div>
                <div class="kpi-label">Driver Rating</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-blue animate-slide-up delay-2">
                <div class="kpi-icon"><i class="bi bi-trophy-fill"></i></div>
                <div class="kpi-value"><?= $delivered ?></div>
                <div class="kpi-label">Deliveries Completed</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-purple animate-slide-up delay-3">
                <div class="kpi-icon"><i class="bi bi-lightning-fill"></i></div>
                <div class="kpi-value"><?= $total > 0 ? round(($delivered/$total)*100) : 100 ?>%</div>
                <div class="kpi-label">Completion Rate</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="gt-section-card h-100 animate-slide-up">
                <h6 style="font-weight:700;margin-bottom:20px">Monthly Delivery Trend</h6>
                <canvas id="performanceChart" height="300"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="gt-section-card h-100 animate-slide-up">
                <h6 style="font-weight:700;margin-bottom:20px">Recent Feedback</h6>
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold" style="font-size:0.85rem">Priya Patel</span>
                            <span class="text-warning small"><i class="bi bi-star-fill"></i> 5.0</span>
                        </div>
                        <p class="mb-0 small text-muted">"Excellent delivery, very polite driver."</p>
                    </div>
                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold" style="font-size:0.85rem">Rajesh Kumar</span>
                            <span class="text-warning small"><i class="bi bi-star-fill"></i> 4.5</span>
                        </div>
                        <p class="mb-0 small text-muted">"On time delivery, package was safe."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($isApproved): ?>
<script>
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Deliveries',
            data: [12, 19, 15, 25, 22, <?= $delivered ?>],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
<?php endif; ?>

<?php include INCLUDES_PATH . 'footer.php'; ?>

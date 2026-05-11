<?php
/**
 * GreenTrans - Driver Vehicle Info
 */
require_once __DIR__ . '/../config/config.php';
requireRole('driver');

define('PAGE_TITLE', 'My Vehicle');

$userId = $_SESSION['user_id'];
$pdo = getDBConnection();

// Check for approval
$isApproved = $_SESSION['user_approved'] ?? 0;

// Fetch assigned vehicle
$vehicle = $pdo->query("SELECT * FROM vehicles WHERE assigned_driver_id = $userId")->fetch();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <?php if (!$isApproved): ?>
    <!-- Re-using the pending approval UI -->
    <div class="gt-section-card text-center animate-slide-up" style="padding: 80px 20px; border: 1px dashed var(--gt-primary); background: rgba(var(--gt-primary-rgb), 0.02);">
        <div class="mb-4">
            <div style="width:100px; height:100px; background: rgba(var(--gt-primary-rgb), 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--gt-primary); font-size: 3rem;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
        <h2 style="font-weight:800; margin-bottom:15px">Verification in Progress</h2>
        <p class="text-muted mx-auto" style="max-width: 500px; font-size: 1.1rem; line-height: 1.7">
            Vehicle details will be visible once your account is approved and a vehicle is assigned to you.
        </p>
    </div>
    <?php else: ?>
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-truck" style="color:var(--gt-primary)"></i> My Vehicle</h1>
        <p>Details of your assigned transport vehicle</p>
    </div>

    <?php if (!$vehicle): ?>
    <div class="gt-section-card text-center animate-slide-up">
        <div class="gt-empty-state">
            <i class="bi bi-exclamation-triangle"></i>
            <h5>No Vehicle Assigned</h5>
            <p class="text-muted">Please contact the manager to get a vehicle assigned to you.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="row g-4 animate-slide-up">
        <div class="col-lg-5">
            <div class="gt-section-card h-100">
                <div class="text-center mb-4">
                    <div style="width:120px; height:120px; background:var(--gt-gradient-primary); border-radius:24px; display:flex; align-items:center; justify-content:center; margin:0 auto; color:#fff; font-size:4rem; box-shadow: var(--gt-shadow-glow)">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h3 class="mt-3 fw-bold"><?= $vehicle['vehicle_number'] ?></h3>
                    <span class="badge-gt badge-<?= $vehicle['status'] ?>"><?= ucfirst($vehicle['status']) ?></span>
                </div>
                
                <hr class="my-4" style="opacity:0.1">
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Brand / Model</span>
                        <span class="fw-bold"><?= $vehicle['brand'] ?> <?= $vehicle['model'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Vehicle Type</span>
                        <span class="fw-bold"><?= ucwords(str_replace('_',' ',$vehicle['vehicle_type'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Fuel Type</span>
                        <span class="fw-bold" style="text-transform:uppercase"><?= $vehicle['fuel_type'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Max Capacity</span>
                        <span class="fw-bold"><?= $vehicle['capacity_kg'] ?> KG</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="gt-section-card mb-4">
                <h6 class="fw-bold mb-4"><i class="bi bi-file-earmark-text text-primary me-2"></i> Documents & Compliance</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <small class="text-muted d-block">Insurance Expiry</small>
                            <span class="fw-bold"><?= date('d M Y', strtotime($vehicle['insurance_expiry'])) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <small class="text-muted d-block">Fitness Expiry</small>
                            <span class="fw-bold"><?= date('d M Y', strtotime($vehicle['fitness_expiry'])) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <small class="text-muted d-block">Last Service</small>
                            <span class="fw-bold"><?= date('d M Y', strtotime($vehicle['last_service_date'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="neo-card" style="background: rgba(var(--gt-primary-rgb), 0.05); border: 1px solid rgba(var(--gt-primary-rgb), 0.1);">
                <div class="d-flex gap-3 align-items-center">
                    <div class="fs-1 text-primary"><i class="bi bi-info-circle"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Maintenance Notice</h6>
                        <p class="mb-0 small text-muted">If you notice any issues with the vehicle, please report them immediately via the Support section.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

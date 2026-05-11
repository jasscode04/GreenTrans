<?php
/**
 * GreenTrans - Book Shipment (Customer)
 */
require_once __DIR__ . '/../config/config.php';
requireRole('customer');

define('PAGE_TITLE', 'Book Shipment');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once CLASSES_PATH . 'Shipment.php';
    $shipmentModel = new Shipment();
    
    $trackingId = generateTrackingId();
    $result = $shipmentModel->create([
        'tracking_id' => $trackingId,
        'customer_id' => $_SESSION['user_id'],
        'pickup_address' => sanitize($_POST['pickup_address']),
        'pickup_city' => sanitize($_POST['pickup_city']),
        'pickup_pincode' => sanitize($_POST['pickup_pincode']),
        'delivery_address' => sanitize($_POST['delivery_address']),
        'delivery_city' => sanitize($_POST['delivery_city']),
        'delivery_pincode' => sanitize($_POST['delivery_pincode']),
        'package_type' => sanitize($_POST['package_type']),
        'weight_kg' => (float)$_POST['weight_kg'],
        'priority' => sanitize($_POST['priority']),
        'notes' => sanitize($_POST['notes'] ?? ''),
        'estimated_delivery' => date('Y-m-d', strtotime('+5 days')),
        'shipping_cost' => (float)$_POST['estimated_cost'],
    ]);
    
    if ($result['success']) {
        // Create notification
        require_once CLASSES_PATH . 'Notification.php';
        $notif = new Notification();
        $notif->create($_SESSION['user_id'], 'Shipment Created', "Your shipment $trackingId has been created successfully.", 'shipment');
        
        setFlash('success', "Shipment booked! Tracking ID: $trackingId");
        redirect(APP_URL . '/customer/dashboard.php');
    } else {
        setFlash('error', 'Failed to book shipment. Please try again.');
    }
}

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-plus-circle-fill" style="color:var(--gt-primary)"></i> Book Shipment</h1>
        <p>Create a new transport booking</p>
    </div>

    <form method="POST" id="bookingForm">
        <div class="row g-4">
            <!-- Pickup Details -->
            <div class="col-lg-6">
                <div class="gt-section-card animate-slide-up">
                    <h6 style="font-weight:700;margin-bottom:20px;color:var(--gt-primary)">
                        <i class="bi bi-geo-alt me-2"></i>Pickup Details
                    </h6>
                    <div class="gt-form-group">
                        <label class="gt-label">Pickup Address</label>
                        <textarea name="pickup_address" class="gt-input" rows="2" placeholder="Full pickup address" required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-8">
                            <div class="gt-form-group">
                                <label class="gt-label">City</label>
                                <input type="text" name="pickup_city" class="gt-input" placeholder="City" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Pincode</label>
                                <input type="text" name="pickup_pincode" class="gt-input" placeholder="Pincode" maxlength="6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="col-lg-6">
                <div class="gt-section-card animate-slide-up delay-1">
                    <h6 style="font-weight:700;margin-bottom:20px;color:#6366f1">
                        <i class="bi bi-pin-map me-2"></i>Delivery Details
                    </h6>
                    <div class="gt-form-group">
                        <label class="gt-label">Delivery Address</label>
                        <textarea name="delivery_address" class="gt-input" rows="2" placeholder="Full delivery address" required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-8">
                            <div class="gt-form-group">
                                <label class="gt-label">City</label>
                                <input type="text" name="delivery_city" class="gt-input" placeholder="City" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Pincode</label>
                                <input type="text" name="delivery_pincode" class="gt-input" placeholder="Pincode" maxlength="6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Details -->
            <div class="col-lg-8">
                <div class="gt-section-card animate-slide-up">
                    <h6 style="font-weight:700;margin-bottom:20px;color:#f59e0b">
                        <i class="bi bi-box-seam me-2"></i>Package Details
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Package Type</label>
                                <select name="package_type" class="gt-input" required>
                                    <option value="parcel">Parcel</option>
                                    <option value="document">Document</option>
                                    <option value="fragile">Fragile</option>
                                    <option value="heavy">Heavy Goods</option>
                                    <option value="bulk">Bulk</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Weight (KG)</label>
                                <input type="number" name="weight_kg" class="gt-input" placeholder="Weight in KG" min="0.1" step="0.1" required id="weightInput">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="gt-form-group">
                                <label class="gt-label">Priority</label>
                                <select name="priority" class="gt-input" required id="priorityInput">
                                    <option value="normal">Normal</option>
                                    <option value="express">Express (1.5x)</option>
                                    <option value="overnight">Overnight (2x)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="gt-form-group">
                        <label class="gt-label">Additional Notes</label>
                        <textarea name="notes" class="gt-input" rows="2" placeholder="Special instructions (optional)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Cost Estimate -->
            <div class="col-lg-4">
                <div class="gt-section-card animate-slide-up delay-1" style="border:2px solid var(--gt-primary);background:rgba(var(--gt-primary-rgb),0.03)">
                    <h6 style="font-weight:700;margin-bottom:20px;color:var(--gt-primary)">
                        <i class="bi bi-calculator me-2"></i>Cost Estimate
                    </h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Base Rate</span>
                            <strong id="baseRate">₹0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Priority Charge</span>
                            <strong id="priorityCharge">₹0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">GST (18%)</span>
                            <strong id="gstAmount">₹0</strong>
                        </div>
                        <hr style="border-color:var(--gt-border-color)">
                        <div class="d-flex justify-content-between">
                            <span style="font-weight:700;font-size:1.1rem">Total</span>
                            <strong style="font-size:1.3rem;color:var(--gt-primary)" id="totalCost">₹0</strong>
                        </div>
                    </div>
                    <input type="hidden" name="estimated_cost" id="estimatedCostInput" value="0">
                    <button type="submit" class="btn-gt-primary w-100 mt-4" style="padding:14px;font-size:1rem">
                        <i class="bi bi-check-circle"></i> Book Shipment
                    </button>
                </div>
            </div>
        </div>
    </form>

<script>
// Dynamic cost calculator
const ratePerKg = 15;
const multipliers = { normal: 1, express: 1.5, overnight: 2 };

function calculateCost() {
    const weight = parseFloat(document.getElementById('weightInput').value) || 0;
    const priority = document.getElementById('priorityInput').value;
    const mult = multipliers[priority];
    
    const base = weight * ratePerKg * 100;
    const priorityCharge = base * (mult - 1);
    const subtotal = base + priorityCharge;
    const gst = subtotal * 0.18;
    const total = subtotal + gst;
    
    document.getElementById('baseRate').textContent = '₹' + Math.round(base).toLocaleString('en-IN');
    document.getElementById('priorityCharge').textContent = '₹' + Math.round(priorityCharge).toLocaleString('en-IN');
    document.getElementById('gstAmount').textContent = '₹' + Math.round(gst).toLocaleString('en-IN');
    document.getElementById('totalCost').textContent = '₹' + Math.round(total).toLocaleString('en-IN');
    document.getElementById('estimatedCostInput').value = total.toFixed(2);
}

document.getElementById('weightInput').addEventListener('input', calculateCost);
document.getElementById('priorityInput').addEventListener('change', calculateCost);
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>

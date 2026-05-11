<?php
/**
 * GreenTrans - Customer Invoices
 */
require_once __DIR__ . '/../config/config.php';
requireRole('customer');
define('PAGE_TITLE', 'Invoices');

$pdo = getDBConnection();
$invoices = $pdo->prepare("SELECT i.*, s.tracking_id, s.pickup_city, s.delivery_city FROM invoices i JOIN shipments s ON i.shipment_id = s.id WHERE i.customer_id = ? ORDER BY i.created_at DESC");
$invoices->execute([$_SESSION['user_id']]);
$invoices = $invoices->fetchAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <h1><i class="bi bi-receipt" style="color:var(--gt-primary)"></i> Invoices</h1>
        <p>View and download your invoices</p>
    </div>

    <div class="gt-section-card animate-slide-up">
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Invoice #</th><th>Shipment</th><th>Route</th><th>Amount</th><th>Tax</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td><strong><?= $inv['invoice_number'] ?></strong></td>
                        <td style="color:var(--gt-primary)"><?= $inv['tracking_id'] ?></td>
                        <td style="font-size:0.85rem"><?= $inv['pickup_city'] ?> → <?= $inv['delivery_city'] ?></td>
                        <td><?= formatIndianCurrency($inv['amount']) ?></td>
                        <td><?= formatIndianCurrency($inv['tax_amount']) ?></td>
                        <td><strong><?= formatIndianCurrency($inv['total_amount']) ?></strong></td>
                        <td><span class="badge-gt badge-<?= $inv['payment_status'] === 'paid' ? 'delivered' : 'pending' ?>"><?= ucfirst($inv['payment_status']) ?></span></td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($inv['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($invoices)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No invoices yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include INCLUDES_PATH . 'footer.php'; ?>

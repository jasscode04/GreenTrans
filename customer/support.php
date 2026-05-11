<?php
/**
 * GreenTrans - Customer Support
 */
require_once __DIR__ . '/../config/config.php';
requireRole('customer');
define('PAGE_TITLE', 'Support');

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketNum = 'TKT' . date('Y') . str_pad(mt_rand(1,9999), 4, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO support_tickets (ticket_number, user_id, subject, message, priority) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$ticketNum, $_SESSION['user_id'], sanitize($_POST['subject']), sanitize($_POST['message']), sanitize($_POST['priority'] ?? 'medium')]);
    setFlash('success', "Support ticket $ticketNum created successfully");
    redirect(APP_URL . '/customer/support.php');
}

$tickets = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
$tickets->execute([$_SESSION['user_id']]);
$tickets = $tickets->fetchAll();

include INCLUDES_PATH . 'header.php';
include INCLUDES_PATH . 'sidebar.php';
?>
<main class="gt-main">
<?php include INCLUDES_PATH . 'navbar.php'; ?>
<div class="gt-content">
    <div class="gt-page-header animate-slide-up">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-headset" style="color:var(--gt-primary)"></i> Support Center</h1>
                <p>Get help with your shipments</p>
            </div>
            <button class="btn-gt-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                <i class="bi bi-plus-circle"></i> New Ticket
            </button>
        </div>
    </div>

    <div class="gt-section-card animate-slide-up">
        <?php if (empty($tickets)): ?>
        <div class="gt-empty-state">
            <i class="bi bi-chat-square-check"></i>
            <h5>No Support Tickets</h5>
            <p class="text-muted">Create a ticket if you need help</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="gt-table">
                <thead><tr><th>Ticket #</th><th>Subject</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><strong><?= $t['ticket_number'] ?></strong></td>
                        <td><?= htmlspecialchars($t['subject']) ?></td>
                        <td><span class="badge-gt badge-<?= $t['priority'] === 'high' || $t['priority'] === 'urgent' ? 'cancelled' : 'pending' ?>"><?= ucfirst($t['priority']) ?></span></td>
                        <td><span class="badge-gt badge-<?= $t['status'] === 'resolved' ? 'delivered' : ($t['status'] === 'open' ? 'pending' : 'in_transit') ?>"><?= ucfirst(str_replace('_',' ',$t['status'])) ?></span></td>
                        <td style="font-size:0.85rem"><?= date('d M Y', strtotime($t['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="border-radius:var(--gt-radius-lg);border:none">
        <div class="modal-header"><h5 class="modal-title" style="font-weight:700">Create Support Ticket</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST"><div class="modal-body">
            <div class="gt-form-group"><label class="gt-label">Subject</label><input type="text" name="subject" class="gt-input" required placeholder="Brief description of issue"></div>
            <div class="gt-form-group"><label class="gt-label">Priority</label>
                <select name="priority" class="gt-input"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select>
            </div>
            <div class="gt-form-group"><label class="gt-label">Message</label><textarea name="message" class="gt-input" rows="4" required placeholder="Describe your issue in detail"></textarea></div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn-gt-primary">Submit Ticket</button></div>
        </form>
    </div></div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>

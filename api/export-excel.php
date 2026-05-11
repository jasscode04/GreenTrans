<?php
/**
 * GreenTrans - Export Report to Excel (CSV)
 */
require_once __DIR__ . '/../config/config.php';

// Only allow admin and manager to export reports
requireRole(['admin', 'manager']);

$pdo = getDBConnection();

// Fetch all shipments with relevant details
$query = "
    SELECT 
        s.tracking_id, 
        u.full_name AS customer_name,
        d.full_name AS driver_name,
        v.vehicle_number,
        s.pickup_city,
        s.delivery_city,
        s.package_type,
        s.weight_kg,
        s.status,
        s.shipping_cost,
        s.payment_status,
        s.created_at
    FROM shipments s
    LEFT JOIN users u ON s.customer_id = u.id
    LEFT JOIN users d ON s.driver_id = d.id
    LEFT JOIN vehicles v ON s.vehicle_id = v.id
    ORDER BY s.created_at DESC
";
$stmt = $pdo->query($query);
$shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = "GreenTrans_Report_" . date('Y-m-d_H-i') . ".csv";

// Set headers for download
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");

// Create file pointer connected to output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel display
fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

// Output headers
fputcsv($output, [
    'Tracking ID', 
    'Customer', 
    'Assigned Driver', 
    'Vehicle', 
    'Pickup City', 
    'Delivery City', 
    'Type', 
    'Weight (kg)', 
    'Status', 
    'Cost (INR)', 
    'Payment', 
    'Date'
]);

// Output data rows
foreach ($shipments as $row) {
    fputcsv($output, [
        $row['tracking_id'],
        $row['customer_name'] ?? 'Unknown',
        $row['driver_name'] ?? 'Unassigned',
        $row['vehicle_number'] ?? 'N/A',
        $row['pickup_city'],
        $row['delivery_city'],
        ucwords($row['package_type']),
        $row['weight_kg'],
        ucfirst($row['status']),
        $row['shipping_cost'],
        ucfirst($row['payment_status']),
        date('d M Y', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit();

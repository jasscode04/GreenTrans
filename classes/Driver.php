<?php
/**
 * GreenTrans - Driver Model
 */
require_once __DIR__ . '/Database.php';

class Driver {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT u.*, da.is_available, da.current_location 
             FROM users u LEFT JOIN driver_availability da ON u.id = da.driver_id 
             WHERE u.role = 'driver' ORDER BY u.full_name"
        );
    }
    
    public function getAvailable() {
        return $this->db->fetchAll(
            "SELECT u.*, da.current_location FROM users u 
             INNER JOIN driver_availability da ON u.id = da.driver_id 
             WHERE u.role='driver' AND u.is_active=1 AND da.is_available=1"
        );
    }
    
    public function toggleAvailability($driverId) {
        $current = $this->db->fetch("SELECT is_available FROM driver_availability WHERE driver_id = ?", [$driverId]);
        if ($current) {
            $new = $current['is_available'] ? 0 : 1;
            $this->db->update('driver_availability', ['is_available' => $new], 'driver_id = ?', [$driverId]);
            return $new;
        } else {
            $this->db->insert('driver_availability', ['driver_id' => $driverId, 'is_available' => 1]);
            return 1;
        }
    }
    
    public function getEarnings($driverId, $period = null) {
        $sql = "SELECT COALESCE(SUM(earnings), 0) FROM deliveries WHERE driver_id = ? AND status = 'delivered'";
        $params = [$driverId];
        if ($period === 'monthly') {
            $sql .= " AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)";
        }
        return $this->db->fetchColumn($sql, $params);
    }
    
    public function getDeliveryCount($driverId, $status = null) {
        $sql = "SELECT COUNT(*) FROM deliveries WHERE driver_id = ?";
        $params = [$driverId];
        if ($status) { $sql .= " AND status = ?"; $params[] = $status; }
        return $this->db->fetchColumn($sql, $params);
    }
    
    public function getPerformanceStats() {
        return $this->db->fetchAll(
            "SELECT u.id, u.full_name, 
                COUNT(d.id) as total_deliveries,
                SUM(CASE WHEN d.status='delivered' THEN 1 ELSE 0 END) as completed,
                COALESCE(SUM(d.earnings), 0) as total_earnings
             FROM users u LEFT JOIN deliveries d ON u.id = d.driver_id
             WHERE u.role = 'driver' GROUP BY u.id ORDER BY completed DESC"
        );
    }
}

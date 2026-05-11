<?php
/**
 * GreenTrans - Vehicle Model
 */
require_once __DIR__ . '/Database.php';

class Vehicle {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        try {
            $id = $this->db->insert('vehicles', $data);
            return ['success' => true, 'vehicle_id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to add vehicle'];
        }
    }
    
    public function getById($id) {
        return $this->db->fetch(
            "SELECT v.*, u.full_name as driver_name FROM vehicles v LEFT JOIN users u ON v.assigned_driver_id = u.id WHERE v.id = ?", [$id]
        );
    }
    
    public function getAll($status = null) {
        $sql = "SELECT v.*, u.full_name as driver_name FROM vehicles v LEFT JOIN users u ON v.assigned_driver_id = u.id";
        $params = [];
        if ($status) { $sql .= " WHERE v.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY v.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
    
    public function update($id, $data) {
        $this->db->update('vehicles', $data, 'id = ?', [$id]);
        return ['success' => true, 'message' => 'Vehicle updated'];
    }
    
    public function assignDriver($vehicleId, $driverId) {
        return $this->db->update('vehicles', ['assigned_driver_id' => $driverId], 'id = ?', [$vehicleId]);
    }
    
    public function updateStatus($id, $status) {
        return $this->db->update('vehicles', ['status' => $status], 'id = ?', [$id]);
    }
    
    public function countByStatus($status = null) {
        if ($status) return $this->db->count('vehicles', 'status = ?', [$status]);
        return $this->db->count('vehicles');
    }
    
    public function getAvailable() { return $this->getAll('available'); }
    
    public function getUtilizationStats() {
        return $this->db->fetch(
            "SELECT COUNT(*) as total,
                SUM(CASE WHEN status='available' THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status='in_transit' THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status='maintenance' THEN 1 ELSE 0 END) as maintenance,
                SUM(CASE WHEN status='retired' THEN 1 ELSE 0 END) as retired
             FROM vehicles"
        );
    }
}

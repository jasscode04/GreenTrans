<?php
/**
 * GreenTrans - Shipment Model
 * Handles shipment CRUD and tracking
 */

require_once __DIR__ . '/Database.php';

class Shipment {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Create a new shipment
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            $shipmentId = $this->db->insert('shipments', $data);
            
            // Add initial tracking entry
            $this->db->insert('shipment_tracking', [
                'shipment_id' => $shipmentId,
                'status' => 'pending',
                'location' => $data['pickup_city'],
                'remarks' => 'Shipment created',
                'updated_by' => $data['customer_id']
            ]);
            
            $this->db->commit();
            return ['success' => true, 'shipment_id' => $shipmentId];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to create shipment'];
        }
    }
    
    /**
     * Get shipment by ID
     */
    public function getById($id) {
        return $this->db->fetch(
            "SELECT s.*, 
                    c.full_name as customer_name, c.phone as customer_phone,
                    d.full_name as driver_name, d.phone as driver_phone,
                    v.vehicle_number, v.vehicle_type
             FROM shipments s
             LEFT JOIN users c ON s.customer_id = c.id
             LEFT JOIN users d ON s.driver_id = d.id
             LEFT JOIN vehicles v ON s.vehicle_id = v.id
             WHERE s.id = ?", [$id]
        );
    }
    
    /**
     * Get shipment by tracking ID
     */
    public function getByTrackingId($trackingId) {
        return $this->db->fetch(
            "SELECT s.*, 
                    c.full_name as customer_name,
                    d.full_name as driver_name,
                    v.vehicle_number
             FROM shipments s
             LEFT JOIN users c ON s.customer_id = c.id
             LEFT JOIN users d ON s.driver_id = d.id
             LEFT JOIN vehicles v ON s.vehicle_id = v.id
             WHERE s.tracking_id = ?", [$trackingId]
        );
    }
    
    /**
     * Get shipments by customer
     */
    public function getByCustomer($customerId, $status = null) {
        $sql = "SELECT s.*, d.full_name as driver_name, v.vehicle_number
                FROM shipments s
                LEFT JOIN users d ON s.driver_id = d.id
                LEFT JOIN vehicles v ON s.vehicle_id = v.id
                WHERE s.customer_id = ?";
        $params = [$customerId];
        
        if ($status) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get shipments by driver
     */
    public function getByDriver($driverId, $status = null) {
        $sql = "SELECT s.*, c.full_name as customer_name, c.phone as customer_phone,
                       v.vehicle_number
                FROM shipments s
                LEFT JOIN users c ON s.customer_id = c.id
                LEFT JOIN vehicles v ON s.vehicle_id = v.id
                WHERE s.driver_id = ?";
        $params = [$driverId];
        
        if ($status) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get all shipments with filters
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT s.*, 
                       c.full_name as customer_name,
                       d.full_name as driver_name,
                       v.vehicle_number
                FROM shipments s
                LEFT JOIN users c ON s.customer_id = c.id
                LEFT JOIN users d ON s.driver_id = d.id
                LEFT JOIN vehicles v ON s.vehicle_id = v.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['priority'])) {
            $sql .= " AND s.priority = ?";
            $params[] = $filters['priority'];
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update shipment status
     */
    public function updateStatus($shipmentId, $status, $location = '', $remarks = '', $updatedBy = null) {
        try {
            $this->db->beginTransaction();
            
            $updateData = ['status' => $status];
            if ($status === 'delivered') {
                $updateData['actual_delivery'] = date('Y-m-d H:i:s');
            }
            
            $this->db->update('shipments', $updateData, 'id = ?', [$shipmentId]);
            
            // Add tracking entry
            $this->db->insert('shipment_tracking', [
                'shipment_id' => $shipmentId,
                'status' => $status,
                'location' => $location,
                'remarks' => $remarks,
                'updated_by' => $updatedBy
            ]);
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Status updated'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
    
    /**
     * Get tracking history for a shipment
     */
    public function getTrackingHistory($shipmentId) {
        return $this->db->fetchAll(
            "SELECT st.*, u.full_name as updated_by_name
             FROM shipment_tracking st
             LEFT JOIN users u ON st.updated_by = u.id
             WHERE st.shipment_id = ?
             ORDER BY st.created_at ASC", [$shipmentId]
        );
    }
    
    /**
     * Assign driver and vehicle
     */
    public function assignDriverVehicle($shipmentId, $driverId, $vehicleId) {
        return $this->db->update('shipments', [
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId
        ], 'id = ?', [$shipmentId]);
    }
    
    /**
     * Count shipments by status
     */
    public function countByStatus($status, $userId = null, $role = null) {
        $sql = "SELECT COUNT(*) FROM shipments WHERE status = ?";
        $params = [$status];
        
        if ($userId && $role === 'customer') {
            $sql .= " AND customer_id = ?";
            $params[] = $userId;
        } elseif ($userId && $role === 'driver') {
            $sql .= " AND driver_id = ?";
            $params[] = $userId;
        }
        
        return $this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Get total revenue
     */
    public function getTotalRevenue($period = null) {
        $sql = "SELECT COALESCE(SUM(shipping_cost), 0) FROM shipments WHERE payment_status = 'paid'";
        $params = [];
        
        if ($period === 'monthly') {
            $sql .= " AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)";
        } elseif ($period === 'yearly') {
            $sql .= " AND YEAR(created_at) = YEAR(CURRENT_DATE)";
        }
        
        return $this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Get monthly revenue data for charts
     */
    public function getMonthlyRevenue($year = null) {
        $year = $year ?? date('Y');
        return $this->db->fetchAll(
            "SELECT MONTH(created_at) as month, 
                    COALESCE(SUM(shipping_cost), 0) as revenue,
                    COUNT(*) as total_shipments
             FROM shipments 
             WHERE YEAR(created_at) = ? AND payment_status = 'paid'
             GROUP BY MONTH(created_at)
             ORDER BY month", [$year]
        );
    }
    
    /**
     * Get delivery statistics for charts
     */
    public function getDeliveryStats() {
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'picked_up' THEN 1 ELSE 0 END) as picked_up
             FROM shipments"
        );
    }
    
    /**
     * Get recent shipments
     */
    public function getRecent($limit = 5) {
        return $this->db->fetchAll(
            "SELECT s.*, c.full_name as customer_name, d.full_name as driver_name
             FROM shipments s
             LEFT JOIN users c ON s.customer_id = c.id
             LEFT JOIN users d ON s.driver_id = d.id
             ORDER BY s.created_at DESC
             LIMIT ?", [(int)$limit]
        );
    }
}

<?php
/**
 * GreenTrans - Notification Model
 */
require_once __DIR__ . '/Database.php';

class Notification {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($userId, $title, $message, $type = 'system', $link = '') {
        return $this->db->insert('notifications', [
            'user_id' => $userId, 'title' => $title,
            'message' => $message, 'type' => $type, 'link' => $link
        ]);
    }
    
    public function getByUser($userId, $limit = 20) {
        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, (int)$limit]
        );
    }
    
    public function getUnreadCount($userId) {
        return $this->db->count('notifications', 'user_id = ? AND is_read = 0', [$userId]);
    }
    
    public function markAsRead($id) {
        return $this->db->update('notifications', ['is_read' => 1], 'id = ?', [$id]);
    }
    
    public function markAllRead($userId) {
        return $this->db->update('notifications', ['is_read' => 1], 'user_id = ? AND is_read = 0', [$userId]);
    }
    
    public function delete($id) {
        return $this->db->delete('notifications', 'id = ?', [$id]);
    }
}

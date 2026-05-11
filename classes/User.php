<?php
/**
 * GreenTrans - User Model
 * Handles user CRUD, authentication, and profile management
 */

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        // Check if email already exists
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            $userId = $this->db->insert('users', $data);
            return ['success' => true, 'user_id' => $userId, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Authenticate user login
     */
    public function login($email, $password) {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Update last login
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_image'] = $user['profile_image'];
        $_SESSION['user_approved'] = (int)$user['is_approved'];
        
        return ['success' => true, 'user' => $user, 'message' => 'Login successful'];
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE email = ?", [$email]) > 0;
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        try {
            $this->db->update('users', $data, 'id = ?', [$userId]);
            
            // Update session data if current user
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                if (isset($data['full_name'])) $_SESSION['user_name'] = $data['full_name'];
                if (isset($data['profile_image'])) $_SESSION['user_image'] = $data['profile_image'];
            }
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->getById($userId);
        
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->update('users', ['password' => $hashedPassword], 'id = ?', [$userId]);
        
        return ['success' => true, 'message' => 'Password changed successfully'];
    }
    
    /**
     * Generate password reset token
     */
    public function generateResetToken($email) {
        $user = $this->getByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->db->update('users', [
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        ], 'id = ?', [$user['id']]);
        
        return ['success' => true, 'token' => $token, 'message' => 'Reset token generated'];
    }
    
    /**
     * Get all users with optional role filter
     */
    public function getAll($role = null, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count users by role
     */
    public function countByRole($role = null) {
        if ($role) {
            return $this->db->count('users', 'role = ?', [$role]);
        }
        return $this->db->count('users');
    }
    
    /**
     * Toggle user status
     */
    public function toggleStatus($userId) {
        $user = $this->getById($userId);
        $newStatus = $user['is_active'] ? 0 : 1;
        $this->db->update('users', ['is_active' => $newStatus], 'id = ?', [$userId]);
        return $newStatus;
    }
    
    /**
     * Approve driver
     */
    public function approveDriver($userId) {
        $this->db->update('users', ['is_approved' => 1], 'id = ?', [$userId]);
        $this->db->update('driver_availability', ['is_available' => 1], 'driver_id = ?', [$userId]);
        $this->logActivity($userId, 'approval', 'Driver account approved by admin');
        return true;
    }
    
    /**
     * Upload profile image
     */
    public function uploadProfileImage($userId, $file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid image format'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
        $uploadPath = dirname(__DIR__) . '/uploads/profiles/' . $filename;
        
        // Create directory if not exists
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $this->updateProfile($userId, ['profile_image' => $filename]);
            return ['success' => true, 'filename' => $filename, 'message' => 'Image uploaded'];
        }
        
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    /**
     * Log user activity
     */
    public function logActivity($userId, $action, $description = '') {
        $this->db->insert('activity_log', [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    }
    
    /**
     * Get activity log for user
     */
    public function getActivityLog($userId, $limit = 20) {
        return $this->db->fetchAll(
            "SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, (int)$limit]
        );
    }
}

<?php
namespace App\Models;

use App\Core\Model;

/**
 * User Model
 */
class User extends Model {
    protected $table = 'users';

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Create new user
     */
    public function createUser($data) {
        return $this->create([
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        return $this->update($userId, $data);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role) {
        return $this->all(['role' => $role, 'status' => 'active']);
    }

    /**
     * Get user statistics
     */
    public function getStatistics() {
        $sql = "SELECT 
                    role,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE status = 'active'
                GROUP BY role";
        
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
}

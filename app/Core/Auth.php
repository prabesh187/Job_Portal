<?php
namespace App\Core;

use App\Models\User;

/**
 * Authentication Class
 * Handles user authentication and authorization
 */
class Auth {
    /**
     * Attempt to login user
     */
    public static function attempt($email, $password) {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                return false;
            }

            self::login($user);
            return true;
        }

        return false;
    }

    /**
     * Login user
     */
    public static function login($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['last_activity'] = time();
    }

    /**
     * Logout user
     */
    public static function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * Check if user is authenticated
     */
    public static function check() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Check session timeout
        $config = require __DIR__ . '/../../config/config.php';
        $timeout = $config['security']['session_lifetime'];
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Get authenticated user ID
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get authenticated user
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'name' => $_SESSION['user_name']
        ];
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::hasRole('admin');
    }

    /**
     * Check if user is employer
     */
    public static function isEmployer() {
        return self::hasRole('employer');
    }

    /**
     * Check if user is candidate
     */
    public static function isCandidate() {
        return self::hasRole('candidate');
    }
}

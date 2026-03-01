<?php
namespace App\Core;

/**
 * Base Controller Class
 * All controllers extend this class
 */
abstract class Controller {
    protected $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
    }

    /**
     * Load view file
     */
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: $view");
        }
    }

    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header("Location: " . $this->url($url));
        exit;
    }

    /**
     * Generate URL
     */
    protected function url($path = '') {
        $baseUrl = $this->config['app']['url'];
        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data with sanitization
     */
    protected function input($key, $default = null) {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    /**
     * Get GET data with sanitization
     */
    protected function query($key, $default = null) {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    /**
     * Sanitize input data
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken() {
        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!Auth::check()) {
            $this->redirect('login');
        }
    }

    /**
     * Check if user has specific role
     */
    protected function requireRole($role) {
        $this->requireAuth();
        if (!Auth::hasRole($role)) {
            $this->redirect('dashboard');
        }
    }
}

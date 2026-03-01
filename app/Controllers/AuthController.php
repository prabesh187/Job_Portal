<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Security;
use App\Models\User;

/**
 * Authentication Controller
 */
class AuthController extends Controller {
    
    /**
     * Show login form
     */
    public function showLogin() {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        $this->view('auth.login', ['csrf_token' => Security::generateCsrfToken()]);
    }

    /**
     * Handle login
     */
    public function login() {
        $this->validateCsrfToken();

        $email = $this->input('email');
        $password = $this->input('password');

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            $this->redirect('login');
        }

        if (Auth::attempt($email, $password)) {
            $this->redirect('dashboard');
        } else {
            $_SESSION['error'] = 'Invalid credentials';
            $this->redirect('login');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister() {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        $this->view('auth.register', ['csrf_token' => Security::generateCsrfToken()]);
    }

    /**
     * Handle registration
     */
    public function register() {
        $this->validateCsrfToken();

        $email = $this->input('email');
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        $fullName = $this->input('full_name');
        $role = $this->input('role');
        $phone = $this->input('phone');

        // Validation
        if (empty($email) || empty($password) || empty($fullName) || empty($role)) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('register');
        }

        if (!Security::validateEmail($email)) {
            $_SESSION['error'] = 'Invalid email format';
            $this->redirect('register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            $this->redirect('register');
        }

        if (!Security::validatePassword($password)) {
            $_SESSION['error'] = 'Password must be at least 8 characters with uppercase, lowercase, and number';
            $this->redirect('register');
        }

        if (!in_array($role, ['employer', 'candidate'])) {
            $_SESSION['error'] = 'Invalid role selected';
            $this->redirect('register');
        }

        $userModel = new User();
        
        // Check if email exists
        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email already registered';
            $this->redirect('register');
        }

        // Create user
        $userId = $userModel->createUser([
            'email' => $email,
            'password' => Security::hashPassword($password),
            'full_name' => $fullName,
            'role' => $role,
            'phone' => $phone
        ]);

        if ($userId) {
            // Create profile based on role
            if ($role === 'employer') {
                $db = \App\Core\Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO employer_profiles (user_id, company_name) VALUES (?, ?)");
                $stmt->execute([$userId, $fullName]);
            } else {
                $db = \App\Core\Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO candidate_profiles (user_id) VALUES (?)");
                $stmt->execute([$userId]);
            }

            $_SESSION['success'] = 'Registration successful! Please login.';
            $this->redirect('login');
        } else {
            $_SESSION['error'] = 'Registration failed';
            $this->redirect('register');
        }
    }

    /**
     * Logout
     */
    public function logout() {
        Auth::logout();
        $this->redirect('login');
    }
}

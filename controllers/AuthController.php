<?php
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $db;
    private $users;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getDatabase();
        $this->users = $this->db->users;
    }

    public function register($username, $password, $email, $role = 'customer') {
        // Cek apakah username sudah ada
        $existingUser = $this->users->findOne(['username' => $username]);
        if ($existingUser) {
            return ['success' => false, 'message' => 'Username sudah digunakan'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user baru
        $result = $this->users->insertOne([
            'username' => $username,
            'password' => $hashedPassword,
            'email' => $email,
            'role' => $role,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($result->getInsertedCount() > 0) {
            return ['success' => true, 'message' => 'Registrasi berhasil'];
        }
        return ['success' => false, 'message' => 'Gagal melakukan registrasi'];
    }

    public function login($username, $password) {
        $user = $this->users->findOne(['username' => $username]);
        
        if ($user && password_verify($password, $user->password)) {
            session_start();
            $_SESSION['user_id'] = (string)$user->_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;
            
            return ['success' => true, 'message' => 'Login berhasil', 'role' => $user->role];
        }
        
        return ['success' => false, 'message' => 'Username atau password salah'];
    }

    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Logout berhasil'];
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }
} 
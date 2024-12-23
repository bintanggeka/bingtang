<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi';
        header('Location: /auth/login.php');
        exit;
    }

    $auth = new AuthController();
    $result = $auth->login($username, $password);

    if ($result['success']) {
        // Redirect berdasarkan role
        if ($result['role'] === 'admin') {
            header('Location: /admin/dashboard.php');
        } else {
            header('Location: /customer/dashboard.php');
        }
        exit;
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /auth/login.php');
        exit;
    }
} else {
    header('Location: /auth/login.php');
    exit;
} 
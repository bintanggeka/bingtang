<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Semua field harus diisi';
        header('Location: /auth/register.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok';
        header('Location: /auth/register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Format email tidak valid';
        header('Location: /auth/register.php');
        exit;
    }

    $auth = new AuthController();
    $result = $auth->register($username, $password, $email);

    if ($result['success']) {
        $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
        header('Location: /auth/login.php');
        exit;
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /auth/register.php');
        exit;
    }
} else {
    header('Location: /auth/register.php');
    exit;
} 
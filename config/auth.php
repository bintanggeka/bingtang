<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /');
        exit();
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function login($user) {
    $_SESSION['user_id'] = (string) $user->_id;
    $_SESSION['role'] = $user->role;
    $_SESSION['name'] = $user->name;
}

function logout() {
    session_destroy();
    header('Location: /auth/login.php');
    exit();
} 
<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Console.php';

$auth = new AuthController();
if (!$auth->isLoggedIn() || $auth->getCurrentUser()['role'] !== 'admin') {
    header('Location: /auth/login.php');
    exit;
}

$console = new Console();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $data = [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'description' => $_POST['description'],
            'hourly_rate' => $_POST['hourly_rate'],
            'accessories' => array_map('trim', explode(',', $_POST['accessories'])),
            'games' => array_map('trim', explode(',', $_POST['games']))
        ];

        $result = $console->addConsole($data);
        if ($result) {
            $_SESSION['success'] = 'Konsol berhasil ditambahkan';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan konsol';
        }
        break;

    case 'edit':
        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'description' => $_POST['description'],
            'hourly_rate' => $_POST['hourly_rate'],
            'accessories' => array_map('trim', explode(',', $_POST['accessories'])),
            'games' => array_map('trim', explode(',', $_POST['games']))
        ];

        $result = $console->updateConsole($id, $data);
        if ($result) {
            $_SESSION['success'] = 'Konsol berhasil diperbarui';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui konsol';
        }
        break;

    case 'delete':
        $id = $_POST['id'];
        $result = $console->deleteConsole($id);
        if ($result) {
            $_SESSION['success'] = 'Konsol berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus konsol';
        }
        break;

    default:
        $_SESSION['error'] = 'Aksi tidak valid';
        break;
}

header('Location: /admin/consoles.php');
exit;

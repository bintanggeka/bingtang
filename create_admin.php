<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';

try {
    $userModel = new User();
    
    // Cek apakah admin sudah ada
    $admin = $userModel->getUserByEmail('admin@rentalps.com');
    
    if ($admin) {
        // Update password admin yang sudah ada
        $userModel->updateUser($admin->_id, [
            'password' => 'admin123'
        ]);
        echo "Password admin berhasil diperbarui.";
    } else {
        // Buat admin baru
        $userModel->createUser([
            'name' => 'Admin',
            'email' => 'admin@rentalps.com',
            'password' => 'admin123',
            'phone' => '08123456789',
            'address' => 'Jl. Admin No. 1',
            'role' => 'admin',
            'is_active' => true
        ]);
        echo "Admin berhasil dibuat.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 
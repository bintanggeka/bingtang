<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/User.php';

requireAdmin();

$userModel = new User();
$user = $userModel->getUserById($_SESSION['user_id']);

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $updateData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ];
        
        // Hanya update password jika diisi
        if (!empty($_POST['new_password'])) {
            // Verifikasi password lama
            if (!$userModel->verifyPassword($user->email, $_POST['current_password'])) {
                throw new Exception('Password saat ini tidak valid.');
            }
            $updateData['password'] = $_POST['new_password'];
        }
        
        $userModel->updateUser($_SESSION['user_id'], $updateData);
        $success = 'Profil berhasil diperbarui.';
        
        // Refresh data user
        $user = $userModel->getUserById($_SESSION['user_id']);
    } catch (Exception $e) {
        $error = 'Gagal memperbarui profil: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Rental PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --ps-blue: #00439C;
            --ps-light-blue: #0070CC;
            --ps-black: #000000;
            --ps-white: #FFFFFF;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        body {
            background: linear-gradient(135deg, rgba(0,67,156,0.1) 0%, rgba(0,112,204,0.1) 100%);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--ps-blue) 0%, var(--ps-light-blue) 100%);
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            min-height: calc(100vh - 56px);
            position: fixed;
            padding-top: 1rem;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 0.2rem 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 16.66667%;
            padding: 2rem;
        }

        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            animation: slideIn 0.8s ease-out;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .profile-header {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .profile-avatar i {
            font-size: 3rem;
            color: var(--ps-blue);
        }

        .profile-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .profile-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .profile-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: var(--ps-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--ps-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,67,156,0.25);
        }

        .password-section {
            background: rgba(0,67,156,0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
        }

        .password-section .form-label {
            color: var(--ps-blue);
        }

        .btn-playstation {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-playstation:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,67,156,0.3);
            color: white;
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: none;
            animation: slideIn 0.5s ease-out;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .playstation-shapes {
            position: fixed;
            font-size: 2rem;
            opacity: 0.1;
            color: var(--ps-blue);
            animation: float 4s ease-in-out infinite;
            z-index: -1;
        }

        .shape-1 { top: 10%; left: 10%; animation-delay: 0s; }
        .shape-2 { top: 20%; right: 10%; animation-delay: 1s; }
        .shape-3 { bottom: 10%; left: 15%; animation-delay: 2s; }
        .shape-4 { bottom: 20%; right: 15%; animation-delay: 3s; }

        .input-group-text {
            background: rgba(0,67,156,0.1);
            border: 1px solid #dee2e6;
            color: var(--ps-blue);
        }

        .text-muted {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>

    <!-- PlayStation Shapes Background -->
    <div class="playstation-shapes shape-1">○</div>
    <div class="playstation-shapes shape-2">□</div>
    <div class="playstation-shapes shape-3">△</div>
    <div class="playstation-shapes shape-4">✕</div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="consoles.php">
                            <i class="fas fa-gamepad"></i> Konsol
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rentals.php">
                            <i class="fas fa-clipboard-list"></i> Penyewaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="packages.php">
                            <i class="fas fa-box"></i> Paket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar"></i> Laporan
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
                    <h1 class="h2" style="color: var(--ps-blue); font-weight: 700;">Profil Admin</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h2 class="profile-title"><?php echo $user->name; ?></h2>
                                <p class="profile-subtitle"><?php echo $user->email; ?></p>
                            </div>
                            <div class="profile-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo $user->name; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo $user->email; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo $user->phone; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address" class="form-label">Alamat</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <textarea class="form-control" id="address" name="address" 
                                                      rows="3" required><?php echo $user->address; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="password-section">
                                        <h5 class="mb-4" style="color: var(--ps-blue);">
                                            <i class="fas fa-lock me-2"></i>Ubah Password
                                        </h5>
                                        
                                        <div class="form-group">
                                            <label for="current_password" class="form-label">Password Saat Ini</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-key"></i>
                                                </span>
                                                <input type="password" class="form-control" id="current_password" 
                                                       name="current_password">
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Isi hanya jika ingin mengubah password
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label for="new_password" class="form-label">Password Baru</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                                <input type="password" class="form-control" id="new_password" 
                                                       name="new_password">
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Minimal 8 karakter
                                            </small>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-playstation">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
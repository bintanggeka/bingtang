<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Rental.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Console.php';
require_once __DIR__ . '/../models/Package.php';

requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: rentals.php');
    exit();
}

$rentalModel = new Rental();
$userModel = new User();
$consoleModel = new Console();
$packageModel = new Package();

$rental = $rentalModel->getRentalById($_GET['id']);
if (!$rental) {
    header('Location: rentals.php');
    exit();
}

$user = $userModel->getUserById($rental->user_id);
$console = $consoleModel->getConsoleById($rental->console_id);
$package = $packageModel->getPackageById($rental->package_id);

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                try {
                    $rentalModel->updateRentalStatus($rental->_id, $_POST['status']);
                    
                    // Update console status
                    if ($_POST['status'] === 'completed') {
                        $consoleModel->updateStatus($rental->console_id, 'available');
                    }
                    
                    $success = 'Status penyewaan berhasil diperbarui.';
                    header('Location: rental_detail.php?id=' . (string) $rental->_id);
                    exit();
                } catch (Exception $e) {
                    $error = 'Gagal memperbarui status penyewaan.';
                }
                break;
        }
    }
}

// Get status text and class
$statusClass = '';
$statusText = '';
switch ($rental->status) {
    case 'pending':
        $statusClass = 'warning';
        $statusText = 'Menunggu';
        break;
    case 'active':
        $statusClass = 'success';
        $statusText = 'Aktif';
        break;
    case 'completed':
        $statusClass = 'info';
        $statusText = 'Selesai';
        break;
    case 'cancelled':
        $statusClass = 'danger';
        $statusText = 'Dibatalkan';
        break;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penyewaan - Rental PS</title>
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

        .detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            animation: slideIn 0.8s ease-out;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .detail-card .card-header {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1rem 1.5rem;
            border: none;
        }

        .detail-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .detail-card .card-header h5 i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .detail-card .card-body {
            padding: 1.5rem;
        }

        .table {
            margin: 0;
        }

        .table th {
            color: var(--ps-blue);
            font-weight: 600;
            width: 35%;
            padding: 1rem;
            border-top: none;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .btn-playstation {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-playstation:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,67,156,0.3);
            color: white;
        }

        .btn-back {
            background: rgba(0,0,0,0.1);
            color: var(--ps-blue);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(0,0,0,0.2);
            transform: translateX(-5px);
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

        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            border: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--ps-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,67,156,0.25);
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
                        <a class="nav-link active" href="rentals.php">
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
                    <h1 class="h2" style="color: var(--ps-blue); font-weight: 700;">Detail Penyewaan</h1>
                    <a href="rentals.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
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

                <div class="row">
                    <!-- Rental Information -->
                    <div class="col-md-6 mb-4">
                        <div class="detail-card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i>Informasi Penyewaan</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>ID Penyewaan</th>
                                        <td><?php echo (string) $rental->_id; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Mulai</th>
                                        <td><?php echo $rental->start_time->toDateTime()->format('d/m/Y H:i'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Selesai</th>
                                        <td><?php echo $rental->end_time->toDateTime()->format('d/m/Y H:i'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total Harga</th>
                                        <td>Rp <?php echo number_format($rental->total_price, 0, ',', '.'); ?></td>
                                    </tr>
                                </table>

                                <button type="button" class="btn btn-playstation mt-3" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#updateStatusModal">
                                    <i class="fas fa-edit me-2"></i>Update Status
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="col-md-6 mb-4">
                        <div class="detail-card">
                            <div class="card-header">
                                <h5><i class="fas fa-user"></i>Informasi Pelanggan</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Nama</th>
                                        <td><?php echo $user->name; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo $user->email; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telepon</th>
                                        <td><?php echo $user->phone; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td><?php echo $user->address; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Console Information -->
                    <div class="col-md-6 mb-4">
                        <div class="detail-card">
                            <div class="card-header">
                                <h5><i class="fas fa-gamepad"></i>Informasi Konsol</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Nama Konsol</th>
                                        <td><?php echo $console->name; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tipe</th>
                                        <td><?php echo $console->type; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Aksesoris</th>
                                        <td><?php echo implode(', ', iterator_to_array($console->accessories)); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Game</th>
                                        <td><?php echo implode(', ', iterator_to_array($console->games)); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Package Information -->
                    <div class="col-md-6 mb-4">
                        <div class="detail-card">
                            <div class="card-header">
                                <h5><i class="fas fa-box"></i>Informasi Paket</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Nama Paket</th>
                                        <td><?php echo $package->name; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Durasi</th>
                                        <td><?php echo $package->duration; ?> Jam</td>
                                    </tr>
                                    <tr>
                                        <th>Harga</th>
                                        <td>Rp <?php echo number_format($package->price, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td><?php echo $package->description; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Update Status Penyewaan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" <?php echo $rental->status === 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="active" <?php echo $rental->status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="completed" <?php echo $rental->status === 'completed' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="cancelled" <?php echo $rental->status === 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-playstation">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
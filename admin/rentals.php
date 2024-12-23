<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Rental.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Console.php';
require_once __DIR__ . '/../models/Package.php';

requireAdmin();

$rentalModel = new Rental();
$userModel = new User();
$consoleModel = new Console();
$packageModel = new Package();

$rentals = $rentalModel->getAllRentals();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                try {
                    $rentalModel->updateRentalStatus($_POST['rental_id'], $_POST['status']);
                    
                    // Update console status
                    if ($_POST['status'] === 'completed') {
                        $rental = $rentalModel->getRentalById($_POST['rental_id']);
                        $consoleModel->updateStatus($rental->console_id, 'available');
                    }
                    
                    $success = 'Status penyewaan berhasil diperbarui.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } catch (Exception $e) {
                    $error = 'Gagal memperbarui status penyewaan.';
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Penyewaan - Rental PS</title>
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

        .rental-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
            animation: slideIn 0.8s ease-out;
            overflow: hidden;
        }

        .rental-card .card-header {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1rem 1.5rem;
            border: none;
        }

        .rental-card .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .table {
            margin: 0;
        }

        .table th {
            border-top: none;
            color: var(--ps-blue);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            padding: 1rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .badge-active {
            background-color: #28a745;
            color: white;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-completed {
            background-color: #6c757d;
            color: white;
        }

        .badge-cancelled {
            background-color: #dc3545;
            color: white;
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

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
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

        .status-dropdown .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .status-dropdown .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .status-dropdown .dropdown-item:hover {
            background-color: rgba(0,67,156,0.1);
            color: var(--ps-blue);
        }

        .status-dropdown .dropdown-item i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
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
                    <h1 class="h2" style="color: var(--ps-blue); font-weight: 700;">Manajemen Penyewaan</h1>
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

                <!-- Rentals Table -->
                <div class="rental-card">
                    <div class="card-header">
                        <h5><i class="fas fa-clipboard-list me-2"></i>Daftar Penyewaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pengguna</th>
                                        <th>Konsol</th>
                                        <th>Paket</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rentals as $rental): 
                                        $user = $userModel->getUserById($rental->user_id);
                                        $console = $consoleModel->getConsoleById($rental->console_id);
                                        $package = $packageModel->getPackageById($rental->package_id);
                                    ?>
                                    <tr>
                                        <td><?php echo (string) $rental->_id; ?></td>
                                        <td><?php echo $user->name; ?></td>
                                        <td><?php echo $console->name; ?></td>
                                        <td><?php echo $package->name; ?></td>
                                        <td><?php echo date('d/m/Y H:i', $rental->start_time->toDateTime()->getTimestamp()); ?></td>
                                        <td><?php echo date('d/m/Y H:i', $rental->end_time->toDateTime()->getTimestamp()); ?></td>
                                        <td>Rp <?php echo number_format($rental->total_price, 0, ',', '.'); ?></td>
                                        <td>
                                            <div class="dropdown status-dropdown">
                                                <button class="badge badge-<?php echo strtolower($rental->status); ?> dropdown-toggle border-0" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <?php 
                                                        switch($rental->status) {
                                                            case 'pending':
                                                                echo 'Menunggu';
                                                                break;
                                                            case 'active':
                                                                echo 'Aktif';
                                                                break;
                                                            case 'completed':
                                                                echo 'Selesai';
                                                                break;
                                                            case 'cancelled':
                                                                echo 'Dibatalkan';
                                                                break;
                                                            default:
                                                                echo ucfirst($rental->status);
                                                        }
                                                    ?>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="POST" class="status-form">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="rental_id" value="<?php echo (string) $rental->_id; ?>">
                                                            <button type="submit" name="status" value="pending" class="dropdown-item">
                                                                <i class="fas fa-clock"></i> Menunggu
                                                            </button>
                                                            <button type="submit" name="status" value="active" class="dropdown-item">
                                                                <i class="fas fa-play"></i> Aktif
                                                            </button>
                                                            <button type="submit" name="status" value="completed" class="dropdown-item">
                                                                <i class="fas fa-check"></i> Selesai
                                                            </button>
                                                            <button type="submit" name="status" value="cancelled" class="dropdown-item">
                                                                <i class="fas fa-times"></i> Dibatalkan
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="rental_detail.php?id=<?php echo (string) $rental->_id; ?>" 
                                               class="btn btn-info btn-action">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Console.php';
require_once __DIR__ . '/../../models/Rental.php';

$auth = new AuthController();
if (!$auth->isLoggedIn() || $auth->getCurrentUser()['role'] !== 'admin') {
    header('Location: /auth/login.php');
    exit;
}

$console = new Console();
$rental = new Rental();

$totalConsoles = count($console->getAllConsoles());
$activeRentals = count($rental->getActiveRentals());
$totalIncome = $rental->getTotalIncome();
$mostRented = $rental->getMostRentedConsoles(5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PS Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="/admin/dashboard.php">
                                <i class="bx bxs-dashboard"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/consoles.php">
                                <i class="bx bx-game"></i>
                                Konsol
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/packages.php">
                                <i class="bx bx-package"></i>
                                Paket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/rentals.php">
                                <i class="bx bx-list-ul"></i>
                                Penyewaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/users.php">
                                <i class="bx bx-user"></i>
                                Pengguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/auth/logout.php">
                                <i class="bx bx-log-out"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Konsol</h5>
                                <h2 class="card-text"><?php echo $totalConsoles; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Penyewaan Aktif</h5>
                                <h2 class="card-text"><?php echo $activeRentals; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Pendapatan</h5>
                                <h2 class="card-text">Rp <?php echo number_format($totalIncome, 0, ',', '.'); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Most Rented Consoles -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Konsol Paling Sering Disewa</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Konsol</th>
                                                <th>Jumlah Penyewaan</th>
                                                <th>Total Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($mostRented as $item): 
                                                $consoleData = $console->getConsole($item->_id);
                                            ?>
                                            <tr>
                                                <td><?php echo $consoleData->name; ?></td>
                                                <td><?php echo $item->count; ?></td>
                                                <td>Rp <?php echo number_format($item->total_income, 0, ',', '.'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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
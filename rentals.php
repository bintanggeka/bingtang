<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/models/Rental.php';
require_once __DIR__ . '/models/Console.php';
require_once __DIR__ . '/models/Package.php';

requireLogin();

$rentalModel = new Rental();
$consoleModel = new Console();
$packageModel = new Package();

$rentals = $rentalModel->getRentalsByUser(getCurrentUserId());

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'cancel') {
        try {
            $rental = $rentalModel->getRentalById($_POST['rental_id']);
            if ($rental && $rental->status === 'pending') {
                $rentalModel->updateRentalStatus($rental->_id, 'cancelled');
                $consoleModel->updateStatus($rental->console_id, 'available');
                $success = 'Penyewaan berhasil dibatalkan.';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = 'Penyewaan tidak dapat dibatalkan.';
            }
        } catch (Exception $e) {
            $error = 'Gagal membatalkan penyewaan.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penyewaan - Rental PS</title>
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

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        body {
            background: linear-gradient(135deg, rgba(0,67,156,0.1) 0%, rgba(0,112,204,0.1) 100%);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
        }

        .page-title {
            color: var(--ps-blue);
            font-weight: 800;
            text-align: center;
            margin: 2rem 0;
            position: relative;
            padding-bottom: 15px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--ps-blue), var(--ps-light-blue));
        }

        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.8s ease-out;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            border-radius: 22px;
            z-index: -1;
            opacity: 0.7;
        }

        .card-header {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            font-weight: 600;
            padding: 1.2rem;
            border: none;
        }

        .card-body {
            padding: 2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            color: var(--ps-blue);
            font-weight: 600;
            border-bottom-width: 1px;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            animation: pulse 2s infinite;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000;
        }

        .badge.bg-success {
            background-color: #198754 !important;
        }

        .badge.bg-info {
            background-color: #0dcaf0 !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #ff4d5a);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease-out;
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
            border-left: 4px solid #0dcaf0;
        }

        .alert-link {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
        }

        .alert-link:hover {
            color: inherit;
            text-decoration: underline;
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

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
            .page-title {
                font-size: 1.8rem;
            }
            .table-responsive {
                margin: 0 -1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- PlayStation Shapes Background -->
    <div class="playstation-shapes shape-1">○</div>
    <div class="playstation-shapes shape-2">□</div>
    <div class="playstation-shapes shape-3">△</div>
    <div class="playstation-shapes shape-4">✕</div>

    <div class="container py-5">
        <h1 class="page-title">
            <i class="fas fa-history me-2"></i>Riwayat Penyewaan
        </h1>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($rentals)): ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>Anda belum memiliki riwayat penyewaan. 
                <a href="rent.php" class="alert-link">
                    <i class="fas fa-gamepad me-1"></i>Sewa sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($rentals as $rental): ?>
                <?php
                    $console = $consoleModel->getConsoleById($rental->console_id);
                    $package = $packageModel->getPackageById($rental->package_id);
                    
                    $statusClass = '';
                    $statusText = '';
                    $statusIcon = '';
                    switch ($rental->status) {
                        case 'pending':
                            $statusClass = 'warning';
                            $statusText = 'Menunggu';
                            $statusIcon = 'clock';
                            break;
                        case 'active':
                            $statusClass = 'success';
                            $statusText = 'Aktif';
                            $statusIcon = 'play-circle';
                            break;
                        case 'completed':
                            $statusClass = 'info';
                            $statusText = 'Selesai';
                            $statusIcon = 'check-circle';
                            break;
                        case 'cancelled':
                            $statusClass = 'danger';
                            $statusText = 'Dibatalkan';
                            $statusIcon = 'x-circle';
                            break;
                    }
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-gamepad me-2"></i><?php echo $console->name; ?> - <?php echo $console->type; ?>
                            </h5>
                            <span class="badge bg-<?php echo $statusClass; ?>">
                                <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i><?php echo $statusText; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th><i class="fas fa-hashtag me-2"></i>ID Penyewaan</th>
                                    <td><?php echo (string) $rental->_id; ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-box me-2"></i>Paket</th>
                                    <td><?php echo $package->name; ?> (<?php echo $package->duration; ?> Jam)</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-alt me-2"></i>Waktu Mulai</th>
                                    <td><?php echo $rental->start_time->toDateTime()->format('d/m/Y H:i'); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-check me-2"></i>Waktu Selesai</th>
                                    <td><?php echo $rental->end_time->toDateTime()->format('d/m/Y H:i'); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-money-bill-wave me-2"></i>Total Harga</th>
                                    <td>Rp <?php echo number_format($rental->total_price, 0, ',', '.'); ?></td>
                                </tr>
                                <?php if (!empty($rental->accessories)): ?>
                                <tr>
                                    <th><i class="fas fa-plug me-2"></i>Aksesoris</th>
                                    <td><?php echo implode(', ', iterator_to_array($rental->accessories)); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($rental->games)): ?>
                                <tr>
                                    <th><i class="fas fa-compact-disc me-2"></i>Game</th>
                                    <td><?php echo implode(', ', iterator_to_array($rental->games)); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>

                            <?php if ($rental->status === 'pending'): ?>
                            <form method="POST" class="mt-3" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penyewaan ini?');">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="rental_id" value="<?php echo (string) $rental->_id; ?>">
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-times-circle me-2"></i>Batalkan Penyewaan
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
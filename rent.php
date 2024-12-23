<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/models/Console.php';
require_once __DIR__ . '/models/Package.php';
require_once __DIR__ . '/models/Rental.php';

requireLogin();

$consoleModel = new Console();
$packageModel = new Package();
$rentalModel = new Rental();

$consoles = $consoleModel->getAllConsoles();
$packages = $packageModel->getAllPackages();

$selectedConsole = null;
$selectedPackage = null;

if (isset($_GET['console'])) {
    $selectedConsole = $consoleModel->getConsoleById($_GET['console']);
}

if (isset($_GET['package'])) {
    $selectedPackage = $packageModel->getPackageById($_GET['package']);
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $console = $consoleModel->getConsoleById($_POST['console_id']);
        $package = $packageModel->getPackageById($_POST['package_id']);
        
        if ($console && $package && $console->status === 'available') {
            $startTime = new DateTime($_POST['start_time']);
            $endTime = clone $startTime;
            $endTime->modify('+' . $package->duration . ' hours');
            
            $rental = $rentalModel->createRental([
                'user_id' => getCurrentUserId(),
                'console_id' => $console->_id,
                'package_id' => $package->_id,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s'),
                'total_price' => $package->price,
                'accessories' => isset($_POST['accessories']) ? $_POST['accessories'] : [],
                'games' => isset($_POST['games']) ? $_POST['games'] : []
            ]);

            // Update console status
            $consoleModel->updateStatus($console->_id, 'rented');
            
            $success = 'Penyewaan berhasil dibuat.';
            header('Location: rentals.php');
            exit();
        } else {
            $error = 'Konsol tidak tersedia.';
        }
    } catch (Exception $e) {
        $error = 'Gagal membuat penyewaan.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa PlayStation - Rental PS</title>
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

        .form-label {
            color: var(--ps-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-control, .form-select {
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--ps-blue);
            box-shadow: 0 0 15px rgba(0,67,156,0.2);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,67,156,0.3);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease-out;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .form-check {
            margin: 10px 0;
            padding-left: 2rem;
        }

        .form-check-input {
            border-color: var(--ps-blue);
        }

        .form-check-input:checked {
            background-color: var(--ps-blue);
            border-color: var(--ps-blue);
        }

        .form-check-label {
            color: var(--ps-black);
            font-weight: 500;
        }

        .section-title {
            color: var(--ps-blue);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
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
        <h1 class="page-title">Sewa PlayStation</h1>

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

        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <!-- Console Selection -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-gamepad me-2"></i>Pilih Konsol
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="console_id" class="form-label">
                                    <i class="fas fa-tv me-2"></i>Konsol
                                </label>
                                <select class="form-select" name="console_id" id="console_id" required>
                                    <option value="">Pilih Konsol</option>
                                    <?php foreach ($consoles as $console): ?>
                                    <?php if ($console->status === 'available'): ?>
                                    <option value="<?php echo (string) $console->_id; ?>" 
                                            <?php echo $selectedConsole && $selectedConsole->_id == $console->_id ? 'selected' : ''; ?>>
                                        <?php echo $console->name; ?> - <?php echo $console->type; ?>
                                    </option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($selectedConsole): ?>
                            <div class="mb-4">
                                <h6 class="section-title">
                                    <i class="fas fa-plug me-2"></i>Aksesoris Tersedia:
                                </h6>
                                <?php foreach ($selectedConsole->accessories as $accessory): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="accessories[]" 
                                           value="<?php echo $accessory; ?>" id="acc_<?php echo $accessory; ?>">
                                    <label class="form-check-label" for="acc_<?php echo $accessory; ?>">
                                        <?php echo $accessory; ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mb-4">
                                <h6 class="section-title">
                                    <i class="fas fa-compact-disc me-2"></i>Game Tersedia:
                                </h6>
                                <?php foreach ($selectedConsole->games as $game): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="games[]" 
                                           value="<?php echo $game; ?>" id="game_<?php echo $game; ?>">
                                    <label class="form-check-label" for="game_<?php echo $game; ?>">
                                        <?php echo $game; ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Package Selection -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-box me-2"></i>Pilih Paket
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="package_id" class="form-label">
                                    <i class="fas fa-clock me-2"></i>Paket Durasi
                                </label>
                                <select class="form-select" name="package_id" id="package_id" required>
                                    <option value="">Pilih Paket</option>
                                    <?php foreach ($packages as $package): ?>
                                    <option value="<?php echo (string) $package->_id; ?>"
                                            <?php echo $selectedPackage && $selectedPackage->_id == $package->_id ? 'selected' : ''; ?>>
                                        <?php echo $package->name; ?> - <?php echo $package->duration; ?> Jam - 
                                        Rp <?php echo number_format($package->price, 0, ',', '.'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="start_time" class="form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Waktu Mulai
                                </label>
                                <input type="datetime-local" class="form-control" id="start_time" 
                                       name="start_time" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Sewa Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Set minimum datetime for start_time
        const startTimeInput = document.getElementById('start_time');
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        startTimeInput.min = now.toISOString().slice(0, 16);
    </script>
</body>
</html> 
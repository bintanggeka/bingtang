<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Console.php';

requireAdmin();

$consoleModel = new Console();
$consoles = $consoleModel->getAllConsoles();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $consoleModel->addConsole([
                        'name' => $_POST['name'],
                        'type' => $_POST['type'],
                        'price_per_hour' => $_POST['price_per_hour'],
                        'accessories' => explode(',', $_POST['accessories']),
                        'games' => explode(',', $_POST['games'])
                    ]);
                    $success = 'Konsol berhasil ditambahkan.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } catch (Exception $e) {
                    $error = 'Gagal menambahkan konsol.';
                }
                break;

            case 'delete':
                try {
                    $consoleModel->deleteConsole($_POST['console_id']);
                    $success = 'Konsol berhasil dihapus.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } catch (Exception $e) {
                    $error = 'Gagal menghapus konsol.';
                }
                break;

            case 'edit':
                try {
                    // Konversi string aksesori dan game menjadi array
                    $accessories = array_filter(array_map('trim', explode(',', $_POST['accessories'])));
                    $games = array_filter(array_map('trim', explode(',', $_POST['games'])));
                    
                    $updateData = [
                        'name' => $_POST['name'],
                        'type' => $_POST['type'],
                        'price_per_hour' => (float)$_POST['price_per_hour'],
                        'accessories' => $accessories,
                        'games' => $games
                    ];
                    
                    $consoleModel->updateConsole($_POST['console_id'], $updateData);
                    $success = 'Konsol berhasil diperbarui.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } catch (Exception $e) {
                    $error = 'Gagal memperbarui konsol: ' . $e->getMessage();
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
    <title>Manajemen Konsol - Rental PS</title>
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

        .console-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
            animation: slideIn 0.8s ease-out;
            overflow: hidden;
        }

        .console-card .card-header {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1rem 1.5rem;
            border: none;
        }

        .console-card .card-header h5 {
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

        .badge-available {
            background-color: #28a745;
            color: white;
        }

        .badge-rented {
            background-color: #ffc107;
            color: #000;
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
                        <a class="nav-link active" href="consoles.php">
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
                    <h1 class="h2" style="color: var(--ps-blue); font-weight: 700;">Manajemen Konsol</h1>
                    <button type="button" class="btn btn-playstation" data-bs-toggle="modal" data-bs-target="#addConsoleModal">
                        <i class="fas fa-plus me-2"></i> Tambah Konsol
                    </button>
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

                <!-- Consoles Table -->
                <div class="console-card">
                    <div class="card-header">
                        <h5><i class="fas fa-gamepad me-2"></i>Daftar Konsol</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Harga/Jam</th>
                                        <th>Status</th>
                                        <th>Aksesoris</th>
                                        <th>Game</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consoles as $console): ?>
                                    <tr>
                                        <td><?php echo (string) $console->_id; ?></td>
                                        <td><?php echo $console->name; ?></td>
                                        <td><?php echo $console->type; ?></td>
                                        <td>Rp <?php echo number_format($console->price_per_hour, 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $console->status === 'available' ? 'available' : 'rented'; ?>">
                                                <?php echo $console->status === 'available' ? 'Tersedia' : 'Disewa'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo implode(', ', iterator_to_array($console->accessories)); ?></td>
                                        <td><?php echo implode(', ', iterator_to_array($console->games)); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-action" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editConsoleModal"
                                                    data-console-id="<?php echo (string) $console->_id; ?>"
                                                    data-console-name="<?php echo $console->name; ?>"
                                                    data-console-type="<?php echo $console->type; ?>"
                                                    data-console-price="<?php echo $console->price_per_hour; ?>"
                                                    data-console-accessories="<?php echo implode(',', iterator_to_array($console->accessories)); ?>"
                                                    data-console-games="<?php echo implode(',', iterator_to_array($console->games)); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus konsol ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="console_id" value="<?php echo (string) $console->_id; ?>">
                                                <button type="submit" class="btn btn-danger btn-action">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <!-- Add Console Modal -->
    <div class="modal fade" id="addConsoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Tambah Konsol
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Konsol</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="PS4">PlayStation 4</option>
                                <option value="PS5">PlayStation 5</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price_per_hour" class="form-label">Harga per Jam</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="price_per_hour" name="price_per_hour" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="accessories" class="form-label">Aksesoris (pisahkan dengan koma)</label>
                            <input type="text" class="form-control" id="accessories" name="accessories" 
                                   placeholder="Contoh: Controller, HDMI Cable, Power Cable">
                        </div>

                        <div class="mb-3">
                            <label for="games" class="form-label">Game (pisahkan dengan koma)</label>
                            <input type="text" class="form-control" id="games" name="games"
                                   placeholder="Contoh: FIFA 23, God of War, Spider-Man">
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

    <!-- Edit Console Modal -->
    <div class="modal fade" id="editConsoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Edit Konsol
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="console_id" id="edit_console_id">
                        
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Konsol</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_type" class="form-label">Tipe</label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="PS4">PlayStation 4</option>
                                <option value="PS5">PlayStation 5</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_price_per_hour" class="form-label">Harga per Jam</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_price_per_hour" name="price_per_hour" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_accessories" class="form-label">Aksesoris (pisahkan dengan koma)</label>
                            <input type="text" class="form-control" id="edit_accessories" name="accessories">
                        </div>

                        <div class="mb-3">
                            <label for="edit_games" class="form-label">Game (pisahkan dengan koma)</label>
                            <input type="text" class="form-control" id="edit_games" name="games">
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
    <script>
        // Handle Edit Console Modal
        document.getElementById('editConsoleModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const consoleId = button.getAttribute('data-console-id');
            const consoleName = button.getAttribute('data-console-name');
            const consoleType = button.getAttribute('data-console-type');
            const consolePrice = button.getAttribute('data-console-price');
            const consoleAccessories = button.getAttribute('data-console-accessories');
            const consoleGames = button.getAttribute('data-console-games');

            const modal = this;
            modal.querySelector('#edit_console_id').value = consoleId;
            modal.querySelector('#edit_name').value = consoleName;
            modal.querySelector('#edit_type').value = consoleType;
            modal.querySelector('#edit_price_per_hour').value = consolePrice;
            modal.querySelector('#edit_accessories').value = consoleAccessories;
            modal.querySelector('#edit_games').value = consoleGames;
        });
    </script>
</body>
</html> 
<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Console.php';

$auth = new AuthController();
if (!$auth->isLoggedIn() || $auth->getCurrentUser()['role'] !== 'admin') {
    header('Location: /auth/login.php');
    exit;
}

$console = new Console();
$consoles = $console->getAllConsoles();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Konsol - PS Rental</title>
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
                            <a class="nav-link text-white" href="/admin/dashboard.php">
                                <i class="bx bxs-dashboard"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="/admin/consoles.php">
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
                    <h1 class="h2">Manajemen Konsol</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addConsoleModal">
                        <i class="bx bx-plus"></i> Tambah Konsol
                    </button>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Consoles Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Harga per Jam</th>
                                <th>Aksesoris</th>
                                <th>Game</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consoles as $item): ?>
                            <tr>
                                <td><?php echo $item->name; ?></td>
                                <td><?php echo $item->type; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $item->status === 'available' ? 'success' : 'danger'; ?>">
                                        <?php echo $item->status === 'available' ? 'Tersedia' : 'Disewa'; ?>
                                    </span>
                                </td>
                                <td>Rp <?php echo number_format($item->hourly_rate, 0, ',', '.'); ?></td>
                                <td><?php echo implode(', ', $item->accessories); ?></td>
                                <td><?php echo implode(', ', $item->games); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editConsole('<?php echo $item->_id; ?>')">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteConsole('<?php echo $item->_id; ?>')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Console Modal -->
    <div class="modal fade" id="addConsoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Konsol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/console_process.php" method="POST">
                    <div class="modal-body">
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
                            <label for="hourly_rate" class="form-label">Harga per Jam</label>
                            <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="accessories" class="form-label">Aksesoris (pisahkan dengan koma)</label>
                            <input type="text" class="form-control" id="accessories" name="accessories">
                        </div>
                        <div class="mb-3">
                            <label for="games" class="form-label">Game (pisahkan dengan koma)</label>
                            <input type="text" class="form-control" id="games" name="games">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" name="action" value="add">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editConsole(id) {
            // Implement edit functionality
            window.location.href = `/admin/edit_console.php?id=${id}`;
        }

        function deleteConsole(id) {
            if (confirm('Apakah Anda yakin ingin menghapus konsol ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/console_process.php';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                form.appendChild(idInput);
                form.appendChild(actionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 
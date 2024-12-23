<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/models/User.php';

requireLogin();

$userModel = new User();
$user = $userModel->getUserById(getCurrentUserId());

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ];

        // Only update password if a new one is provided
        if (!empty($_POST['new_password'])) {
            if (empty($_POST['current_password'])) {
                $error = 'Password saat ini harus diisi untuk mengubah password.';
            } else if (!$userModel->verifyPassword($user->email, $_POST['current_password'])) {
                $error = 'Password saat ini salah.';
            } else {
                $data['password'] = $_POST['new_password'];
            }
        }

        if (empty($error)) {
            $userModel->updateUser($user->_id, $data);
            $success = 'Profil berhasil diperbarui.';
            
            // Update session name if it was changed
            if ($data['name'] !== $_SESSION['name']) {
                $_SESSION['name'] = $data['name'];
            }
            
            // Reload user data
            $user = $userModel->getUserById(getCurrentUserId());
        }
    } catch (Exception $e) {
        $error = 'Gagal memperbarui profil.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Rental PS</title>
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

        .form-label {
            color: var(--ps-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-control {
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--ps-blue);
            box-shadow: 0 0 15px rgba(0,67,156,0.2);
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
            width: 100%;
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

        hr {
            border-color: rgba(0,67,156,0.1);
            margin: 2rem 0;
        }

        .profile-icon {
            font-size: 3rem;
            color: var(--ps-blue);
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="fas fa-user-circle me-2"></i>Profil Saya
                </h1>

                <div class="card">
                    <div class="card-header">
                        <div class="text-center">
                            <i class="fas fa-user-circle profile-icon"></i>
                            <h4 class="card-title mb-0"><?php echo $user->name; ?></h4>
                        </div>
                    </div>
                    <div class="card-body">
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
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo $user->name; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo $user->email; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Nomor Telepon
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo $user->phone; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                </label>
                                <textarea class="form-control" id="address" name="address" 
                                          rows="3" required><?php echo $user->address; ?></textarea>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password Saat Ini
                                </label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>Isi hanya jika ingin mengubah password.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key me-2"></i>Password Baru
                                </label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
    </script>
</body>
</html> 
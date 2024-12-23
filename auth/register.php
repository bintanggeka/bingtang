<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/User.php';

if (isLoggedIn()) {
    header('Location: /');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();
    
    // Cek apakah email sudah terdaftar
    if ($userModel->getUserByEmail($_POST['email'])) {
        $error = 'Email sudah terdaftar.';
    } else {
        try {
            $userModel->createUser([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address']
            ]);
            $success = 'Registrasi berhasil! Silakan login.';
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan saat mendaftar.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Rental PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-color: var(--ps-black);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, rgba(0,67,156,0.3) 0%, rgba(0,112,204,0.3) 100%),
                url('https://wallpaperaccess.com/full/217097.jpg') center/cover;
            filter: blur(5px);
            z-index: -1;
        }

        .container {
            position: relative;
            z-index: 1;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.6);
            backdrop-filter: blur(10px);
            animation: slideIn 0.8s ease-out;
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

        .card-body {
            padding: 3rem;
        }

        .ps-logo {
            width: 150px;
            margin-bottom: 1.5rem;
            animation: float 6s ease-in-out infinite;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus {
            background: white;
            border-color: var(--ps-blue);
            box-shadow: 0 0 15px rgba(0,67,156,0.3);
        }

        .form-label {
            color: var(--ps-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,67,156,0.4);
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

        a {
            color: var(--ps-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--ps-light-blue);
            text-decoration: none;
        }

        .playstation-shapes {
            position: absolute;
            font-size: 2rem;
            opacity: 0.1;
            color: white;
            animation: float 4s ease-in-out infinite;
        }

        .shape-1 { top: 10%; left: 10%; animation-delay: 0s; }
        .shape-2 { top: 20%; right: 10%; animation-delay: 1s; }
        .shape-3 { bottom: 10%; left: 15%; animation-delay: 2s; }
        .shape-4 { bottom: 20%; right: 15%; animation-delay: 3s; }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--ps-blue);
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--ps-light-blue);
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 2rem;
            }
            .ps-logo {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <!-- PlayStation Shapes Background -->
    <div class="playstation-shapes shape-1">○</div>
    <div class="playstation-shapes shape-2">□</div>
    <div class="playstation-shapes shape-3">△</div>
    <div class="playstation-shapes shape-4">✕</div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/0/00/PlayStation_logo.svg" 
                                 alt="PlayStation Logo" 
                                 class="ps-logo">
                            <h2 class="mb-4" style="color: var(--ps-blue); font-weight: 700;">Daftar Akun Baru</h2>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Masukkan nama lengkap Anda" required>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Masukkan email Anda" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Masukkan password Anda" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Nomor Telepon
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="Masukkan nomor telepon Anda" required>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                </label>
                                <textarea class="form-control" id="address" name="address" 
                                          placeholder="Masukkan alamat lengkap Anda" rows="3" required></textarea>
                            </div>

                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Sudah punya akun? 
                                <a href="login.php" class="fw-bold">
                                    Masuk disini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 
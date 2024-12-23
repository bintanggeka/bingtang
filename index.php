<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/models/Console.php';
require_once __DIR__ . '/models/Package.php';

$consoleModel = new Console();
$packageModel = new Package();

$consoles = $consoleModel->getAllConsoles();
$packages = $packageModel->getAllPackages();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental PS</title>
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
            background-color: #f8f9fa;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://wallpaperaccess.com/full/217097.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 150px 0;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,67,156,0.3), rgba(0,112,204,0.3));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            animation: fadeIn 1s ease-out;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.25rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            margin-bottom: 2rem;
        }

        .btn-hero {
            padding: 15px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: 0.5s;
        }

        .btn-hero:hover::before {
            left: 100%;
        }

        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .section-title {
            color: var(--ps-blue);
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--ps-blue), var(--ps-light-blue));
        }

        .console-card, .package-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
            animation: fadeIn 0.8s ease-out;
        }

        .console-card:hover, .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,67,156,0.2);
        }

        .console-card img {
            height: 250px;
            object-fit: cover;
            transition: all 0.3s ease;
            width: 100%;
        }

        .console-card:hover img {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: var(--ps-blue);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .price-tag {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--ps-blue), var(--ps-light-blue));
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,67,156,0.4);
        }

        .features-section {
            background: linear-gradient(45deg, rgba(0,67,156,0.05), rgba(0,112,204,0.05));
            padding: 80px 0;
        }

        .feature-item {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,67,156,0.2);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--ps-blue);
            margin-bottom: 1.5rem;
        }

        .feature-title {
            color: var(--ps-blue);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-spacing {
            padding: 80px 0;
        }

        .package-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ps-blue);
        }

        @media (max-width: 768px) {
            .hero {
                padding: 100px 0;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .section-spacing {
                padding: 50px 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="container text-center">
                <h1 class="display-4 fw-bold mb-4">PlayStation Rental</h1>
            <p class="lead mb-4">Nikmati pengalaman gaming terbaik dengan konsol PlayStation terbaru</p>
            <?php if (!isLoggedIn()): ?>
                    <a href="auth/register.php" class="btn btn-primary btn-hero me-3">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </a>
                    <a href="auth/login.php" class="btn btn-outline-light btn-hero">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
            <?php else: ?>
                    <a href="rent.php" class="btn btn-primary btn-hero">
                        <i class="fas fa-gamepad me-2"></i>Sewa Sekarang
                    </a>
            <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Consoles Section -->
    <section class="section-spacing">
        <div class="container">
            <h2 class="section-title">Konsol Tersedia</h2>
            <div class="row">
                <?php foreach ($consoles as $console): ?>
                <?php if ($console->status === 'available'): ?>
                <div class="col-md-4 mb-4">
                    <div class="console-card">
                        <?php
                        $imageUrl = '';
                        switch(strtolower($console->type)) {
                            case 'ps4 slim':
                                $imageUrl = 'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=627&q=80';
                                break;
                            case 'ps4 pro':
                                $imageUrl = 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80';
                                break;
                            case 'ps5':
                                $imageUrl = 'https://images.unsplash.com/photo-1622297845775-5ff3fef71d13?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1506&q=80';
                                break;
                            default:
                                $imageUrl = 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80';
                        }
                        ?>
                        <img src="<?php echo $imageUrl; ?>" 
                             class="card-img-top" alt="<?php echo $console->name; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $console->name; ?></h5>
                            <div class="price-tag">
                                <i class="fas fa-clock me-1"></i>
                                Rp <?php echo number_format($console->price_per_hour, 0, ',', '.'); ?>/jam
                            </div>
                            <p class="card-text">
                                <i class="fas fa-gamepad me-2"></i>
                                <strong>Tipe:</strong> <?php echo $console->type; ?>
                            </p>
                            <div class="d-grid">
                                <?php if (isLoggedIn()): ?>
                                <a href="rent.php?console=<?php echo (string) $console->_id; ?>" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Sewa Sekarang
                                </a>
                                <?php else: ?>
                                <a href="auth/login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login untuk Menyewa
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="section-spacing bg-light">
        <div class="container">
            <h2 class="section-title">Paket Penyewaan</h2>
            <div class="row">
                <?php foreach ($packages as $package): ?>
                <div class="col-md-4 mb-4">
                    <div class="package-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $package->name; ?></h5>
                            <div class="price-tag">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo $package->duration; ?> Jam
                            </div>
                            <p class="card-text"><?php echo $package->description; ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="package-price">
                                    Rp <?php echo number_format($package->price, 0, ',', '.'); ?>
                                </div>
                                <?php if (isLoggedIn()): ?>
                                <a href="rent.php?package=<?php echo (string) $package->_id; ?>" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Pilih Paket
                                </a>
                                <?php else: ?>
                                <a href="auth/login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Mengapa Memilih Kami?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <i class="fas fa-gamepad feature-icon"></i>
                        <h4 class="feature-title">Konsol Terbaru</h4>
                        <p>Nikmati gaming dengan konsol PlayStation terbaru dan terawat dengan baik</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <i class="fas fa-clock feature-icon"></i>
                        <h4 class="feature-title">Fleksibel</h4>
                        <p>Pilih durasi penyewaan sesuai kebutuhan Anda dengan harga terjangkau</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h4 class="feature-title">Aman & Terpercaya</h4>
                        <p>Proses penyewaan yang aman dan terpercaya dengan pelayanan profesional</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
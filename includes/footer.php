<footer class="bg-dark text-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Rental PS</h5>
                <p>Nikmati pengalaman gaming terbaik dengan konsol PlayStation terbaru. Kami menyediakan layanan rental PS dengan harga terjangkau dan pelayanan terbaik.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Link Cepat</h5>
                <ul class="list-unstyled">
                    <li><a href="/" class="text-light text-decoration-none">Beranda</a></li>
                    <li><a href="/rent.php" class="text-light text-decoration-none">Sewa</a></li>
                    <?php if (isLoggedIn()): ?>
                    <li><a href="/rentals.php" class="text-light text-decoration-none">Riwayat Sewa</a></li>
                    <li><a href="/profile.php" class="text-light text-decoration-none">Profil</a></li>
                    <?php else: ?>
                    <li><a href="/auth/login.php" class="text-light text-decoration-none">Login</a></li>
                    <li><a href="/auth/register.php" class="text-light text-decoration-none">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Kontak</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-geo-alt"></i> Jl. Contoh No. 123, Kota</li>
                    <li><i class="bi bi-telephone"></i> +62 123 4567 890</li>
                    <li><i class="bi bi-envelope"></i> info@rentalps.com</li>
                </ul>
                <div class="mt-3">
                    <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <small>&copy; <?php echo date('Y'); ?> Rental PS. All rights reserved.</small>
        </div>
    </div>
</footer> 
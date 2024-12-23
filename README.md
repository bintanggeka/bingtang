# Aplikasi Rental PlayStation (PS)

Aplikasi manajemen rental PlayStation berbasis web yang dibangun menggunakan PHP dan MongoDB. Aplikasi ini memungkinkan pengelolaan rental konsol PlayStation secara efisien dengan antarmuka yang modern dan responsif.

![PlayStation Logo](https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/PlayStation_logo.svg/2560px-PlayStation_logo.svg.png)

## 🎮 Fitur Utama

### Admin Panel

- **Manajemen Konsol**

  - Tambah, edit, dan hapus konsol PlayStation
  - Kelola status ketersediaan konsol
  - Atur daftar game dan aksesoris untuk setiap konsol

- **Manajemen Penyewaan**

  - Lihat dan kelola semua transaksi penyewaan
  - Update status penyewaan (pending, active, completed, cancelled)
  - Detail informasi penyewaan lengkap

- **Manajemen Pengguna**

  - Kelola data pelanggan
  - Lihat riwayat penyewaan per pelanggan
  - Pengaturan profil admin

- **Manajemen Paket**

  - Atur berbagai paket penyewaan
  - Tentukan durasi dan harga paket
  - Aktifkan/nonaktifkan paket

- **Laporan**
  - Laporan pendapatan
  - Statistik konsol paling banyak disewa
  - Data pelanggan teratas
  - Filter laporan berdasarkan rentang tanggal

### User Panel

- Lihat daftar konsol tersedia
- Booking konsol dengan paket yang diinginkan
- Riwayat penyewaan
- Manajemen profil

## 💻 Teknologi yang Digunakan

- **Backend:** PHP 7.4+
- **Database:** MongoDB 5.0+
- **Frontend:**
  - HTML5
  - CSS3
  - JavaScript
  - Bootstrap 5.3
  - Font Awesome 6.0
- **Library/Framework Tambahan:**
  - MongoDB PHP Driver
  - Bootstrap Icons
  - Chart.js (untuk visualisasi data)

## 🚀 Cara Instalasi

1. **Prasyarat**

   ```bash
   - PHP 7.4 atau lebih tinggi
   - MongoDB 5.0 atau lebih tinggi
   - MongoDB PHP Driver
   - Composer
   ```

2. **Clone Repository**

   ```bash
   git clone https://github.com/username/rental-ps.git
   cd rental-ps
   ```

3. **Install Dependencies**

   ```bash
   composer install
   ```

4. **Konfigurasi Database**

   - Buat file `.env` dari `.env.example`

   ```bash
   cp .env.example .env
   ```

   - Sesuaikan konfigurasi MongoDB di file `.env`:

   ```env
   DB_HOST=localhost
   DB_PORT=27017
   DB_DATABASE=rental_ps
   DB_USERNAME=username
   DB_PASSWORD=password
   ```

5. **Import Data**

   ```bash
   # Jalankan migrasi untuk membuat struktur database dan data awal
   php migrations/init.php
   ```

6. **Jalankan Aplikasi**
   ```bash
   php -S localhost:8000
   ```

## 📱 Cara Penggunaan

### Admin

1. Login ke admin panel dengan kredensial default:

   - Email: admin@rental-ps.com
   - Password: admin123

2. **Mengelola Konsol**

   - Klik menu "Konsol"
   - Tambah konsol baru dengan mengklik tombol "Tambah Konsol"
   - Isi informasi konsol (nama, tipe, harga per jam, aksesoris, game)
   - Simpan perubahan

3. **Mengelola Penyewaan**

   - Klik menu "Penyewaan"
   - Lihat daftar penyewaan aktif
   - Update status penyewaan sesuai kebutuhan
   - Lihat detail penyewaan dengan mengklik tombol detail

4. **Melihat Laporan**
   - Klik menu "Laporan"
   - Pilih rentang tanggal yang diinginkan
   - Lihat statistik pendapatan dan penyewaan
   - Analisis konsol paling populer dan pelanggan teratas

### User

1. **Registrasi/Login**

   - Klik tombol "Daftar" untuk membuat akun baru
   - Login dengan akun yang sudah dibuat

2. **Menyewa Konsol**

   - Pilih konsol yang tersedia
   - Pilih paket penyewaan
   - Isi detail waktu sewa
   - Konfirmasi penyewaan

3. **Melihat Riwayat**
   - Klik menu "Riwayat"
   - Lihat status penyewaan
   - Cek detail setiap penyewaan

## 🔒 Keamanan

- Autentikasi user dengan password hashing
- Validasi input untuk semua form
- Proteksi terhadap SQL Injection
- CSRF Protection
- XSS Prevention

## 📄 Lisensi

Aplikasi ini dilisensikan di bawah [MIT License](LICENSE).

## 👨‍💻 Pengembang

Dikembangkan oleh [Nama Anda] - [Email Anda]

## 📞 Dukungan

Jika Anda menemukan bug atau memiliki saran, silakan buat issue baru di repository ini atau hubungi kami melalui:

- Email: support@rental-ps.com
- WhatsApp: +62 xxx-xxxx-xxxx

## 🙏 Terima Kasih

Terima kasih telah menggunakan aplikasi Rental PlayStation kami. Kami berharap aplikasi ini dapat membantu mengoptimalkan pengelolaan bisnis rental PlayStation Anda.

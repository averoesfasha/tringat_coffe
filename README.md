# Tringat Coffee — Website Pemesanan FIFO + QR Code

Starter project PHP native + MySQL untuk dijalankan di VS Code dengan Laragon/XAMPP.

## Fitur
- 33 menu awal Tringat Coffee dalam 6 kategori: Coffee, Non Coffee, Mocktail, Tea Series, Add On, dan Snack.
- Pilihan suhu Hot/Iced dan tingkat gula untuk minuman.
- Pelanggan memesan lewat halaman web, bisa dibuka dengan scan QR Code.
- Nomor antrean harian dengan format A001, A002, dan seterusnya.
- FIFO (First In, First Out): dashboard menampilkan pesanan berdasarkan ID/waktu masuk, dari yang paling awal.
- Status pesanan: Menunggu, Diproses, Selesai, Dibatalkan.
- Halaman konfirmasi dan pelacakan status pesanan.
- Area admin dilindungi login dengan session; dashboard, kelola menu, dan QR Code tidak bisa diakses sebelum login.
- Password admin disimpan menggunakan hash `password_hash()`/diverifikasi dengan `password_verify()`, serta form admin dilindungi token CSRF.
- Admin dapat mengelola menu dan membuat QR Code untuk halaman pemesanan.
- Ringkasan pesanan harian dan pendapatan dari pesanan berstatus Selesai.

## Instalasi di Laragon
1. Ekstrak folder `tringat_coffee` ke `C:\laragon\www\tringat_coffee`.
2. Jalankan Laragon, lalu Start All (Apache/Nginx dan MySQL).
3. Buka `http://localhost/phpmyadmin` atau HeidiSQL.
4. Import file `database.sql`.
5. Pastikan pengaturan database di `config.php` sesuai. Default Laragon: user `root`, password kosong.
6. Harga yang dimasukkan ke `database.sql` adalah harga contoh awal; sesuaikan di dashboard admin sebelum menerima transaksi nyata.
7. Buka `http://localhost/tringat_coffee`.
8. Buat akun admin pertama kali di `http://localhost/tringat_coffee/admin/setup_admin.php`. Gunakan username minimal 4 karakter dan password minimal 10 karakter.
9. Setelah akun berhasil dibuat, **hapus file `admin/setup_admin.php`** dari folder proyek. Halaman setup juga akan menolak akses setelah akun admin sudah ada.
10. Login admin melalui `http://localhost/tringat_coffee/admin/login.php`. Dashboard admin: `http://localhost/tringat_coffee/admin/`.
11. QR Code: `http://localhost/tringat_coffee/admin/qr.php` (harus login terlebih dahulu).

## Instalasi di XAMPP
Salin folder ke `C:\xampp\htdocs\tringat_coffee`, jalankan Apache dan MySQL, import `database.sql`, lalu buka `http://localhost/tringat_coffee`.

## Penggunaan QR Code
- Halaman QR memakai pustaka QRCode.js dari CDN, sehingga koneksi internet dibutuhkan saat membuat QR.
- Saat lokal, QR berisi URL localhost dan hanya bisa dibuka dari perangkat yang mengakses server tersebut. Untuk pelanggan umum, deploy website ke hosting/domain, lalu masukkan URL publik lengkap, misalnya `https://domainkamu.com/tringat_coffee/order.php`.
- QR hanya membuka halaman pemesanan; tidak otomatis membayar pesanan.

## Catatan penting sebelum produksi
- Sebelum website dipublikasikan, gunakan password admin yang kuat, hapus `admin/setup_admin.php`, aktifkan HTTPS, dan pastikan konfigurasi database hosting aman.
- Nomor antrean harian dibuat dari nomor tertinggi pada tanggal berjalan. Untuk banyak transaksi bersamaan, implementasikan tabel counter dan transaksi/locking agar nomor antrean dijamin unik.
- FIFO berarti pesanan lebih awal diproses lebih dahulu; admin tetap perlu mengubah status saat pesanan mulai dibuat dan selesai.
- Harga dalam rupiah dan menu contoh dapat diubah lewat dashboard atau database.


## Catatan pembaruan menu
- `database.sql` berisi 33 menu awal dan harga contoh yang dapat diubah melalui Kelola Menu.
- Pilihan Hot/Iced dan tingkat gula tersimpan pada detail item pesanan untuk kategori minuman.
- Jika database lama sudah pernah diimpor, ekspor/cadangkan dahulu data penting. Untuk pemasangan baru, gunakan database baru lalu impor `database.sql` yang disertakan. Skema lama perlu menambahkan kolom `temperature` dan `sugar_level` pada `order_items` sebelum memakai form pemesanan baru.


## Login admin
- Import `database.sql`; file ini membuat tabel `admin_users`. Jika database lama sudah ada, impor kembali file SQL atau jalankan bagian `CREATE TABLE admin_users` dari file tersebut melalui tab SQL phpMyAdmin. Cadangkan database terlebih dahulu.
- Buka `http://localhost/tringat_coffee/admin/setup_admin.php` untuk membuat akun admin pertama kali. Tidak ada username/password default.
- Setelah berhasil membuat akun, hapus `admin/setup_admin.php` dari folder `admin`.
- Login di `http://localhost/tringat_coffee/admin/login.php`; untuk keluar gunakan tombol Logout.
- Semua halaman admin utama memeriksa session. Permintaan POST untuk mengubah status pesanan/menu dan logout juga memeriksa token CSRF.

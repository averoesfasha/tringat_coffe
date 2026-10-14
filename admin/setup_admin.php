<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

try {
    $count = (int)$pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
} catch (PDOException $e) {
    http_response_code(500);
    exit('Tabel admin_users belum tersedia. Import database.sql terlebih dahulu.');
}
if ($count > 0) {
    http_response_code(403);
    exit('Akun admin sudah dibuat. Demi keamanan, halaman setup dinonaktifkan. Hapus file setup_admin.php dari server.');
}

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifikasiAdminCsrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (!preg_match('/^[A-Za-z0-9_.-]{4,40}$/', $username)) {
        $error = 'Username harus 4–40 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
    } elseif (strlen($password) < 10) {
        $error = 'Password minimal 10 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'Akun gagal dibuat. Username mungkin sudah digunakan.';
        }
    }
}
$pageTitle = 'Setup Admin';
$assetPrefix = '../';
require __DIR__ . '/../includes/header.php';
?>
<section class="card" style="max-width:520px;margin:48px auto;padding:28px">
  <span class="eyebrow">Pengaturan pertama kali</span>
  <h1 class="page-title">Buat Akun Admin</h1>
  <?php if ($success): ?>
    <div class="notice" role="status">Akun admin berhasil dibuat. Demi keamanan, hapus file <code>admin/setup_admin.php</code> dari folder proyek setelah ini.</div>
    <p><a class="btn" href="login.php">Lanjut ke Login</a></p>
  <?php else: ?>
    <p class="muted">Halaman ini hanya aktif sebelum akun admin pertama dibuat.</p>
    <?php if ($error !== ''): ?><div class="notice" role="alert" style="margin:16px 0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(adminCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
      <div class="form-row"><label for="username">Username admin</label><input class="input" id="username" name="username" minlength="4" maxlength="40" pattern="[A-Za-z0-9_.-]+" required></div>
      <div class="form-row"><label for="password">Password (minimal 10 karakter)</label><input class="input" id="password" type="password" name="password" minlength="10" required autocomplete="new-password"></div>
      <div class="form-row"><label for="confirm_password">Ulangi password</label><input class="input" id="confirm_password" type="password" name="confirm_password" minlength="10" required autocomplete="new-password"></div>
      <button class="btn" type="submit">Buat Akun Admin</button>
    </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>

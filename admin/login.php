<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$next = $_GET['next'] ?? 'index.php';
$allowedNext = ['index.php', 'menu.php', 'qr.php'];
if (!is_string($next) || !in_array($next, $allowedNext, true)) {
    $next = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifikasiAdminCsrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $nextPost = $_POST['next'] ?? 'index.php';
    if (!is_string($nextPost) || !in_array($nextPost, $allowedNext, true)) {
        $nextPost = 'index.php';
    }

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int)$admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                unset($_SESSION['admin_csrf']);
                header('Location: ' . $nextPost);
                exit;
            }
            // Pesan sama untuk username/password agar tidak membocorkan akun yang terdaftar.
            $error = 'Username atau password salah.';
        } catch (PDOException $e) {
            $error = 'Tabel akun admin belum tersedia. Import database.sql atau jalankan pembaruan database.';
        }
    }
}
$pageTitle = 'Login Admin';
$assetPrefix = '../';
require __DIR__ . '/../includes/header.php';
?>
<section class="card" style="max-width:480px;margin:48px auto;padding:28px">
  <span class="eyebrow">Area terbatas</span>
  <h1 class="page-title">Login Admin</h1>
  <p class="muted">Masuk untuk mengelola menu, pesanan, dan QR Code Tringat Coffee.</p>
  <?php if ($error !== ''): ?><div class="notice" role="alert" style="margin:16px 0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <form method="post" autocomplete="on">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(adminCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">
    <div class="form-row"><label for="username">Username</label><input class="input" id="username" name="username" autocomplete="username" required maxlength="80" autofocus></div>
    <div class="form-row"><label for="password">Password</label><input class="input" id="password" type="password" name="password" autocomplete="current-password" required></div>
    <button class="btn" type="submit" style="width:100%">Login</button>
  </form>
  <p class="small muted" style="margin-top:18px">Belum punya akun admin? Jalankan <code>setup_admin.php</code> satu kali untuk membuat akun pertama.</p>
  <a href="../index.php" class="small">Kembali ke website pelanggan</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>

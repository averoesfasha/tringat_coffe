<?php
// Autentikasi dan session khusus area admin.
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function wajibLogin(): void
{
    if (empty($_SESSION['admin_id'])) {
        $next = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
        $allowed = ['index.php', 'menu.php', 'qr.php'];
        if (!in_array($next, $allowed, true)) {
            $next = 'index.php';
        }
        header('Location: login.php?next=' . rawurlencode($next));
        exit;
    }
}

function adminCsrfToken(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function verifikasiAdminCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['admin_csrf']) || !hash_equals($_SESSION['admin_csrf'], $token)) {
        http_response_code(403);
        exit('Permintaan tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

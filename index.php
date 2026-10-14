<?php require 'config.php'; $pageTitle='Beranda'; $menus=$pdo->query('SELECT * FROM menu WHERE active=1 ORDER BY category,name')->fetchAll(); require 'includes/header.php'; ?>
<section class="hero"><span class="eyebrow">Coffee · Comfort · Community</span><h1>Tringat Coffe <h1>Good coffee.</h1><h1>Less waiting.</h1><p>Nikmati kopi favoritmu dari meja atau dari rumah. Scan QR Code, pilih menu, lalu pantau nomor antreanmu tanpa perlu mengantre di kasir.</p><div><a class="btn gold" href="order.php">Mulai Pesan ↗</a> <a class="btn" href="admin/index.php">Dashboard Admin</a></div></section>
<div class="section-head"><div><span class="eyebrow">Menu pilihan</span><h2>Temukan favoritmu</h2></div><a href="order.php" class="muted">Lihat & pesan menu →</a></div>
<div class="grid"><?php foreach($menus as $m): ?><article class="card menu-card"><div class="menu-icon"><?= $m['category']==='Snack'?'🥐':(in_array($m['category'],['Non Coffee','Tea Series'],true)?'🍵':($m['category']==='Mocktail'?'🧊':($m['category']==='Add On'?'➕':'☕'))) ?></div><span class="pill"><?= htmlspecialchars($m['category']) ?></span><h3><?= htmlspecialchars($m['name']) ?></h3><p class="muted"><?= htmlspecialchars($m['description']) ?></p><div class="price"><?= rupiah($m['price']) ?></div><a class="btn light" href="order.php">Pesan menu</a></article><?php endforeach; ?></div>
<div class="card" style="margin-top:24px;display:flex;gap:20px;align-items:center;justify-content:space-between;flex-wrap:wrap"><div><span class="eyebrow">Praktis & tertib</span><h2 style="font-family:'Playfair Display';margin:8px 0">Pesan lewat QR Code</h2><p class="muted">Scan kode yang tersedia di meja atau kasir untuk langsung membuka halaman pemesanan.</p></div><a class="btn" href="admin/qr.php">Lihat QR Code</a></div>
<?php require 'includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config.php';


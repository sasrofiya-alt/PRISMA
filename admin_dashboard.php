<?php
require_once 'config.php';
require_once 'includes/functions.php';
require_admin();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='pengguna'")->fetchColumn();
$todayLogins = (int)$pdo->query("SELECT COUNT(*) FROM login_logs WHERE DATE(login_at)=CURDATE()")->fetchColumn();
$totalLogins = (int)$pdo->query("SELECT COUNT(*) FROM login_logs")->fetchColumn();
$recent = $pdo->query("SELECT l.login_at, u.nama, u.email FROM login_logs l JOIN users u ON u.id=l.user_id ORDER BY l.login_at DESC LIMIT 8")->fetchAll();
$pageTitle='Dashboard Admin'; require 'includes/header.php';
?>
<section class="home-hero"><div class="home-hero-content"><span class="home-kicker">PRISMA · ADMIN SPACE</span><p class="home-date"><?= e(date('d/m/Y')) ?></p><h1>Halo, <?= e($_SESSION['user_name'] ?? 'Admin') ?>! <span>✦</span></h1><p class="home-intro">Selamat datang di ruang pengelolaan PRISMA. Pantau akun pengguna, riwayat login, sekaligus kelola tugas dan catatanmu sendiri dari sini.</p><div class="home-hero-actions"><a class="btn home-btn-main" href="users.php">Lihat pengguna</a><a class="home-btn-link" href="login_history.php">Riwayat login →</a><a class="home-btn-link" href="logout.php" onclick="return confirm('Yakin ingin keluar dari akun Admin?')">↪ Keluar</a></div></div><div class="home-hero-art"><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><div class="hero-glass"><span>✧</span><strong>P.</strong><small>ADMIN SPACE</small></div></div></section>
<div class="home-section-heading"><div><p class="eyebrow">RINGKASAN APLIKASI</p><h2>Aktivitas PRISMA</h2></div></div>
<section class="stat-grid home-stats">
<article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon lilac">♙</span><span class="stat-mini-label">AKUN</span></div><p>Total pengguna</p><strong><?= $totalUsers ?></strong><small>Akun pengguna terdaftar</small></article>
<article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon mint">◷</span><span class="stat-mini-label">HARI INI</span></div><p>Login hari ini</p><strong><?= $todayLogins ?></strong><small>Login berhasil tercatat</small></article>
<article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon blue">↗</span><span class="stat-mini-label">RIWAYAT</span></div><p>Total login</p><strong><?= $totalLogins ?></strong><small>Seluruh catatan login berhasil</small></article>
</section>
<section class="panel home-upcoming-panel"><div class="panel-heading"><div><p class="eyebrow">AKTIVITAS TERBARU</p><h2>Login terakhir</h2></div><a class="text-link" href="login_history.php">Lihat semua →</a></div>
<?php if (!$recent): ?><div class="empty-state"><h3>Belum ada riwayat login</h3><p>Riwayat login berhasil akan muncul di sini.</p></div><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Nama</th><th>Email</th><th>Waktu login</th></tr></thead><tbody><?php foreach($recent as $r): ?><tr><td><?= e($r['nama']) ?></td><td><?= e($r['email']) ?></td><td><?= e(date('d M Y, H:i',strtotime($r['login_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require 'includes/footer.php'; ?>

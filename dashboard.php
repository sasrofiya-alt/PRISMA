<?php
require_once 'config.php';
require_once 'includes/functions.php';
require_user();

$uid = current_user_id();
$name = $_SESSION['user_name'] ?? 'Pengguna';

$counts = ['total' => 0, 'Belum' => 0, 'Dikerjakan' => 0, 'Selesai' => 0];
$stmt = $pdo->prepare('SELECT status, COUNT(*) AS jumlah FROM tasks WHERE user_id = ? GROUP BY status');
$stmt->execute([$uid]);
foreach ($stmt->fetchAll() as $row) {
    if (isset($counts[$row['status']])) {
        $counts[$row['status']] = (int) $row['jumlah'];
        $counts['total'] += (int) $row['jumlah'];
    }
}

$stmt = $pdo->prepare("SELECT id, judul_tugas, mata_pelajaran, deadline, prioritas, status FROM tasks WHERE user_id = ? AND status <> 'Selesai' ORDER BY deadline ASC LIMIT 5");
$stmt->execute([$uid]);
$upcoming = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM saved_links WHERE user_id = ?');
$stmt->execute([$uid]);
$linkCount = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM notes WHERE user_id = ?');
$stmt->execute([$uid]);
$noteCount = (int) $stmt->fetchColumn();

$progress = $counts['total'] > 0 ? (int) round(($counts['Selesai'] / $counts['total']) * 100) : 0;
$hour = (int) date('H');
$greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
$bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$tanggalHariIni = date('j') . ' ' . $bulan[(int) date('n')] . ' ' . date('Y');

$pageTitle = 'Beranda';
require 'includes/header.php';
?>

<section class="home-hero">
    <div class="home-hero-content">
        <span class="home-kicker">PRISMA · PERSONAL PRODUCTIVITY SPACE</span>
        <p class="home-date"><?= e($tanggalHariIni) ?></p>
        <h1><?= e($greeting) ?>,<br><?= e($name) ?>! <span>✦</span></h1>
        <p class="home-intro">Semua tugas, link, dan catatan pentingmu ada di satu tempat. Yuk, lanjutkan langkah kecilmu hari ini.</p>
        <div class="home-hero-actions">
            <a class="btn home-btn-main" href="task_form.php">＋ Tambah tugas</a>
            <a class="home-btn-link" href="tasks.php">Lihat daftar tugas <span>→</span></a>
        </div>
    </div>
    <div class="home-hero-art" aria-hidden="true">
        <div class="orbit orbit-one"></div><div class="orbit orbit-two"></div>
        <div class="hero-glass"><span>✧</span><strong>P.</strong><small>PLAN · SAVE · GROW</small></div>
        <i class="spark spark-one">✦</i><i class="spark spark-two">✧</i><i class="spark spark-three">·</i>
    </div>
</section>

<div class="home-section-heading">
    <div><p class="eyebrow">RINGKASAN AKTIVITAS</p><h2>Ruang produktivitasmu</h2></div>
    <span class="home-live"><i></i> Data akunmu</span>
</div>

<section class="stat-grid home-stats">
    <article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon lilac">▣</span><span class="stat-mini-label">KESELURUHAN</span></div><p>Total tugas</p><strong><?= $counts['total'] ?></strong><small>Tugas yang kamu simpan</small></article>
    <article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon amber">◷</span><span class="stat-mini-label">PERLU DILAKUKAN</span></div><p>Belum dikerjakan</p><strong><?= $counts['Belum'] ?></strong><small>Siapkan langkah pertamamu</small></article>
    <article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon blue">↗</span><span class="stat-mini-label">SEDANG BERJALAN</span></div><p>Dikerjakan</p><strong><?= $counts['Dikerjakan'] ?></strong><small>Tetap konsisten, ya</small></article>
    <article class="stat-card home-stat"><div class="stat-top"><span class="stat-icon mint">✓</span><span class="stat-mini-label">TELAH TERCAPAI</span></div><p>Selesai</p><strong><?= $counts['Selesai'] ?></strong><small>Setiap progres berarti</small></article>
</section>

<section class="home-lower-grid">
    <div class="panel home-progress-panel">
        <div class="panel-heading"><div><p class="eyebrow">PERKEMBANGANMU</p><h2>Progress tugas</h2></div><span class="progress-number"><?= $progress ?>%</span></div>
        <div class="progress-track"><span style="width: <?= $progress ?>%"></span></div>
        <div class="progress-caption"><span><?= $counts['Selesai'] ?> dari <?= $counts['total'] ?> tugas selesai</span><span><?= $counts['total'] - $counts['Selesai'] ?> tersisa</span></div>
        <div class="home-tip"><span>✧</span><p>Fokus pada satu tugas dalam satu waktu. Tidak harus sempurna, yang penting terus bergerak.</p></div>
    </div>
    <div class="panel home-shortcuts-panel">
        <p class="eyebrow">SEMUA DALAM SATU RUANG</p><h2>Akses cepat</h2>
        <a class="home-shortcut" href="links.php"><span class="shortcut-icon shortcut-green">↗</span><span><b>Link tersimpan</b><small><?= $linkCount ?> link penting</small></span><i>→</i></a>
        <a class="home-shortcut" href="notes.php"><span class="shortcut-icon shortcut-peach">▤</span><span><b>Catatan</b><small><?= $noteCount ?> catatan pribadi</small></span><i>→</i></a>
        <a class="home-shortcut" href="todos.php"><span class="shortcut-icon shortcut-lilac">☑</span><span><b>To-Do List</b><small>Atur kegiatan harian</small></span><i>→</i></a>
        <a class="home-shortcut" href="calendar.php"><span class="shortcut-icon shortcut-yellow">▦</span><span><b>Kalender</b><small>Pantau deadline tugas</small></span><i>→</i></a>
    </div>
</section>

<section class="panel home-upcoming-panel">
    <div class="panel-heading"><div><p class="eyebrow">TETAP TERORGANISIR</p><h2>Tugas yang perlu diperhatikan</h2></div><a class="text-link" href="tasks.php">Lihat semua →</a></div>
    <?php if (!$upcoming): ?>
        <div class="empty-state"><div class="empty-icon">✦</div><h3>Belum ada tugas aktif</h3><p>Tambahkan tugas pertamamu agar tidak ada hal penting yang terlewat.</p><a class="btn btn-soft" href="task_form.php">＋ Tambah tugas</a></div>
    <?php else: ?>
        <div class="task-list">
            <?php foreach ($upcoming as $task): ?>
                <div class="task-row">
                    <div class="task-check">□</div>
                    <div class="task-info"><strong><?= e($task['judul_tugas']) ?></strong><span><?= e($task['mata_pelajaran']) ?></span></div>
                    <div class="task-date">Deadline <?= e(date('d M Y', strtotime($task['deadline']))) ?></div>
                    <span class="status status-<?= strtolower(e($task['status'])) ?>"><?= e($task['status']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>

<?php
require_once __DIR__ . '/functions.php';
require_login();
$pageTitle = $pageTitle ?? 'Dashboard';
$currentName = $_SESSION['user_name'] ?? 'Pengguna';
$isAdmin = current_user_role() === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | PRISMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= $isAdmin ? 'admin_dashboard.php' : 'dashboard.php' ?>">
            <span class="brand-mark">P</span><span>PRISMA<small>PRODUCTIVITY SPACE</small></span>
        </a>

        <?php if ($isAdmin): ?>
            <div class="nav-label">MENU ADMIN</div>
            <nav class="nav-menu" style="margin-bottom: 14px;">
                <a class="<?= active_page('admin_dashboard.php') ?>" href="admin_dashboard.php"><span>⌂</span> Dashboard Admin</a>
                <a class="<?= active_page('users.php') ?>" href="users.php"><span>♙</span> Data Pengguna</a>
                <a class="<?= active_page('login_history.php') ?>" href="login_history.php"><span>◷</span> Riwayat Login</a>
            </nav>
        <?php endif; ?>

        <div class="nav-label"><?= $isAdmin ? 'FITUR PENGGUNA' : 'MENU UTAMA' ?></div>
        <nav class="nav-menu">
            <a class="<?= active_page('dashboard.php') ?>" href="dashboard.php"><span>⌂</span> Beranda</a>
            <a class="<?= active_page('tasks.php') ?>" href="tasks.php"><span>▣</span> Tugas</a>
            <a class="<?= active_page('links.php') ?>" href="links.php"><span>↗</span> Simpan Link</a>
            <a class="<?= active_page('notes.php') ?>" href="notes.php"><span>▤</span> Catatan</a>
            <a class="<?= active_page('todos.php') ?>" href="todos.php"><span>☑</span> To-Do List</a>
            <a class="<?= active_page('calendar.php') ?>" href="calendar.php"><span>▦</span> Kalender</a>
        </nav>

        <div class="sidebar-bottom">
            <a class="<?= active_page('profile.php') ?>" href="profile.php"><span>◉</span> Profil Saya</a>
            <a href="logout.php" class="sidebar-logout" onclick="return confirm('Yakin ingin keluar dari akun?')"><span>↪</span> Keluar</a>
        </div>
        <div class="sidebar-note">Satu ruang untuk semua hal pentingmu.</div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button>
            <div class="breadcrumb">Ruang kerja <span>/</span> <strong><?= e($pageTitle) ?></strong></div>
            <div class="top-user">
                <div class="avatar"><?= e(strtoupper(substr($currentName, 0, 1))) ?></div>
                <span class="user-name-text"><?= e($currentName) ?></span>
                <?php if ($isAdmin): ?><span class="role-chip">Admin</span><?php endif; ?>
                <a href="logout.php" class="topbar-logout-btn" title="Keluar dari akun" onclick="return confirm('Yakin ingin keluar dari akun?')">↪ Keluar</a>
            </div>
        </header>
        <section class="page-content">
            <?php show_flash(); ?>

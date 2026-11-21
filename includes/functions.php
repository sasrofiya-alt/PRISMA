<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_id() {
    return (int)($_SESSION['user_id'] ?? 0);
}

function current_user_role() {
    return $_SESSION['user_role'] ?? 'pengguna';
}

function require_admin() {
    require_login();
    if (current_user_role() !== 'admin') {
        flash('Halaman tersebut khusus Admin.', 'error');
        header('Location: dashboard.php');
        exit;
    }
}

function require_user() {
    // Admin dan Pengguna sama-sama diizinkan mengakses semua fitur pengguna
    require_login();
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function show_flash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert '.e($f['type']).'">'.e($f['message']).'</div>';
        unset($_SESSION['flash']);
    }
}

function active_page($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}

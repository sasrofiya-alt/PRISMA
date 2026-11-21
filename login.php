<?php
require_once 'config.php';
require_once 'includes/functions.php';
if (!empty($_SESSION['user_id'])) { header('Location: ' . (($_SESSION['user_role'] ?? '') === 'admin' ? 'admin_dashboard.php' : 'dashboard.php')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, nama, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
        $pdo->prepare('INSERT INTO login_logs (user_id, login_at) VALUES (?, NOW())')->execute([$user['id']]);
        header('Location: ' . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php')); exit;
    }
    $error = 'Email atau password tidak sesuai.';
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login | PRISMA</title><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/style.css"></head>
<body class="auth-body"><div class="auth-wrap"><div class="auth-aside"><a class="brand brand-light" href="login.php"><span class="brand-mark">P</span><span>PRISMA<small>PRODUCTIVITY SPACE</small></span></a><div><p class="eyebrow">YOUR PERSONAL SPACE</p><h1>Stay focused.<br>Make it happen.</h1><p>Kelola tugas dan simpan semua hal penting dalam satu tempat.</p></div><div class="auth-decoration">P.</div></div><div class="auth-panel"><form class="auth-card" method="post"><p class="eyebrow">SELAMAT DATANG KEMBALI</p><h2>Masuk ke PRISMA</h2><p class="muted">Lanjutkan aktivitas produktifmu hari ini.</p><?php show_flash(); ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<label>Email</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="nama@email.com">
<label>Password</label>
<div class="password-field">
    <input type="password" name="password" id="loginPassword" required placeholder="Masukkan password">
    <button type="button" class="password-toggle" data-target="loginPassword" aria-label="Tampilkan atau sembunyikan password" title="Lihat password">
        <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        <svg class="icon-eye-off" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
    </button>
</div>
<button class="btn btn-primary btn-full" type="submit">Masuk <span>→</span></button><p class="auth-switch">Belum punya akun? <a href="register.php">Daftar sekarang</a></p></form></div></div><script src="assets/app.js"></script></body></html>

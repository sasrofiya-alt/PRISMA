<?php
require_once 'config.php';
require_once 'includes/functions.php';
if (!empty($_SESSION['user_id'])) { header('Location: ' . (($_SESSION['user_role'] ?? '') === 'admin' ? 'admin_dashboard.php' : 'dashboard.php')); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Isi nama dan email dengan benar. Password minimal 6 karakter.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'pengguna')");
            $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Akun berhasil dibuat. Silakan login.');
            header('Location: login.php'); exit;
        }
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Daftar | PRISMA</title><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/style.css"></head>
<body class="auth-body"><div class="auth-wrap"><div class="auth-aside"><a class="brand brand-light" href="login.php"><span class="brand-mark">P</span><span>PRISMA<small>PRODUCTIVITY SPACE</small></span></a><div><p class="eyebrow">MULAI LEBIH TERATUR</p><h1>Semua rencana,<br>di satu ruang.</h1><p>Simpan tugas, link, dan catatan pentingmu tanpa tercecer.</p></div><div class="auth-decoration">P.</div></div><div class="auth-panel"><form class="auth-card" method="post"><p class="eyebrow">BUAT AKUN BARU</p><h2>Daftar ke PRISMA</h2><p class="muted">Buat akun untuk mulai mengatur aktivitasmu.</p>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<label>Nama lengkap</label><input name="nama" required maxlength="100" value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama kamu">
<label>Email</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="nama@email.com">
<label>Password</label>
<div class="password-field">
    <input type="password" name="password" id="registerPassword" required minlength="6" placeholder="Minimal 6 karakter">
    <button type="button" class="password-toggle" data-target="registerPassword" aria-label="Tampilkan atau sembunyikan password" title="Lihat password">
        <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        <svg class="icon-eye-off" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
    </button>
</div>
<button class="btn btn-primary btn-full" type="submit">Buat Akun <span>→</span></button><p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></p></form></div></div><script src="assets/app.js"></script></body></html>

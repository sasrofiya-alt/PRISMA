<?php
require_once 'config.php';require_once 'includes/functions.php';require_login();$uid=current_user_id();$error='';
$s=$pdo->prepare('SELECT nama,email FROM users WHERE id=?');$s->execute([$uid]);$user=$s->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){$nama=trim($_POST['nama']??'');$email=trim($_POST['email']??'');$newPass=$_POST['password']??'';
 if($nama===''||!filter_var($email,FILTER_VALIDATE_EMAIL)){$error='Nama dan email harus diisi dengan benar.';}
 else{$s=$pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?');$s->execute([$email,$uid]);if($s->fetch()){$error='Email sudah digunakan akun lain.';}else{$s=$pdo->prepare('UPDATE users SET nama=?,email=? WHERE id=?');$s->execute([$nama,$email,$uid]);if($newPass!==''){if(strlen($newPass)<6){$error='Password baru minimal 6 karakter.';}else{$s=$pdo->prepare('UPDATE users SET password=? WHERE id=?');$s->execute([password_hash($newPass,PASSWORD_DEFAULT),$uid]);}}if(!$error){$_SESSION['user_name']=$nama;$_SESSION['user_email']=$email;flash('Profil berhasil diperbarui.');header('Location: profile.php');exit;}}}
}
$pageTitle='Profil Saya';require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">AKUN PRIBADI</p><h1>Profil Saya</h1><p class="muted">Perbarui informasi akun PRISMA-mu.</p></div></div>
<form class="panel form-panel narrow-panel" method="post"><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><div class="profile-avatar"><?= e(strtoupper(substr($user['nama'],0,1))) ?></div><div class="field"><label>Nama lengkap</label><input name="nama" required maxlength="100" value="<?= e($_POST['nama']??$user['nama']) ?>"></div><div class="field"><label>Email</label><input type="email" name="email" required value="<?= e($_POST['email']??$user['email']) ?>"></div><div class="field"><label>Password baru <span class="muted">(kosongkan jika tidak ingin mengubah)</span></label>
<div class="password-field">
    <input type="password" name="password" id="profilePassword" minlength="6" placeholder="Minimal 6 karakter">
    <button type="button" class="password-toggle" data-target="profilePassword" aria-label="Tampilkan atau sembunyikan password" title="Lihat password">
        <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        <svg class="icon-eye-off" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
    </button>
</div>
</div><button class="btn btn-primary">Simpan Perubahan</button></form>
<?php require 'includes/footer.php'; ?>

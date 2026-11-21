<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_admin();
$q=trim($_GET['q']??'');
$sql="SELECT u.id,u.nama,u.email,u.created_at,u.last_login_at,(SELECT COUNT(*) FROM login_logs l WHERE l.user_id=u.id) AS jumlah_login FROM users u WHERE u.role='pengguna'";
$params=[]; if($q!==''){ $sql.=" AND (u.nama LIKE ? OR u.email LIKE ?)"; $params=["%$q%","%$q%"];} $sql.=" ORDER BY u.created_at DESC";
$s=$pdo->prepare($sql);$s->execute($params);$users=$s->fetchAll();
$pageTitle='Data Pengguna'; require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">PENGELOLAAN AKUN</p><h1>Data Pengguna</h1><p class="muted">Daftar akun yang terdaftar di aplikasi PRISMA.</p></div></div>
<section class="panel"><form method="get" class="search-form"><input name="q" value="<?= e($q) ?>" placeholder="Cari nama atau email pengguna..."><button class="btn btn-primary" type="submit">Cari</button><?php if($q!==''): ?><a class="btn btn-soft" href="users.php">Reset</a><?php endif; ?></form>
<?php if(!$users): ?><div class="empty-state"><h3>Pengguna belum ditemukan</h3><p>Belum ada akun yang cocok dengan pencarianmu.</p></div><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Nama</th><th>Email</th><th>Tanggal daftar</th><th>Login terakhir</th><th>Total login</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><strong><?= e($u['nama']) ?></strong></td><td><?= e($u['email']) ?></td><td><?= e(date('d M Y',strtotime($u['created_at']))) ?></td><td><?= $u['last_login_at'] ? e(date('d M Y, H:i',strtotime($u['last_login_at']))) : 'Belum pernah' ?></td><td><?= (int)$u['jumlah_login'] ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require 'includes/footer.php'; ?>
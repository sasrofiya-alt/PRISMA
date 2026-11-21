<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_admin();
$q=trim($_GET['q']??''); $sql="SELECT l.id,l.login_at,u.nama,u.email FROM login_logs l JOIN users u ON u.id=l.user_id WHERE 1=1";$params=[];
if($q!==''){ $sql.=" AND (u.nama LIKE ? OR u.email LIKE ?)";$params=["%$q%","%$q%"];} $sql.=" ORDER BY l.login_at DESC LIMIT 300";
$s=$pdo->prepare($sql);$s->execute($params);$logs=$s->fetchAll();$pageTitle='Riwayat Login';require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">AKTIVITAS AKUN</p><h1>Riwayat Login</h1><p class="muted">Catatan waktu setiap pengguna berhasil masuk ke PRISMA.</p></div></div>
<section class="panel"><form method="get" class="search-form"><input name="q" value="<?= e($q) ?>" placeholder="Cari nama atau email..."><button class="btn btn-primary" type="submit">Cari</button><?php if($q!==''): ?><a class="btn btn-soft" href="login_history.php">Reset</a><?php endif; ?></form>
<?php if(!$logs): ?><div class="empty-state"><h3>Belum ada riwayat login</h3><p>Data login berhasil akan tercatat otomatis setelah pengguna masuk.</p></div><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Nama pengguna</th><th>Email</th><th>Tanggal</th><th>Waktu</th><th>Status</th></tr></thead><tbody><?php foreach($logs as $l): ?><tr><td><strong><?= e($l['nama']) ?></strong></td><td><?= e($l['email']) ?></td><td><?= e(date('d M Y',strtotime($l['login_at']))) ?></td><td><?= e(date('H:i:s',strtotime($l['login_at']))) ?></td><td><span class="status status-selesai">Berhasil</span></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require 'includes/footer.php'; ?>
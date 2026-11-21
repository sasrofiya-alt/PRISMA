<?php
require_once 'config.php';require_once 'includes/functions.php';require_user();$uid=current_user_id();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action']??'add';
 if($action==='add'){$title=trim($_POST['judul_todo']??'');if($title!==''){$s=$pdo->prepare('INSERT INTO todos(user_id,judul_todo) VALUES(?,?)');$s->execute([$uid,$title]);flash('To-Do ditambahkan.');}else{flash('Judul pekerjaan wajib diisi.','error');}}
 if($action==='toggle'){$id=(int)($_POST['id']??0);$status=$_POST['status']==='Selesai'?'Selesai':'Belum';$s=$pdo->prepare('UPDATE todos SET status=? WHERE id=? AND user_id=?');$s->execute([$status,$id,$uid]);}
 if($action==='delete'){$s=$pdo->prepare('DELETE FROM todos WHERE id=? AND user_id=?');$s->execute([(int)($_POST['id']??0),$uid]);flash('To-Do dihapus.');}
 header('Location: todos.php');exit;
}
$s=$pdo->prepare('SELECT * FROM todos WHERE user_id=? ORDER BY status ASC,created_at DESC');$s->execute([$uid]);$todos=$s->fetchAll();$pageTitle='To-Do List';require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">LANGKAH KECIL SETIAP HARI</p><h1>To-Do List</h1><p class="muted">Catat pekerjaan kecil dan tandai saat selesai.</p></div></div>
<form class="panel todo-add" method="post"><input type="text" name="judul_todo" required maxlength="150" placeholder="Apa yang ingin kamu kerjakan?"><button class="btn btn-primary">＋ Tambahkan</button></form>
<div class="panel todo-panel"><?php if(!$todos): ?><div class="empty-state"><div class="empty-icon">☑</div><h3>Daftarmu masih kosong</h3><p>Tambahkan pekerjaan kecil yang ingin kamu selesaikan.</p></div><?php else: foreach($todos as $t): ?><div class="todo-row <?= $t['status']==='Selesai'?'todo-done':'' ?>"><form method="post"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $t['id'] ?>"><input type="hidden" name="status" value="<?= $t['status']==='Selesai'?'Belum':'Selesai' ?>"><button class="todo-toggle" title="Ubah status"><?= $t['status']==='Selesai'?'✓':'○' ?></button></form><span><?= e($t['judul_todo']) ?></span><small><?= e($t['status']) ?></small><form method="post" onsubmit="return confirm('Hapus item ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $t['id'] ?>"><button class="icon-button danger-text" title="Hapus">×</button></form></div><?php endforeach; endif; ?></div>
<?php require 'includes/footer.php'; ?>

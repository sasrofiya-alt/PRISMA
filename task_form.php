<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_once 'includes/task_files.php'; require_user();
$uid=current_user_id(); $id=(int)($_GET['id']??0); $task=['judul_tugas'=>'','mata_pelajaran'=>'','deskripsi'=>'','deadline'=>'','prioritas'=>'Sedang','status'=>'Belum'];
if($id){$s=$pdo->prepare('SELECT * FROM tasks WHERE id=? AND user_id=?');$s->execute([$id,$uid]);$found=$s->fetch();if(!$found){flash('Tugas tidak ditemukan.','error');header('Location: tasks.php');exit;}$task=$found;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 foreach($task as $k=>$v){if(isset($_POST[$k]))$task[$k]=trim($_POST[$k]);}
 if($task['judul_tugas']===''||$task['mata_pelajaran']===''||$task['deadline']==='')$error='Judul, mata pelajaran, dan deadline wajib diisi.';
 elseif(!in_array($task['prioritas'],['Rendah','Sedang','Tinggi'],true)||!in_array($task['status'],['Belum','Dikerjakan','Selesai'],true))$error='Pilihan prioritas atau status tidak valid.';
 else{
  try {
   $pdo->beginTransaction();
   if($id){$s=$pdo->prepare('UPDATE tasks SET judul_tugas=?,mata_pelajaran=?,deskripsi=?,deadline=?,prioritas=?,status=? WHERE id=? AND user_id=?');$s->execute([$task['judul_tugas'],$task['mata_pelajaran'],$task['deskripsi'],$task['deadline'],$task['prioritas'],$task['status'],$id,$uid]);$taskId=$id;}
   else{$s=$pdo->prepare('INSERT INTO tasks(user_id,judul_tugas,mata_pelajaran,deskripsi,deadline,prioritas,status) VALUES(?,?,?,?,?,?,?)');$s->execute([$uid,$task['judul_tugas'],$task['mata_pelajaran'],$task['deskripsi'],$task['deadline'],$task['prioritas'],$task['status']]);$taskId=(int)$pdo->lastInsertId();}
   task_upload_files($pdo,$taskId,$uid);
   $pdo->commit(); flash('Tugas dan file berhasil disimpan.');header('Location: tasks.php');exit;
  } catch(Throwable $ex) { if($pdo->inTransaction())$pdo->rollBack(); $error=$ex->getMessage(); }
 }
}
$files=[]; if($id){$s=$pdo->prepare('SELECT * FROM task_files WHERE task_id=? ORDER BY uploaded_at DESC');$s->execute([$id]);$files=$s->fetchAll();}
$pageTitle=$id?'Edit Tugas':'Tambah Tugas';require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">RUANG TUGAS</p><h1><?= e($pageTitle) ?></h1><p class="muted">Buat tugas sekaligus simpan file pendukungnya.</p></div><a class="btn btn-outline" href="tasks.php">← Kembali</a></div>
<form class="panel form-panel" method="post" enctype="multipart/form-data"><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><div class="form-grid"><div class="field full"><label>Judul tugas *</label><input name="judul_tugas" required maxlength="150" value="<?= e($task['judul_tugas']) ?>" placeholder="Contoh: Membuat halaman login"></div><div class="field"><label>Mata pelajaran *</label><input name="mata_pelajaran" required maxlength="100" value="<?= e($task['mata_pelajaran']) ?>" placeholder="Contoh: Pemrograman Web"></div><div class="field"><label>Deadline *</label><input type="date" name="deadline" required value="<?= e($task['deadline']) ?>"></div><div class="field"><label>Prioritas</label><select name="prioritas"><?php foreach(['Rendah','Sedang','Tinggi'] as $v): ?><option <?= $task['prioritas']===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?></select></div><div class="field"><label>Status</label><select name="status"><?php foreach(['Belum','Dikerjakan','Selesai'] as $v): ?><option <?= $task['status']===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?></select></div><div class="field full"><label>Deskripsi tugas</label><textarea name="deskripsi" rows="4" placeholder="Tulis detail atau instruksi tugas..."><?= e($task['deskripsi']) ?></textarea></div>
<div class="field full"><label>File Tugas</label><p class="muted">Unggah LKPD, Word, Excel, PDF, PowerPoint, gambar, atau ZIP. Maksimal 10 MB per file.</p><input type="file" name="task_files[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip"></div>
<?php if($files): ?><div class="field full"><label>File yang sudah tersimpan</label><div class="task-file-list"><?php foreach($files as $f): ?><div class="task-file-row"><span>📄 <?= e($f['nama_asli']) ?></span><a href="download_task_file.php?id=<?= (int)$f['id'] ?>&inline=1" target="_blank">Buka</a><a href="download_task_file.php?id=<?= (int)$f['id'] ?>">Unduh</a><form method="post" action="task_file_delete.php" onsubmit="return confirm('Hapus file ini?')"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="link-danger">Hapus</button></form></div><?php endforeach; ?></div></div><?php endif; ?>
</div><div class="form-actions"><button class="btn btn-primary" type="submit">Simpan Tugas</button><a class="btn btn-outline" href="tasks.php">Batal</a></div></form>
<?php require 'includes/footer.php'; ?>

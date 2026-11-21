<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_once 'includes/task_files.php'; require_user();
$uid=current_user_id();
$tasksStmt=$pdo->prepare('SELECT id,judul_tugas,mata_pelajaran FROM tasks WHERE user_id=? ORDER BY deadline ASC'); $tasksStmt->execute([$uid]); $tasks=$tasksStmt->fetchAll();
$selectedTask=(int)($_GET['task_id'] ?? $_POST['task_id'] ?? 0); $validIds=array_column($tasks,'id');
if ($selectedTask && !in_array($selectedTask,$validIds,false)) { $selectedTask=0; }
$errorMessages=[];
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['upload_files'])) {
    $kind=$_POST['jenis_file'] ?? '';
    if (!$selectedTask) $errorMessages[]='Pilih tugas terlebih dahulu.';
    if (!in_array($kind,['Materi Guru','Hasil Pengerjaan'],true)) $errorMessages[]='Kategori file tidak valid.';
    if (!$errorMessages && isset($_FILES['files'])) $errorMessages=store_task_uploads($pdo,$selectedTask,$kind,$_FILES['files']);
    if (!$errorMessages) { flash('File berhasil diunggah.'); header('Location: files.php?task_id='.$selectedTask); exit; }
}
$sql='SELECT f.*,t.judul_tugas,t.mata_pelajaran FROM task_files f JOIN tasks t ON t.id=f.task_id WHERE t.user_id=?'; $params=[$uid];
if ($selectedTask) {$sql.=' AND f.task_id=?';$params[]=$selectedTask;}
$kindFilter=$_GET['jenis'] ?? ''; if (in_array($kindFilter,['Materi Guru','Hasil Pengerjaan'],true)) {$sql.=' AND f.jenis_file=?';$params[]=$kindFilter;}
$sql.=' ORDER BY f.uploaded_at DESC'; $st=$pdo->prepare($sql);$st->execute($params);$fileRows=$st->fetchAll();
$pageTitle='File Tugas'; require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">PUSAT DOKUMEN</p><h1>File Tugas</h1><p class="muted">Simpan, lihat, dan kelola file materi maupun hasil pengerjaanmu.</p></div></div>
<?php if($errorMessages): ?><div class="alert error"><?php foreach($errorMessages as $m): ?><div><?= e($m) ?></div><?php endforeach; ?></div><?php endif; ?>
<div class="files-layout">
 <section class="panel"><div class="panel-heading"><div><p class="eyebrow">TAMBAHKAN DOKUMEN</p><h2>Upload File</h2></div></div>
 <?php if(!$tasks): ?><div class="empty-state"><h3>Belum ada tugas</h3><p>Buat tugas terlebih dahulu sebelum mengunggah file.</p><a class="btn btn-primary" href="task_form.php">＋ Tambah Tugas</a></div><?php else: ?>
 <form method="post" enctype="multipart/form-data" class="file-upload-form">
  <div class="field"><label>Pilih tugas *</label><select name="task_id" required><option value="">Pilih tugas</option><?php foreach($tasks as $t): ?><option value="<?= (int)$t['id'] ?>" <?= $selectedTask===(int)$t['id']?'selected':'' ?>><?= e($t['judul_tugas'].' — '.$t['mata_pelajaran']) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label>Kategori file *</label><select name="jenis_file"><option>Materi Guru</option><option>Hasil Pengerjaan</option></select></div>
  <div class="field"><label>Pilih file *</label><input type="file" name="files[]" multiple required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip"><small class="field-help">Bisa memilih beberapa file sekaligus. Format PDF, Word, Excel, PowerPoint, JPG, PNG, atau ZIP. Maksimal 10 MB per file.</small></div>
  <button class="btn btn-primary" type="submit" name="upload_files">＋ Upload File</button>
 </form><?php endif; ?></section>
 <section class="panel"><div class="panel-heading"><div><p class="eyebrow">ARSIP DOKUMEN</p><h2>Daftar File</h2></div><span class="category-pill"><?= count($fileRows) ?> file</span></div>
 <form method="get" class="file-filters"><select name="task_id"><option value="0">Semua tugas</option><?php foreach($tasks as $t): ?><option value="<?= (int)$t['id'] ?>" <?= $selectedTask===(int)$t['id']?'selected':'' ?>><?= e($t['judul_tugas']) ?></option><?php endforeach; ?></select><select name="jenis"><option value="">Semua kategori</option><option <?= $kindFilter==='Materi Guru'?'selected':'' ?>>Materi Guru</option><option <?= $kindFilter==='Hasil Pengerjaan'?'selected':'' ?>>Hasil Pengerjaan</option></select><button class="btn btn-dark" type="submit">Filter</button></form>
 <?php if(!$fileRows): ?><div class="empty-state"><div class="empty-icon">▤</div><h3>Belum ada file</h3><p>File yang kamu unggah akan muncul di daftar ini.</p></div><?php else: ?><div class="file-list"><?php foreach($fileRows as $f): $ext=strtolower(pathinfo($f['nama_asli'],PATHINFO_EXTENSION)); $canPreview=in_array($ext,['pdf','jpg','jpeg','png'],true); ?><article class="file-item"><div class="file-type-icon <?= e($ext) ?>"><?= e(strtoupper($ext)) ?></div><div class="file-main"><strong><?= e($f['nama_asli']) ?></strong><small><?= e($f['judul_tugas']) ?> · <?= e($f['jenis_file']) ?></small><small><?= number_format($f['ukuran_file']/1024,0,',','.') ?> KB · <?= e(date('d M Y, H:i',strtotime($f['uploaded_at']))) ?></small></div><div class="file-actions"><?php if($canPreview): ?><a class="file-action" target="_blank" href="view_task_file.php?id=<?= (int)$f['id'] ?>">Buka</a><?php endif; ?><a class="file-action" href="download_task_file.php?id=<?= (int)$f['id'] ?>">Unduh</a><form method="post" action="task_file_delete.php" onsubmit="return confirm('Hapus file ini?')"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><input type="hidden" name="return_task" value="<?= $selectedTask ?>"><button class="file-action danger" type="submit">Hapus</button></form></div></article><?php endforeach; ?></div><?php endif; ?>
 </section>
</div>
<?php require 'includes/footer.php'; ?>

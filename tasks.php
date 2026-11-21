<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_user();
$uid=current_user_id(); $view=($_GET['view']??'tasks')==='files'?'files':'tasks'; $q=trim($_GET['q']??''); $status=$_GET['status']??''; $prio=$_GET['prioritas']??'';

$sql='SELECT t.*, (SELECT COUNT(*) FROM task_files f WHERE f.task_id=t.id) AS jumlah_file, (SELECT COUNT(*) FROM saved_links sl WHERE sl.task_id=t.id) AS jumlah_link FROM tasks t WHERE t.user_id = ?'; $params=[$uid];
if($q!==''){ $sql.=' AND (t.judul_tugas LIKE ? OR t.mata_pelajaran LIKE ?)'; $params[]="%$q%"; $params[]="%$q%"; }
if(in_array($status,['Belum','Dikerjakan','Selesai'],true)){ $sql.=' AND t.status = ?'; $params[]=$status; }
if(in_array($prio,['Rendah','Sedang','Tinggi'],true)){ $sql.=' AND t.prioritas = ?'; $params[]=$prio; }
$sql.=' ORDER BY (t.status = "Selesai") ASC, t.deadline ASC'; $stmt=$pdo->prepare($sql); $stmt->execute($params); $tasks=$stmt->fetchAll();

$fs=$pdo->prepare('SELECT f.*,t.judul_tugas,t.mata_pelajaran FROM task_files f JOIN tasks t ON t.id=f.task_id WHERE t.user_id=? ORDER BY f.uploaded_at DESC');$fs->execute([$uid]);$files=$fs->fetchAll();

// Stats for tasks
$totalTasks = count($tasks);
$totalFiles = count($files);
$totalDone = 0;
foreach($tasks as $t) { if($t['status'] === 'Selesai') $totalDone++; }

$pageTitle='Tugas'; require 'includes/header.php';
$todayTs = strtotime(date('Y-m-d'));
?>

<section class="home-hero" style="margin-bottom: 24px;">
    <div class="home-hero-content">
        <span class="home-kicker">RUANG PRODUKTIVITAS</span>
        <h1>Ruang Tugas</h1>
        <p class="home-intro" style="margin: 8px 0 0;">Semua tugas dan file pentingmu tertata rapi dalam satu tempat.</p>
    </div>
    <div style="position:relative; z-index:2;">
        <a class="btn home-btn-main" href="task_form.php">＋ Tambah Tugas</a>
    </div>
</section>

<div class="stat-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 24px;">
    <article class="stat-card">
        <div class="stat-top"><span class="stat-icon lilac">▣</span><span class="stat-mini-label">TOTAL TUGAS</span></div>
        <p>Total Tugas</p>
        <strong><?= $totalTasks ?></strong>
        <small>Catatan tugas tersimpan</small>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span class="stat-icon mint">▤</span><span class="stat-mini-label">FILE TERSIMPAN</span></div>
        <p>File Tersimpan</p>
        <strong><?= $totalFiles ?></strong>
        <small>Dokumen dan file pendukung</small>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span class="stat-icon amber">✦</span><span class="stat-mini-label">SUDAH SELESAI</span></div>
        <p>Sudah Selesai</p>
        <strong><?= $totalDone ?></strong>
        <small>Tugas berstatus selesai</small>
    </article>
</div>

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
    <div class="task-tabs" style="margin: 0; border-bottom: 0;">
        <a class="<?= $view==='tasks'?'active':'' ?>" href="tasks.php?view=tasks" style="border-radius: 10px;">▣ Daftar Tugas <span><?= count($tasks) ?></span></a>
        <a class="<?= $view==='files'?'active':'' ?>" href="tasks.php?view=files" style="border-radius: 10px;">▤ File Tersimpan <span><?= count($files) ?></span></a>
    </div>
    <span class="muted" style="font-size: 12.5px;">Kelola pekerjaanmu dengan lebih teratur</span>
</div>

<?php if($view==='tasks'): ?>
<form class="filter-bar" method="get" style="background: var(--surface); padding: 18px 20px; border-radius: var(--radius-lg); border: 1px solid var(--line); box-shadow: var(--shadow-sm); margin-bottom: 24px;">
    <input type="hidden" name="view" value="tasks">
    <div style="flex: 1; min-width: 240px;">
        <input name="q" value="<?= e($q) ?>" placeholder="Cari nama tugas atau mata pelajaran..." style="width: 100%;">
    </div>
    <div>
        <select name="status" style="min-width: 170px;">
            <option value="">Semua status</option>
            <?php foreach(['Belum','Dikerjakan','Selesai'] as $s): ?>
                <option <?= $status===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-dark" type="submit" style="background: #182620; padding: 11px 20px;">Terapkan Filter</button>
    <?php if($q!=='' || $status!==''): ?>
        <a class="btn btn-outline" href="tasks.php">Reset</a>
    <?php endif; ?>
</form>

<?php if(!$tasks): ?>
    <div class="panel empty-state">
        <div class="empty-icon">▣</div>
        <h3>Belum ada tugas</h3>
        <p>Tambahkan tugas dan unggah LKPD atau file pendukungnya.</p>
        <a class="btn btn-primary" href="task_form.php">＋ Tambah Tugas</a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;">
        <?php foreach($tasks as $t):
            $dlTs = strtotime($t['deadline']);
            $diffDays = (int)round(($dlTs - $todayTs) / 86400);
            $badgeText = '';
            $badgeClass = 'deadline-safe';
            if ($t['status'] !== 'Selesai') {
                if ($diffDays < 0) { $badgeText = 'Terlewat '.abs($diffDays).' hari'; $badgeClass = 'deadline-overdue'; }
                elseif ($diffDays === 0) { $badgeText = 'Hari ini!'; $badgeClass = 'deadline-soon'; }
                elseif ($diffDays <= 3) { $badgeText = 'H-'.$diffDays; $badgeClass = 'deadline-soon'; }
                else { $badgeText = $diffDays.' hari lagi'; }
            } else {
                $badgeText = date('d M Y', $dlTs);
            }
        ?>
        <article class="panel" style="padding: 22px; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span class="category-pill"><?= e($t['mata_pelajaran']) ?></span>
                    <span style="font-size: 15px; color: var(--muted); cursor: pointer;" title="Opsi">✦</span>
                </div>
                <h3 style="font-size: 17px; margin: 0 0 8px; color: var(--ink);"><?= e($t['judul_tugas']) ?></h3>
                <p class="muted" style="font-size: 12.5px; margin: 0 0 18px; line-height: 1.55;"><?= e($t['deskripsi'] !== '' ? $t['deskripsi'] : 'Belum ada deskripsi untuk tugas ini.') ?></p>
            </div>
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--line); padding-top: 14px; margin-top: 4px; font-size: 12px; color: var(--muted);">
                    <div>📅 <?= e(date('d M Y', $dlTs)) ?> <?php if($t['status'] !== 'Selesai'): ?><span class="deadline-badge <?= $badgeClass ?>"><?= $badgeText ?></span><?php endif; ?></div>
                    <div>▣ <?= (int)$t['jumlah_file'] ?> file</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px; pt: 4px;">
                    <div style="display: flex; gap: 6px; align-items: center;">
                        <span class="status status-<?= strtolower($t['status']) ?>"><?= e($t['status']) ?></span>
                        <span class="priority priority-<?= strtolower($t['prioritas']) ?>"><?= e($t['prioritas']) ?></span>
                    </div>
                    <div class="actions" style="gap: 14px;">
                        <a href="task_form.php?id=<?= (int)$t['id'] ?>" style="font-weight: 700; color: var(--primary);">Detail ↗</a>
                        <form method="post" action="task_delete.php" onsubmit="return confirm('Hapus tugas ini?')">
                            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                            <button class="link-danger" title="Hapus" style="cursor: pointer; background: none; border: 0; font-size: 14px;">🗑</button>
                        </form>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php else: ?>
<div class="panel table-panel">
    <div class="section-heading">
        <div><h2>File Tersimpan</h2><p class="muted">Semua file yang diunggah melalui formulir tugas.</p></div>
        <a class="btn btn-primary" href="task_form.php">＋ Upload File lewat Tugas</a>
    </div>
    <?php if(!$files): ?>
        <div class="empty-state"><div class="empty-icon">▤</div><h3>Belum ada file tersimpan</h3><p>Tambahkan tugas, lalu pilih file yang ingin disimpan.</p></div>
    <?php else: ?>
        <div class="saved-files-list">
            <?php foreach($files as $f): $ext=strtolower(pathinfo($f['nama_asli'],PATHINFO_EXTENSION)); ?>
            <div class="saved-file">
                <div class="file-type-icon"><?= in_array($ext,['jpg','jpeg','png'])?'▧':($ext==='pdf'?'PDF':strtoupper($ext)) ?></div>
                <div class="saved-file-info"><b><?= e($f['nama_asli']) ?></b><small><?= e($f['judul_tugas']) ?> · <?= e($f['mata_pelajaran']) ?> · <?= number_format($f['ukuran_file']/1048576,2) ?> MB</small></div>
                <div class="saved-file-actions">
                    <a href="download_task_file.php?id=<?= (int)$f['id'] ?>&inline=1" target="_blank">Buka</a>
                    <a href="download_task_file.php?id=<?= (int)$f['id'] ?>">Unduh</a>
                    <form method="post" action="task_file_delete.php" onsubmit="return confirm('Hapus file ini?')"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="link-danger">Hapus</button></form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>

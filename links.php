<?php
require_once 'config.php';require_once 'includes/functions.php';require_user();$uid=current_user_id();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=(int)($_POST['id']??0);$title=trim($_POST['judul_link']??'');$url=trim($_POST['url']??'');$cat=trim($_POST['kategori']??'');
 $taskId=($_POST['task_id']??'')!==''?(int)$_POST['task_id']:null;
 if($title===''||!filter_var($url,FILTER_VALIDATE_URL)||$cat===''){flash('Lengkapi judul, kategori, dan URL yang valid.','error');}
 else{if($id){$s=$pdo->prepare('UPDATE saved_links SET judul_link=?,url=?,kategori=?,task_id=? WHERE id=? AND user_id=?');$s->execute([$title,$url,$cat,$taskId,$id,$uid]);}else{$s=$pdo->prepare('INSERT INTO saved_links(user_id,task_id,judul_link,url,kategori) VALUES(?,?,?,?,?)');$s->execute([$uid,$taskId,$title,$url,$cat]);}flash('Link berhasil disimpan.');}
 header('Location: links.php');exit;
}
if(isset($_GET['delete'])){$s=$pdo->prepare('DELETE FROM saved_links WHERE id=? AND user_id=?');$s->execute([(int)$_GET['delete'],$uid]);flash('Link dihapus.');header('Location: links.php');exit;}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM saved_links WHERE id=? AND user_id=?');$s->execute([(int)$_GET['edit'],$uid]);$edit=$s->fetch();}

$q = trim($_GET['q'] ?? '');
$catFilter = trim($_GET['kategori'] ?? '');
$sqlLinks = 'SELECT sl.*, t.judul_tugas FROM saved_links sl LEFT JOIN tasks t ON t.id = sl.task_id WHERE sl.user_id = ?';
$paramsLinks = [$uid];
if ($q !== '') { $sqlLinks .= ' AND (sl.judul_link LIKE ? OR sl.url LIKE ? OR sl.kategori LIKE ?)'; $paramsLinks[] = "%$q%"; $paramsLinks[] = "%$q%"; $paramsLinks[] = "%$q%"; }
if ($catFilter !== '') { $sqlLinks .= ' AND sl.kategori = ?'; $paramsLinks[] = $catFilter; }
$sqlLinks .= ' ORDER BY sl.created_at DESC';
$s = $pdo->prepare($sqlLinks); $s->execute($paramsLinks); $links = $s->fetchAll();

$catsStmt = $pdo->prepare('SELECT DISTINCT kategori FROM saved_links WHERE user_id = ? ORDER BY kategori ASC');
$catsStmt->execute([$uid]);
$categories = $catsStmt->fetchAll(PDO::FETCH_COLUMN);

$s=$pdo->prepare('SELECT id,judul_tugas FROM tasks WHERE user_id=? ORDER BY judul_tugas');$s->execute([$uid]);$taskOptions=$s->fetchAll();
$pageTitle='Simpan Link';require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">SIMPAN DAN TEMUKAN</p><h1>Link Tersimpan</h1><p class="muted">Kumpulkan tautan materi, Google Drive, atau pengumpulan tugas agar tidak tenggelam di chat.</p></div></div>
<div class="content-split">
    <form class="panel compact-form" method="post">
        <h2><?= $edit?'Edit link':'Tambah link baru' ?></h2>
        <input type="hidden" name="id" value="<?= e($edit['id']??'') ?>">
        <label>Judul link *</label>
        <input name="judul_link" required maxlength="150" value="<?= e($edit['judul_link']??'') ?>" placeholder="Contoh: Materi HTML & CSS">
        <label>URL / alamat link *</label>
        <input type="url" name="url" required value="<?= e($edit['url']??'') ?>" placeholder="https://...">
        <label>Kategori / mata pelajaran *</label>
        <input name="kategori" required maxlength="100" value="<?= e($edit['kategori']??'') ?>" placeholder="Contoh: Pemrograman Web">
        <label>Hubungkan dengan tugas (opsional)</label>
        <select name="task_id">
            <option value="">Tidak dihubungkan</option>
            <?php foreach($taskOptions as $t): ?>
                <option value="<?= $t['id'] ?>" <?= (string)($edit['task_id']??'')===(string)$t['id']?'selected':'' ?>><?= e($t['judul_tugas']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-full">Simpan Link</button>
        <?php if($edit): ?><a class="text-link center-link" href="links.php">Batal edit</a><?php endif; ?>
    </form>

    <div>
        <form class="filter-bar" method="get">
            <input name="q" value="<?= e($q) ?>" placeholder="Cari judul, URL, atau kategori link...">
            <select name="kategori">
                <option value="">Semua kategori</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= e($c) ?>" <?= $catFilter===$c?'selected':'' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-dark" type="submit">Cari</button>
            <?php if($q!=='' || $catFilter!==''): ?><a class="btn btn-outline" href="links.php">Reset</a><?php endif; ?>
        </form>

        <div class="link-grid">
            <?php if(!$links): ?>
                <div class="panel empty-state" style="grid-column:1/-1;"><div class="empty-icon">↗</div><h3>Belum ada link ditemukan</h3><p>Tambahkan link penting pertamamu atau ubah kata kunci pencarian.</p></div>
            <?php else: foreach($links as $l): ?>
                <article class="panel link-card">
                    <div class="link-card-top">
                        <span class="quick-icon">↗</span>
                        <span class="category-pill"><?= e($l['kategori']) ?></span>
                    </div>
                    <h3><?= e($l['judul_link']) ?></h3>
                    <p class="url-text" title="<?= e($l['url']) ?>"><?= e($l['url']) ?></p>
                    <?php if(!empty($l['judul_tugas'])): ?>
                        <div class="link-task-tag" title="Terhubung ke tugas: <?= e($l['judul_tugas']) ?>">▣ <?= e($l['judul_tugas']) ?></div>
                    <?php endif; ?>
                    <div class="link-card-actions">
                        <a href="<?= e($l['url']) ?>" target="_blank" rel="noopener noreferrer">Buka ↗</a>
                        <button type="button" class="copy-link-btn" data-url="<?= e($l['url']) ?>">Salin URL</button>
                        <a href="links.php?edit=<?= $l['id'] ?>">Edit</a>
                        <a class="danger-text" href="links.php?delete=<?= $l['id'] ?>" onclick="return confirm('Hapus link ini?')">Hapus</a>
                    </div>
                </article>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>

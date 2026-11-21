<?php
require_once 'config.php';require_once 'includes/functions.php';require_user();$uid=current_user_id();
$month=isset($_GET['month'])?(int)$_GET['month']:(int)date('m');$year=isset($_GET['year'])?(int)$_GET['year']:(int)date('Y');
if($month<1||$month>12){$month=(int)date('m');}
$first=strtotime("$year-$month-01");$days=(int)date('t',$first);$offset=(int)date('N',$first);
$startDate=date('Y-m-01',$first);$endDate=date('Y-m-t',$first);
$s=$pdo->prepare('SELECT * FROM tasks WHERE user_id=? AND deadline BETWEEN ? AND ? ORDER BY deadline');$s->execute([$uid,$startDate,$endDate]);$rows=$s->fetchAll();$byDay=[];foreach($rows as $r){$byDay[(int)date('j',strtotime($r['deadline']))][]=$r;}
$prev=strtotime('-1 month',$first);$next=strtotime('+1 month',$first);$pageTitle='Kalender';require 'includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">PANTAU JADWAL</p><h1>Kalender Deadline</h1><p class="muted">Lihat jadwal pengumpulan tugas berdasarkan tanggal.</p></div><a class="btn btn-primary" href="task_form.php">＋ Tambah Tugas</a></div>
<div class="panel calendar-panel"><div class="calendar-head"><a class="btn btn-outline" href="?month=<?= date('n',$prev) ?>&year=<?= date('Y',$prev) ?>">←</a><h2><?= e(date('F Y',$first)) ?></h2><a class="btn btn-outline" href="?month=<?= date('n',$next) ?>&year=<?= date('Y',$next) ?>">→</a></div><div class="calendar-grid"><?php foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d): ?><div class="calendar-day-name"><?= $d ?></div><?php endforeach; ?><?php for($i=1;$i<$offset;$i++): ?><div class="calendar-cell muted-cell"></div><?php endfor; ?><?php for($d=1;$d<=$days;$d++): ?><div class="calendar-cell"><b><?= $d ?></b><?php if(!empty($byDay[$d])): foreach($byDay[$d] as $t): ?><a class="calendar-event" title="<?= e($t['judul_tugas']) ?>" href="task_form.php?id=<?= $t['id'] ?>"><?= e($t['judul_tugas']) ?></a><?php endforeach; endif; ?></div><?php endfor; ?></div></div>
<?php require 'includes/footer.php'; ?>

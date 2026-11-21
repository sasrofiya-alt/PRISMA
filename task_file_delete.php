<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_user();
if($_SERVER['REQUEST_METHOD']==='POST'){$s=$pdo->prepare('SELECT f.* FROM task_files f JOIN tasks t ON t.id=f.task_id WHERE f.id=? AND t.user_id=?');$s->execute([(int)($_POST['id']??0),current_user_id()]);$f=$s->fetch();if($f){$d=$pdo->prepare('DELETE FROM task_files WHERE id=?');$d->execute([$f['id']]);$p=__DIR__.'/'.$f['path_file'];if(is_file($p))@unlink($p);flash('File berhasil dihapus.');}else flash('File tidak ditemukan.','error');}
header('Location: tasks.php?view=files');exit;

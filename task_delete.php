<?php
require_once 'config.php';require_once 'includes/functions.php';require_user();
if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)($_POST['id']??0);$s=$pdo->prepare('SELECT f.path_file FROM task_files f JOIN tasks t ON t.id=f.task_id WHERE t.id=? AND t.user_id=?');$s->execute([$id,current_user_id()]);foreach($s->fetchAll() as $f){$p=__DIR__.'/'.$f['path_file'];if(is_file($p))@unlink($p);}$d=$pdo->prepare('DELETE FROM tasks WHERE id=? AND user_id=?');$d->execute([$id,current_user_id()]);flash('Tugas dan file terkait berhasil dihapus.');}
header('Location: tasks.php');exit;

<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_user();
$id=(int)($_GET['id']??0);$s=$pdo->prepare('SELECT f.* FROM task_files f JOIN tasks t ON t.id=f.task_id WHERE f.id=? AND t.user_id=?');$s->execute([$id,current_user_id()]);$f=$s->fetch();if(!$f){http_response_code(404);exit('File tidak ditemukan.');}
$path=__DIR__.'/'.$f['path_file'];if(!is_file($path)){http_response_code(404);exit('Berkas tidak tersedia.');}
$inline=isset($_GET['inline']) && in_array($f['mime_type'],['application/pdf','image/jpeg','image/png'],true);header('Content-Type: '.$f['mime_type']);header('Content-Length: '.filesize($path));header('X-Content-Type-Options: nosniff');header('Content-Disposition: '.($inline?'inline':'attachment').'; filename="'.str_replace(['"',"\r","\n"],'_',basename($f['nama_asli'])).'"');readfile($path);exit;

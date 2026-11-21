<?php
require_once 'config.php'; require_once 'includes/functions.php'; require_login();
$id=(int)($_GET['id']??0);$s=$pdo->prepare('SELECT f.* FROM task_files f JOIN tasks t ON t.id=f.task_id WHERE f.id=? AND t.user_id=?');$s->execute([$id,current_user_id()]);$f=$s->fetch();
if(!$f){http_response_code(404);exit('File tidak ditemukan.');}
$ext=strtolower(pathinfo($f['nama_asli'],PATHINFO_EXTENSION));$types=['pdf'=>'application/pdf','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png'];if(!isset($types[$ext])){http_response_code(415);exit('Pratinjau browser hanya tersedia untuk PDF dan gambar. Silakan unduh file ini.');}
$path=__DIR__.'/uploads/tugas/'.$f['path_file'];if(!is_file($path)){http_response_code(404);exit('File tidak tersedia di server.');}
$name=str_replace(["\r","\n",'"'],'_',basename($f['nama_asli']));header('X-Content-Type-Options: nosniff');header('Content-Type: '.$types[$ext]);header('Content-Disposition: inline; filename="'.$name.'"');header('Content-Length: '.filesize($path));readfile($path);exit;

<?php
function task_upload_files(PDO $pdo, int $taskId, int $userId, string $field='task_files'): array {
    if (empty($_FILES[$field]) || !is_array($_FILES[$field]['name'])) return [];
    $allowed = ['pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','zip'];
    $dir = __DIR__ . '/../uploads/tugas';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) throw new RuntimeException('Folder upload tidak dapat dibuat.');
    $saved=[];
    foreach ($_FILES[$field]['name'] as $i=>$original) {
        $err=$_FILES[$field]['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        if ($err===UPLOAD_ERR_NO_FILE) continue;
        if ($err!==UPLOAD_ERR_OK) throw new RuntimeException('Ada file yang gagal diunggah. Periksa ukuran file.');
        $size=(int)$_FILES[$field]['size'][$i];
        if ($size>10*1024*1024) throw new RuntimeException('Ukuran setiap file maksimal 10 MB.');
        $ext=strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext,$allowed,true)) throw new RuntimeException('Format file tidak didukung: '.htmlspecialchars($original,ENT_QUOTES,'UTF-8'));
        $tmp=$_FILES[$field]['tmp_name'][$i];
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp) ?: 'application/octet-stream';
        $stored=bin2hex(random_bytes(16)).'.'.$ext;
        if (!move_uploaded_file($tmp,$dir.'/'.$stored)) throw new RuntimeException('Gagal menyimpan file.');
        $rel='uploads/tugas/'.$stored;
        try {
            $st=$pdo->prepare('INSERT INTO task_files(task_id,nama_file,nama_asli,path_file,mime_type,ukuran_file) VALUES(?,?,?,?,?,?)');
            $st->execute([$taskId,basename($original),basename($original),$rel,$mime,$size]);
            $saved[]=$rel;
        } catch(Throwable $e) { @unlink($dir.'/'.$stored); throw $e; }
    }
    return $saved;
}

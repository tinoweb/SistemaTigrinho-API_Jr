<?php
header('Content-Type: application/json');
session_start();
if (empty($_SESSION['admin_root_auth']) || $_SESSION['admin_root_auth'] !== '1') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'unauthorized']);
  exit;
}

$maxSize = 8 * 1024 * 1024; // 8MB
// aceitar qualquer imagem reconhecida; fallback pela extensão original

if (!isset($_FILES['file'])) {
  echo json_encode(['ok' => false, 'error' => 'no_file']);
  exit;
}
if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
  $map = [
    UPLOAD_ERR_INI_SIZE => 'ini_size',
    UPLOAD_ERR_FORM_SIZE => 'form_size',
    UPLOAD_ERR_PARTIAL => 'partial',
    UPLOAD_ERR_NO_FILE => 'no_file',
    UPLOAD_ERR_NO_TMP_DIR => 'no_tmp_dir',
    UPLOAD_ERR_CANT_WRITE => 'cant_write',
    UPLOAD_ERR_EXTENSION => 'extension_blocked',
  ];
  $err = isset($map[$_FILES['file']['error']]) ? $map[$_FILES['file']['error']] : 'unknown';
  echo json_encode(['ok' => false, 'error' => $err]);
  exit;
}

$file = $_FILES['file'];
if ($file['size'] > $maxSize) { echo json_encode(['ok' => false, 'error' => 'too_large']); exit; }
// extrair extensão original como fallback (não depender de finfo)
$ext = 'bin';
if (preg_match('/\.(png|jpg|jpeg|gif|webp)$/i', $file['name'], $m)) {
  $ext = strtolower($m[1] === 'jpeg' ? 'jpg' : $m[1]);
} elseif (!empty($_FILES['file']['type']) && preg_match('/image\/(png|jpe?g|gif|webp)/i', (string)$_FILES['file']['type'], $m2)) {
  $map = ['jpeg' => 'jpg', 'jpg' => 'jpg'];
  $ext = strtolower($map[$m2[1]] ?? $m2[1]);
}
if ($ext === 'bin') { $ext = 'jpg'; }
$code = isset($_POST['game_id']) ? trim((string)$_POST['game_id']) : '';
// permitir a-zA-Z0-9_- para compor o código no nome do arquivo
$safeCode = $code !== '' ? preg_replace('/[^a-zA-Z0-9_-]/', '', $code) : '';
$base = bin2hex(random_bytes(8));
$filename = ($safeCode ? ('game_'.$safeCode.'_') : 'game_') . $base . '.' . $ext;

$uploadDir = __DIR__ . '/../../uploads/games';
if (!is_dir($uploadDir)) {
  if (!mkdir($uploadDir, 0775, true)) {
    echo json_encode(['ok' => false, 'error' => 'mkdir_failed']);
    exit;
  }
}
if (!is_writable($uploadDir)) {
  echo json_encode(['ok' => false, 'error' => 'not_writable']);
  exit;
}
$target = $uploadDir . '/' . $filename;

if (!is_uploaded_file($file['tmp_name'])) {
  echo json_encode(['ok' => false, 'error' => 'not_uploaded']);
  exit;
}
if (!move_uploaded_file($file['tmp_name'], $target)) {
  echo json_encode(['ok' => false, 'error' => 'move_failed']);
  exit;
}

@chmod($target, 0644);

$url = '/uploads/games/' . $filename;
echo json_encode(['ok' => true, 'url' => $url]);
?>
<?php
header('Content-Type: application/json');
session_start();
if (empty($_SESSION['admin_root_auth']) || $_SESSION['admin_root_auth'] !== '1') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'unauthorized']);
  exit;
}

require __DIR__ . '/../../includes/connect.php';
require __DIR__ . '/../../includes/data.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) { echo json_encode(['ok' => false, 'error' => 'invalid_id']); exit; }

// deletar usuários vinculados ao agente
mysqli_query($link, "DELETE FROM users WHERE agentid = ".$id);
// deletar o agente
$ok = mysqli_query($link, "DELETE FROM agents WHERE id = ".$id);

if ($ok) echo json_encode(['ok' => true]);
else echo json_encode(['ok' => false, 'error' => 'persist_failed']);
?>
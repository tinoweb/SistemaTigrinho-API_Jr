<?php
header('Content-Type: application/json');
error_reporting(0);
session_start();
if (
  (empty($_SESSION['admin_root_auth']) || $_SESSION['admin_root_auth'] !== '1')
  && (!isset($_COOKIE['auth']) || $_COOKIE['auth'] !== 'admin_in')
) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'unauthorized']);
  exit;
}

$jsonPath = __DIR__ . '/../../includes/games.json';
$games = [];
if (file_exists($jsonPath)) {
  $content = file_get_contents($jsonPath);
  $games = json_decode($content, true);
  if (!is_array($games)) $games = [];
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

function save_games($path, $data) {
  $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  return file_put_contents($path, $json) !== false;
}

if ($action === 'add') {
  $name = trim($input['name'] ?? '');
  $id = isset($input['id']) ? trim((string)$input['id']) : '';
$image = isset($input['image']) ? trim($input['image']) : null;
  $vendor = isset($input['vendor']) ? strtoupper(trim($input['vendor'])) : null;
  $status = isset($input['status']) ? strtolower(trim($input['status'])) : null; // 'ativo' | 'manutencao' | 'inativo'
  if ($status !== null && !in_array($status, ['ativo','manutencao','inativo'])) { $status = null; }
  if ($vendor !== null && !in_array($vendor, ['PG','PP'])) { $vendor = null; }
  if ($name === '' || $id === '') {
    echo json_encode(['ok' => false, 'error' => 'invalid_params']);
    exit;
  }
  // evitar duplicados por id
  foreach ($games as $g) {
    if ((string)($g['id'] ?? '') === $id) {
      echo json_encode(['ok' => false, 'error' => 'duplicate_id']);
      exit;
    }
  }
  $gameItem = ['name' => $name, 'id' => $id, 'image' => ($image ? $image : null), 'vendor' => $vendor];
  if ($status !== null) { $gameItem['status'] = $status; }
  $games[] = $gameItem;
  if (save_games($jsonPath, $games)) echo json_encode(['ok' => true]);
  else echo json_encode(['ok' => false, 'error' => 'persist_failed']);
  exit;
}

if ($action === 'update') {
  $targetId = isset($input['targetId']) ? trim((string)$input['targetId']) : '';
  $name = isset($input['name']) ? trim($input['name']) : null;
  $newId = isset($input['id']) ? trim((string)$input['id']) : null;
  $image = isset($input['image']) ? trim($input['image']) : null;
  $vendor = isset($input['vendor']) ? strtoupper(trim($input['vendor'])) : null;
  $status = isset($input['status']) ? strtolower(trim($input['status'])) : null; // 'ativo' | 'manutencao' | 'inativo'
  if ($status !== null && !in_array($status, ['ativo','manutencao','inativo',''])) { $status = null; }
  if ($vendor !== null && !in_array($vendor, ['PG','PP',''])) { $vendor = null; }
  $found = false;
  foreach ($games as &$g) {
    if ((string)($g['id'] ?? '') === $targetId) {
      if ($name !== null) $g['name'] = $name;
      if ($newId !== null && $newId !== '') $g['id'] = $newId;
      if ($image !== null) $g['image'] = ($image !== '' ? $image : null);
      if ($vendor !== null) {
        $g['vendor'] = ($vendor !== '' ? $vendor : null);
      }
      if ($status !== null) {
        $g['status'] = ($status !== '' ? $status : null);
      }
      $found = true;
      break;
    }
  }
  if (!$found) { echo json_encode(['ok' => false, 'error' => 'not_found']); exit; }
  if (save_games($jsonPath, $games)) echo json_encode(['ok' => true]);
  else echo json_encode(['ok' => false, 'error' => 'persist_failed']);
  exit;
}

if ($action === 'delete') {
  $targetId = isset($input['targetId']) ? trim((string)$input['targetId']) : '';
  $before = count($games);
  $games = array_values(array_filter($games, function($g) use ($targetId) {
    return (string)($g['id'] ?? '') !== $targetId;
  }));
  if ($before === count($games)) { echo json_encode(['ok' => false, 'error' => 'not_found']); exit; }
  if (save_games($jsonPath, $games)) echo json_encode(['ok' => true]);
  else echo json_encode(['ok' => false, 'error' => 'persist_failed']);
  exit;
}

// Atualização em massa de status por vendor
if ($action === 'bulk_set_status') {
  $pgStatus = isset($input['pg_status']) ? strtolower(trim($input['pg_status'])) : null;
  $ppStatus = isset($input['pp_status']) ? strtolower(trim($input['pp_status'])) : null;
  $valid = ['ativo','manutencao','inativo'];
  if ($pgStatus !== null && !in_array($pgStatus, $valid)) $pgStatus = null;
  if ($ppStatus !== null && !in_array($ppStatus, $valid)) $ppStatus = null;
  $changed = false;
  foreach ($games as &$g) {
    $v = isset($g['vendor']) ? strtoupper(trim($g['vendor'])) : '';
    if ($v === 'PG' && $pgStatus !== null) { $g['status'] = $pgStatus; $changed = true; }
    if ($v === 'PP' && $ppStatus !== null) { $g['status'] = $ppStatus; $changed = true; }
  }
  if (!$changed) { echo json_encode(['ok' => false, 'error' => 'no_changes']); exit; }
  if (save_games($jsonPath, $games)) echo json_encode(['ok' => true]);
  else echo json_encode(['ok' => false, 'error' => 'persist_failed']);
  exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown_action']);
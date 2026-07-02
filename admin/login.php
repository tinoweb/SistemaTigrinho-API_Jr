<?php
error_reporting(0);
session_start();

// Se já autenticado como admin raiz, vai para o index
if (!empty($_SESSION['admin_root_auth']) && $_SESSION['admin_root_auth'] === '1') {
    header('Location: /admin/index.php');
    exit;
}

include_once __DIR__ . '/../includes/connect.php';
include_once __DIR__ . '/../includes/data.php';

// Processa login simples de admin raiz (sem vínculo a agente)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = isset($_POST['user']) ? trim($_POST['user']) : '';
    $pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';

    // Credenciais do banco ou padrão
    $dbUser = getSetting('admin_user');
    $dbPass = getSetting('admin_pass');

    // Se não existir no banco, usa o padrão admin/admin
    if (empty($dbUser)) $dbUser = 'admin';
    if (empty($dbPass)) $dbPass = 'admin';

    if ($user === $dbUser && $pass === $dbPass) {
        $_SESSION['admin_root_auth'] = '1';
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Credenciais inválidas';
    }
}

$siteLogo = getSetting('site_logo');
if (empty($siteLogo)) $siteLogo = 'https://i.ibb.co/7tMYpYRg/GYPSY.png';

$siteFavicon = getSetting('site_favicon');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin Raiz</title>
  <?php if (!empty($siteFavicon)): ?>
      <link rel="icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
      <link rel="shortcut icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
  <?php endif; ?>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background:#f8f9fa; display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .card { background:#fff; border:1px solid #e9ecef; border-radius:12px; padding:24px; box-shadow:0 8px 24px rgba(0,0,0,0.08); width:100%; max-width:400px; }
    .title { font-weight:700; margin-bottom:16px; text-align: center; }
    .input { width:100%; padding:10px; border:2px solid #e9ecef; border-radius:8px; margin-bottom:12px; }
    .btn { width:100%; padding:10px; background:#212529; color:#fff; border:none; border-radius:8px; cursor:pointer; }
    .error { color:#dc3545; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="card">
    <div style="text-align:center; margin-bottom: 20px;">
        <img src="<?php echo $siteLogo; ?>" alt="Logo" style="max-width: 100px; height: auto;">
    </div>
    <div class="title">Login Admin Raiz</div>
    <?php if (!empty($error)) echo '<div class="error">'.htmlspecialchars($error).'</div>'; ?>
    <form method="post" action="">
      <input class="input" type="text" name="user" placeholder="Usuário" required />
      <input class="input" type="password" name="pass" placeholder="Senha" required />
      <button class="btn" type="submit">Entrar</button>
    </form>
  </div>
</body>
</html>
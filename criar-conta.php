<?php
error_reporting(0);
include_once 'includes/connect.php';
include_once 'includes/data.php';

$siteLogo = getSetting('site_logo');
if (empty($siteLogo)) {
    $siteLogo = 'https://i.ibb.co/7tMYpYRg/GYPSY.png';
}

$siteFavicon = getSetting('site_favicon');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Criar Conta - Stell Games</title>

    <?php if (!empty($siteFavicon)): ?>
        <link rel="icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
    <?php endif; ?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">


<body>
<div class="login-container">
    <div class="logo-container">
        <img src="<?php echo $siteLogo; ?>" class="logo">
        <div class="brand-title">Criar Conta</div>
        <div class="brand-subtitle">Registre seu acesso</div>
    </div>

    <form action="processa-cadastro.php" method="POST">

        <div class="form-group">
            <label class="form-label">Agent Code</label>
            <div class="input-wrapper">
                <input type="text" name="agentCode" class="form-input" placeholder="Seu Agent Code" required>
                <i class="fa-solid fa-user input-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Senha</label>
            <div class="input-wrapper">
                <input type="password" name="senha" class="form-input" placeholder="Crie uma senha" required>
                <i class="fa-solid fa-lock input-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Callback URL</label>
            <div class="input-wrapper">
                <input type="text" name="callbackurl" class="form-input" placeholder="https://seusite.com/callback">
                <i class="fa-solid fa-link input-icon"></i>
            </div>
        </div>

        <button type="submit" class="login-button">
            <span class="button-text">CADASTRAR</span>
        </button>

        <a href="index.php" class="register-button">JÁ TENHO CONTA</a>

    </form>

    <div class="footer-text">
        &copy; <?php echo date('Y'); ?> Stell Games. All rights reserved.
    </div>
</div>
</body>
</html>

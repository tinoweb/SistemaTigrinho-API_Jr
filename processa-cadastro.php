<?php
include_once 'includes/connect.php'; 
// aqui já existe $link conectado

// Dados do formulário
$agentCode   = trim($_POST['agentCode']);
$senha       = $_POST['senha'];
$callbackurl = trim($_POST['callbackurl']);

// Proteção básica
if(empty($agentCode) || empty($senha)){
    die("Preencha todos os campos.");
}

// ===== Configurações padrão ===== //
$saldo = 100000;

$agentToken = bin2hex(random_bytes(16));
$secretKey  = bin2hex(random_bytes(16));

$probganho             = 50;
$probbonus             = 5;
$probganhortp          = 50;
$probganhoinfluencer   = 50;
$probbonusinfluencer   = 5;
$probganhoaposta       = 50;
$probganhosaldo        = 50;

$status = 1;

// Senha segura
$senhaHash = $senha;

// ===== Verifica se agentCode já existe ===== //
$check = mysqli_prepare($link, "SELECT id FROM agents WHERE agentCode = ?");
mysqli_stmt_bind_param($check, "s", $agentCode);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check) > 0){
    die("❌ AgentCode já existe. Escolha outro.");
}
mysqli_stmt_close($check);

// ===== INSERÇÃO ===== //
$sql = "INSERT INTO agents 
(agentCode, saldo, agentToken, secretKey, probganho, probbonus, probganhortp, probganhoinfluencer, probbonusinfluencer, probganhoaposta, probganhosaldo, callbackurl, senha, status)
VALUES 
(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = mysqli_prepare($link, $sql);

mysqli_stmt_bind_param($stmt, "sissiiiiiiissi", 
    $agentCode,
    $saldo,
    $agentToken,
    $secretKey,
    $probganho,
    $probbonus,
    $probganhortp,
    $probganhoinfluencer,
    $probbonusinfluencer,
    $probganhoaposta,
    $probganhosaldo,
    $callbackurl,
    $senhaHash,
    $status
);

if(mysqli_stmt_execute($stmt)){

    // ================================================================= //
    // >>> INTEGRAÇÃO COM BANCO DE DADOS APIPP (PAINEL ANTIGO) <<< //
    // ================================================================= //
    
    // Conecta ao banco apipp
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_user = getenv('DB_USERNAME_PP') ?: 'apipp';
    $db_pass = getenv('DB_PASSWORD_PP') ?: '13211321';
    $db_name = getenv('DB_NAME_PP') ?: 'apipp';
    $link_pp = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    
    if($link_pp){
        // Verifica se já existe para evitar erro
        $agentCodeEscaped = mysqli_real_escape_string($link_pp, $agentCode);
        $check_pp = mysqli_query($link_pp, "SELECT id FROM agents WHERE agentCode = '$agentCodeEscaped' LIMIT 1");
        
        if(mysqli_num_rows($check_pp) == 0){
            // Prepara dados para o formato do painel
            $email_pp = $agentCode . "@stellgames.com"; // Email fictício para compatibilidade
            $password_pp = password_hash($senha, PASSWORD_BCRYPT); // Hash BCRYPT exigido pelo painel
            
            // Sincronização de credenciais (PG = PP)
            // O cliente pediu para que o Token e SecretKey sejam iguais nos dois sistemas
            $token_pp = $agentToken; 
            $uuid_pp  = $secretKey;
            
            $agentName_pp = $agentCode;
            $agentType_pp = 2;
            $percent_pp = 20;
            $balance_pp = 50; // Saldo inicial padrão do painel
            $depth_pp = 1;
            $parentId_pp = 1;
            $currency_pp = 'BRL';
            $lang_pp = 'pt';
            $rtp_pp = '96';
            $memo_pp = 'Cadastrou pelo site principal (integração)';
            $createdAt_pp = date('Y-m-d H:i:s');
            $updatedAt_pp = date('Y-m-d H:i:s');
            
            $sql_pp = "INSERT INTO agents 
            (email, agentCode, password, token, secretKey, agentName, agentType, percent, balance, depth, parentId, currency, lang, rtp, memo, createdAt, updatedAt) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_pp = mysqli_prepare($link_pp, $sql_pp);
            if($stmt_pp){
                mysqli_stmt_bind_param($stmt_pp, "ssssssiidisssssss", 
                   $email_pp,
                   $agentCode,
                   $password_pp,
                   $token_pp,
                   $uuid_pp,
                   $agentName_pp,
                   $agentType_pp,
                   $percent_pp,
                   $balance_pp,
                   $depth_pp,
                   $parentId_pp,
                   $currency_pp,
                   $lang_pp,
                   $rtp_pp,
                   $memo_pp,
                   $createdAt_pp,
                   $updatedAt_pp
                );
                mysqli_stmt_execute($stmt_pp);
                mysqli_stmt_close($stmt_pp);
            }
        }
        mysqli_close($link_pp);
    }
    // ================================================================= //

    // Carrega configurações visuais
    include_once 'includes/data.php';
    $siteLogo = getSetting('site_logo');
    if(empty($siteLogo)){
        $siteLogo = 'https://i.ibb.co/7tMYpYRg/GYPSY.png';
    }

    $siteFavicon = getSetting('site_favicon');
    ?>
    
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <title>Conta Criada - Stell Games</title>

        <?php if (!empty($siteFavicon)): ?>
            <link rel="icon" href="<?php echo $siteFavicon; ?>">
        <?php endif; ?>

        <!-- CSS principal -->
        <link rel="stylesheet" href="assets/css/style.css">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body>
        <div class="login-container">
            
            <div class="logo-container">
                <img src="<?php echo $siteLogo; ?>" class="logo">
                <div class="brand-title">Conta Criada!</div>
                <div class="brand-subtitle">Seu acesso foi registrado com sucesso</div>
            </div>

            <div class="form-group" style="text-align:center;">
                <i class="fa-solid fa-circle-check" style="font-size:60px;color:#00e676;text-shadow:0 0 20px #00e676;"></i>
            </div>

            <div style="text-align:center;color:#fff;font-size:1.1rem;line-height:1.6;margin-bottom:20px;">
                <b>Agent Token</b><br>
                <span style="color:#00e5ff;"><?php echo $agentToken; ?></span>
                <br><br>
                <b>Secret Key</b><br>
                <span style="color:#00e5ff;"><?php echo $secretKey; ?></span>
            </div>

            <a href="index.php" class="login-button" style="text-decoration:none;display:block;text-align:center;">
                IR PARA LOGIN
            </a>

            <div class="footer-text">
                &copy; <?php echo date('Y'); ?> Stell Games. All rights reserved.
            </div>

        </div>
    </body>
    </html>

    <?php

} else {
    echo "❌ Erro ao cadastrar: " . mysqli_error($link);
}


mysqli_stmt_close($stmt);
?>

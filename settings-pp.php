<?php
include 'includes/connect.php';
include 'includes/connect_pp.php';
include 'includes/header.php';

// Verifica login
if (!isset($_COOKIE['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg = '';

// Busca dados atuais do agente logado
if($myAgentIdPP > 0){
    $q = mysqli_query($link_pp, "SELECT * FROM agents WHERE id = $myAgentIdPP LIMIT 1");
    if($q && mysqli_num_rows($q) > 0){
        $agentData = mysqli_fetch_assoc($q);
    } else {
        die("Erro ao carregar dados do agente.");
    }
} else {
    die("Agente não identificado.");
}

// Processa formulário
if(isset($_POST['btn_save'])){
    $callbackurl = mysqli_real_escape_string($link_pp, $_POST['callbackurl']);
    
    // RTP geralmente é sensível, verifique se o agente pode alterar. 
    // O usuário pediu para "configurar os rtp", então vou permitir.
    $rtp = intval($_POST['rtp']);
    
    // Validação básica
    if($rtp < 0) $rtp = 0;
    if($rtp > 100) $rtp = 100;

    $sqlUpd = "UPDATE agents SET 
        siteEndPoint = '$callbackurl',
        rtp = $rtp,
        updatedAt = NOW()
        WHERE id = $myAgentIdPP";
        
    if(mysqli_query($link_pp, $sqlUpd)){
        $msg = '<div class="alert success"><i class="fa-solid fa-check-circle"></i> Configurações atualizadas com sucesso!</div>';
        // Recarrega dados
        $q = mysqli_query($link_pp, "SELECT * FROM agents WHERE id = $myAgentIdPP LIMIT 1");
        $agentData = mysqli_fetch_assoc($q);
    } else {
        $msg = '<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> Erro ao atualizar: '.mysqli_error($link_pp).'</div>';
    }
}

// Gerar Token/Secret se não existirem (Opcional, mas útil)
if(empty($agentData['token']) || empty($agentData['secretKey'])){
    // Se estiver vazio, talvez queira gerar. Por enquanto vou apenas mostrar o que tem.
}

?>

<style>
    :root {
        --primary-color: #00e5ff;
        --bg-dark: #0a0a0a;
        --card-bg: #141414;
        --text-color: #ffffff;
        --border-color: #333;
    }

    .settings-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .settings-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        margin-bottom: 30px;
    }

    .settings-header {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
        gap: 15px;
    }

    .settings-title {
        font-size: 1.5rem;
        color: var(--primary-color);
        font-weight: 600;
        margin: 0;
    }

    .info-group {
        background: rgba(255,255,255,0.05);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px dashed #444;
    }

    .info-label {
        font-size: 0.85rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }

    .info-value-box {
        display: flex;
        gap: 10px;
    }

    .info-value {
        flex: 1;
        background: #000;
        border: 1px solid #333;
        color: #00ff88;
        padding: 10px 15px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }

    .btn-copy {
        background: #333;
        color: #fff;
        border: 1px solid #444;
        padding: 0 15px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-copy:hover {
        background: #444;
        border-color: #555;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: #ccc;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        background: #000;
        border: 1px solid var(--border-color);
        color: #fff;
        padding: 12px;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    .btn-save {
        background: var(--primary-color);
        color: #000;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        font-size: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
        display: block;
        width: 100%;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.4);
    }

    .alert {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert.success { background: rgba(0, 230, 118, 0.1); color: #00e676; border: 1px solid #00e676; }
    .alert.error { background: rgba(255, 23, 68, 0.1); color: #ff1744; border: 1px solid #ff1744; }

    .api-docs {
        color: #aaa;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .api-docs code {
        background: #222;
        padding: 2px 6px;
        border-radius: 4px;
        color: #e0e0e0;
    }
</style>

<div class="settings-container">
    <a href="home-pp.php" style="color: #888; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Voltar para Dashboard
    </a>

    <?php echo $msg; ?>

    <!-- Credenciais de API -->
    <div class="settings-card">
        <div class="settings-header">
            <i class="fa-solid fa-key" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 class="settings-title">Credenciais de API</h2>
        </div>
        
        <p style="color:#888; margin-bottom:20px;">Use estas credenciais para integrar seu site com nossa API de jogos.</p>

        <div class="info-group">
            <label class="info-label">Agent Code</label>
            <div class="info-value-box">
                <input type="text" class="info-value" value="<?php echo htmlspecialchars($agentData['agentCode']); ?>" readonly id="agentCode">
                <button class="btn-copy" onclick="copyToClipboard('agentCode')"><i class="fa-regular fa-copy"></i></button>
            </div>
        </div>

        <div class="info-group">
            <label class="info-label">API Token</label>
            <div class="info-value-box">
                <input type="text" class="info-value" value="<?php echo htmlspecialchars($agentData['token']); ?>" readonly id="apiToken">
                <button class="btn-copy" onclick="copyToClipboard('apiToken')"><i class="fa-regular fa-copy"></i></button>
            </div>
        </div>

        <div class="info-group">
            <label class="info-label">Secret Key</label>
            <div class="info-value-box">
                <input type="text" class="info-value" value="<?php echo htmlspecialchars($agentData['secretKey']); ?>" readonly id="secretKey">
                <button class="btn-copy" onclick="copyToClipboard('secretKey')"><i class="fa-regular fa-copy"></i></button>
            </div>
        </div>
    </div>

    <!-- Configurações -->
    <div class="settings-card">
        <div class="settings-header">
            <i class="fa-solid fa-sliders" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 class="settings-title">Configurações de Integração</h2>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Site Endpoint (URL de Callback)</label>
                <p style="font-size:0.85rem; color:#666; margin-bottom:10px;">
                    Insira a URL do seu site que receberá as notificações de saldo e transações (Webhook).
                </p>
                <input type="url" name="callbackurl" class="form-control" 
                       value="<?php echo htmlspecialchars($agentData['siteEndPoint']); ?>" 
                       placeholder="https://seusite.com/api/callback">
            </div>

            <div class="form-group">
                <label>RTP Geral (%)</label>
                <p style="font-size:0.85rem; color:#666; margin-bottom:10px;">
                    Defina a porcentagem de Retorno ao Jogador para sua integração.
                </p>
                <input type="number" name="rtp" class="form-control" 
                       value="<?php echo intval($agentData['rtp']); ?>" 
                       min="0" max="100">
            </div>

            <button type="submit" name="btn_save" class="btn-save">SALVAR CONFIGURAÇÕES</button>
        </form>
    </div>
    
    <!-- Documentação Rápida -->
    <div class="settings-card">
        <div class="settings-header">
            <i class="fa-solid fa-book" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 class="settings-title">Como Conectar</h2>
        </div>
        <div class="api-docs">
            <p>Para conectar seu site, utilize as credenciais acima. O fluxo básico é:</p>
            <ol style="margin-left: 20px; margin-top: 10px;">
                <li style="margin-bottom:10px;">Configure o <strong>Site Endpoint</strong> acima com a URL do seu backend.</li>
                <li style="margin-bottom:10px;">Envie requisições para nossa API usando seu <code>Agent Code</code> e assinando com sua <code>Secret Key</code>.</li>
                <li style="margin-bottom:10px;">Nossa API enviará atualizações de saldo para o seu <strong>Endpoint</strong> sempre que houver uma aposta ou ganho.</li>
            </ol>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    navigator.clipboard.writeText(copyText.value).then(function() {
        // Feedback visual simples
        var btn = copyText.nextElementSibling;
        var originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i>';
        setTimeout(function(){
            btn.innerHTML = originalIcon;
        }, 2000);
    });
}
</script>

</body>
</html>
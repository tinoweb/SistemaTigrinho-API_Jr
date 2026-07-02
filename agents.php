<?php include "includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body {
        background-color: #f0f0f0;
        color: #333;
        font-family: Arial, sans-serif;
    }
    .main-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 15px;
    }
    .page-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .page-title {
        color: #333;
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    /* Card Style matching edit-agents.php container look */
    .data-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
        position: relative;
    }
    
    .data-row {
        margin-bottom: 12px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }
    .data-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .data-label {
        display: block;
        font-weight: bold;
        color: #555;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .data-value {
        display: block;
        color: #333;
        font-size: 16px;
        word-break: break-all;
    }
    
    .card-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }
    
    .btn-card {
        flex: 1;
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        text-decoration: none;
        color: white;
        font-weight: bold;
        transition: opacity 0.3s;
    }
    .btn-card:hover { opacity: 0.9; color: white; }
    
    .btn-edit { background-color: #333; } /* Matching edit-agents button color */
    .btn-delete { background-color: #d9534f; }
    
    .copy-btn {
        cursor: pointer;
        color: #333;
        margin-left: 5px;
    }
    
    .add-button-container {
        margin-bottom: 20px;
        text-align: center;
    }
    
    .btn-add {
        display: inline-block;
        padding: 12px 24px;
        background-color: #28a745;
        color: #fff;
        border-radius: 4px;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
    }
    .btn-add:hover { background-color: #218838; color: #fff; text-decoration: none; }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .data-card { padding: 15px; }
        .data-value { font-size: 14px; }
    }
</style>

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">Gerenciar Agentes</h1>
        <div class="add-button-container">
            <a href="edit-agents.php?act=add" class="btn-add">
                <i class="fa-solid fa-plus"></i> Novo Agente
            </a>
        </div>
    </div>

    <?php
    $users = getAG("agents");
    if($users):
        foreach ($users as $u):
    ?>
        <div class="data-card">
            <div class="data-row">
                <span class="data-label">ID</span>
                <span class="data-value">#<?= $u["id"] ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Agent Code</span>
                <span class="data-value"><?= $u["agentCode"] ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Saldo</span>
                <span class="data-value">R$ <?= number_format($u["saldo"], 2, ',', '.') ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Agent Token</span>
                <div class="data-value">
                    <?= substr($u["agentToken"], 0, 15) ?>... 
                    <i class="fa-regular fa-copy copy-btn" onclick="copyText('<?= $u["agentToken"] ?>')" title="Copiar Token"></i>
                </div>
            </div>
            
            <div class="data-row">
                <span class="data-label">Secret Key</span>
                <div class="data-value">
                    <?= substr($u["secretKey"], 0, 15) ?>...
                    <i class="fa-regular fa-copy copy-btn" onclick="copyText('<?= $u["secretKey"] ?>')" title="Copiar Key"></i>
                </div>
            </div>
            
            <div class="data-row">
                <span class="data-label">Probabilidades (Ganho / Bônus / RTP)</span>
                <span class="data-value">
                    <?= $u["probganho"] ?>% / <?= $u["probbonus"] ?>% / <?= $u["probganhortp"] ?>%
                </span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Callback URL</span>
                <span class="data-value"><?= !empty($u["callbackurl"]) ? $u["callbackurl"] : '<span style="color:#999">Não definida</span>' ?></span>
            </div>

            <div class="card-actions">
                <a href="edit-agents.php?act=edit&id=<?= $u["id"] ?>" class="btn-card btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </a>
                <a href="delete-agent.php?id=<?= $u["id"] ?>" class="btn-card btn-delete" onclick="return confirm('Deseja realmente deletar este agent?')">
                    <i class="fa-solid fa-trash"></i> Excluir
                </a>
            </div>
        </div>
    <?php 
        endforeach;
    else:
    ?>
        <div class="data-card">
            <p style="text-align: center; color: #777;">Nenhum agente encontrado.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function copyText(text){
    navigator.clipboard.writeText(text).then(()=>{
        // Simple toast or alert
        alert("Copiado para a área de transferência!");
    }).catch(err => {
        console.error('Erro ao copiar:', err);
    });
}
</script>

<?php include "includes/footer.php"; ?>

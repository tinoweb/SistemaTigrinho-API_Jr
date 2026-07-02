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
        font-weight: bold;
    }
    .page-stats {
        color: #666;
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    /* Card Style */
    .data-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
        position: relative;
    }
    
    .data-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }
    .data-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .data-label {
        font-weight: bold;
        color: #555;
        font-size: 14px;
        flex: 1;
    }
    
    .data-value {
        color: #333;
        font-size: 16px;
        text-align: right;
        flex: 1;
        word-break: break-word;
        padding-left: 10px;
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
        border: none;
        cursor: pointer;
        display: inline-block;
    }
    .btn-card:hover { opacity: 0.9; color: white; text-decoration: none; }
    
    .btn-edit { background-color: #333; }
    .btn-delete { background-color: #d9534f; }
    
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

    /* Badges */
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        display: inline-block;
    }
    .badge-success { background-color: #28a745; }
    .badge-warning { background-color: #ffc107; color: #333; }
    .badge-danger { background-color: #dc3545; }
    .badge-dark { background-color: #343a40; }
    .badge-secondary { background-color: #6c757d; }
    
    .money-pos { color: #28a745; font-weight: bold; }
    .money-neg { color: #dc3545; font-weight: bold; }

    /* Responsive */
    @media (max-width: 600px) {
        .data-card { padding: 15px; }
        .data-row { flex-direction: column; align-items: flex-start; }
        .data-value { text-align: left; padding-left: 0; margin-top: 4px; width: 100%; }
    }
</style>

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">Gerenciamento de Usuários</h1>
        <p class="page-stats">
            Total de usuários: <strong><?php 
                $agentId = isset($_COOKIE['admin_id']) ? intval($_COOKIE['admin_id']) : 0;
                echo countByAgent("users", $agentId);
            ?></strong>
        </p>
        <div class="add-button-container">
            <a href="edit-users.php?act=add" class="btn-add">
                <i class="fa-solid fa-plus"></i> Novo Usuário
            </a>
        </div>
    </div>

    <?php
    $users = getAll("users");
    if($users):
        foreach ($users as $u):
            // Calcular classe do RTP
            $rtp = floatval($u['rtp']);
            $rtpClass = 'badge-danger';
            if($rtp >= 80) $rtpClass = 'badge-success';
            elseif($rtp >= 60) $rtpClass = 'badge-warning';
            
            // Verificar se é influencer
            $isInfluencer = $u['isinfluencer'] == '1' || strtolower($u['isinfluencer']) == 'sim';
    ?>
        <div class="data-card">
            <div class="data-row">
                <span class="data-label">ID</span>
                <span class="data-value">#<?= $u['id'] ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Username</span>
                <span class="data-value"><?= htmlspecialchars($u['username']) ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Token</span>
                <span class="data-value" style="font-family: monospace; font-size: 14px;">
                    <?= htmlspecialchars($u['token']) ?>
                </span>
            </div>
            
            <div class="data-row">
                <span class="data-label">ATK</span>
                <span class="data-value"><?= htmlspecialchars($u['atk']) ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Saldo</span>
                <span class="data-value money-pos">
                    R$ <?= number_format(floatval($u['saldo']), 2, ',', '.') ?>
                </span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Apostado / Ganho</span>
                <span class="data-value">
                    R$ <?= number_format(floatval($u['valorapostado']), 2, ',', '.') ?> / 
                    R$ <?= number_format(floatval($u['valorganho']), 2, ',', '.') ?>
                </span>
            </div>
            
            <div class="data-row">
                <span class="data-label">RTP</span>
                <span class="data-value">
                    <span class="badge <?= $rtpClass ?>"><?= number_format($rtp, 1) ?>%</span>
                </span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Influencer</span>
                <span class="data-value">
                    <span class="badge <?= $isInfluencer ? 'badge-dark' : 'badge-secondary' ?>">
                        <?= $isInfluencer ? 'Sim' : 'Não' ?>
                    </span>
                </span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Agent ID</span>
                <span class="data-value"><?= htmlspecialchars($u['agentid']) ?></span>
            </div>

            <div class="card-actions">
                <a href="edit-users.php?act=edit&id=<?= $u['id'] ?>" class="btn-card btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </a>
                <a href="save.php?act=delete&id=<?= $u['id'] ?>&cat=users" class="btn-card btn-delete" onclick="return navConfirm(this.href);">
                    <i class="fa-solid fa-trash"></i> Excluir
                </a>
            </div>
        </div>
    <?php 
        endforeach;
    else:
    ?>
        <div class="data-card">
            <p style="text-align: center; color: #777;">Nenhum usuário encontrado.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function navConfirm(url){
    if(confirm('Deseja realmente realizar esta ação?')){
        window.location.href = url;
    }
    return false;
}
</script>

<?php include "includes/footer.php"; ?>

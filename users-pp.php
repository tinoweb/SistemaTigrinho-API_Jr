<?php
include "includes/header.php";
include "includes/connect_pp.php";

// Buscar usuários
$users = [];
if(!empty($myAgentCodePP)){
    // Busca apenas usuários deste agente
    $query = mysqli_query($link_pp, "SELECT * FROM users WHERE agentCode = '$myAgentCodePP' ORDER BY id DESC LIMIT 100");
    if($query){
        while($row = mysqli_fetch_assoc($query)){
            $users[] = $row;
        }
    }
}
?>

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
    .page-subtitle {
        color: #777;
        font-size: 14px;
        margin-bottom: 20px;
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
    
    .btn-back { 
        display: inline-block;
        padding: 10px 20px;
        background-color: #6c757d;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
    }
    .btn-back:hover { background-color: #5a6268; color: white; text-decoration: none; }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        color: white;
    }
    .status-active { background-color: #28a745; }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .data-card { padding: 15px; }
        .data-value { font-size: 14px; }
    }
</style>

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">Usuários (PP)</h1>
        <p class="page-subtitle">Gerenciando usuários do banco API PP</p>
        <a href="home-pp.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <?php if(empty($users)): ?>
        <div class="data-card">
            <p style="text-align: center; color: #777;">Nenhum usuário encontrado.</p>
        </div>
    <?php else: ?>
        <?php foreach($users as $u): ?>
        <div class="data-card">
            <div class="data-row">
                <span class="data-label">ID</span>
                <span class="data-value">#<?php echo $u['id']; ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Username</span>
                <span class="data-value"><strong><?php echo isset($u['username']) ? $u['username'] : (isset($u['agentCode']) ? $u['agentCode'] : 'N/A'); ?></strong></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Email</span>
                <span class="data-value"><?php echo isset($u['email']) ? $u['email'] : '-'; ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Saldo</span>
                <span class="data-value">R$ <?php echo number_format(isset($u['balance']) ? $u['balance'] : 0, 2, ',', '.'); ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Data Cadastro</span>
                <span class="data-value"><?php echo isset($u['created_at']) ? date('d/m/Y H:i', strtotime($u['created_at'])) : '-'; ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Status</span>
                <div class="data-value">
                    <span class="status-badge status-active">Ativo</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>

<?php
include "includes/header.php";
include "includes/connect_pp.php";

// Buscar agentes
$agents = [];
if($myAgentIdPP > 0){
    // Busca apenas os sub-agentes diretos
    $query = mysqli_query($link_pp, "SELECT * FROM agents WHERE parentId = $myAgentIdPP ORDER BY id DESC LIMIT 100");
    if($query){
        while($row = mysqli_fetch_assoc($query)){
            $agents[] = $row;
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
    
    .btn-edit { background-color: #333; }
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
    .status-inactive { background-color: #dc3545; }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .data-card { padding: 15px; }
        .data-value { font-size: 14px; }
    }
</style>

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">Agentes (PP)</h1>
        <p class="page-subtitle">Gerenciando agentes do banco API PP</p>
        <a href="home-pp.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <?php if(count($agents) > 0): ?>
        <?php foreach($agents as $agent): ?>
        <div class="data-card">
            <div class="data-row">
                <span class="data-label">ID</span>
                <span class="data-value">#<?php echo $agent['id']; ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Nome</span>
                <span class="data-value"><?php echo htmlspecialchars($agent['agentName']); ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Código</span>
                <span class="data-value"><?php echo htmlspecialchars($agent['agentCode']); ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Saldo</span>
                <span class="data-value">R$ <?php echo number_format($agent['balance'], 2, ',', '.'); ?></span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Porcentagem (%)</span>
                <span class="data-value"><?php echo $agent['percent']; ?>%</span>
            </div>
            
            <div class="data-row">
                <span class="data-label">Status</span>
                <div class="data-value">
                    <?php if($agent['status'] == 1): ?>
                        <span class="status-badge status-active">Ativo</span>
                    <?php else: ?>
                        <span class="status-badge status-inactive">Inativo</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-actions">
                <a href="agent-edit-pp.php?id=<?php echo $agent['id']; ?>" class="btn-card btn-edit">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="data-card">
            <p style="text-align: center; color: #777;">Nenhum agente encontrado.</p>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>

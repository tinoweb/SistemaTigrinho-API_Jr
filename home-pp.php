<?php
include "includes/header.php";
include "includes/connect_pp.php"; // Conecta ao banco apipp ($link_pp)

// Contadores do Painel PP
$usersCountPP = 0;
$agentsCountPP = 0;

// Conta Usuários (Tabela users)
// Filtro: Usuários vinculados ao agente logado.
// IMPORTANTE: Verificar como users são vinculados. Assumindo agentCode ou parentId.
// Geralmente em sistemas seamless, users têm 'agentCode' ou 'agent_id'.
// Vou usar agentCode por segurança, pois temos $myAgentCodePP.
$usersCountPP = 0;
if(!empty($myAgentCodePP)){
    // Ajuste aqui se a coluna for diferente (ex: agent_id)
    $qU = mysqli_query($link_pp, "SELECT COUNT(*) as c FROM users WHERE agentCode = '$myAgentCodePP'");
    if($qU){
        $row = mysqli_fetch_assoc($qU);
        $usersCountPP = $row['c'];
    }
}

// Conta Agentes (Tabela agents)
// Filtro: Agentes onde parentId = meu ID PP
$agentsCountPP = 0;
if($myAgentIdPP > 0){
    $qA = mysqli_query($link_pp, "SELECT COUNT(*) as c FROM agents WHERE parentId = $myAgentIdPP");
    if($qA){
        $row = mysqli_fetch_assoc($qA);
        $agentsCountPP = $row['c'];
    }
}

// Soma de Ganhos/Perdas (Saldo dos Agentes Filhos)
$totalBalancePP = 0;
if($myAgentIdPP > 0){
    $qB = mysqli_query($link_pp, "SELECT SUM(balance) as s FROM agents WHERE parentId = $myAgentIdPP");
    if($qB){
        $row = mysqli_fetch_assoc($qB);
        $totalBalancePP = floatval($row['s']);
    }
}
?>

<style>
    /* Estilos copiados e adaptados do home.php */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    .pp-header-section {
        background: linear-gradient(135deg, #2c3e50 0%, #000000 100%); /* Tom azulado/escuro para diferenciar */
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .pp-header-section::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .header-content { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; }

    .header-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        background: linear-gradient(45deg, #ffffff, #aab7c4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
        background: linear-gradient(180deg, #2c3e50, #4ca1af);
    }

    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); }

    .stat-icon {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #2c3e50, #4ca1af);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.5rem; margin-bottom: 15px;
    }

    .stat-number { font-size: 2rem; font-weight: 700; color: #2c3e50; margin-bottom: 5px; }
    .stat-label { color: #6c757d; font-size: 0.9rem; font-weight: 500; }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .action-card {
        background: #212529; /* Dark background for cards */
        padding: 30px;
        border-radius: 12px;
        text-decoration: none;
        color: white;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 200px;
    }

    .action-card:hover {
        transform: translateY(-5px);
        background: #2c3034;
        border-color: rgba(255,255,255,0.2);
        color: white;
        text-decoration: none;
    }

    .action-icon-box {
        width: 50px; height: 50px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 20px;
    }

    .action-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; }
    .action-desc { font-size: 0.9rem; color: #adb5bd; line-height: 1.5; }

    .back-hub-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8);
        padding: 8px 16px; border-radius: 6px; text-decoration: none;
        font-size: 0.9rem; margin-bottom: 10px;
        position: relative; z-index: 2;
    }
    .back-hub-btn:hover { background: rgba(255,255,255,0.2); color: white; text-decoration: none; }

</style>

<div class="admin-container">

    <!-- Header Section -->
    <div class="pp-header-section">
        <a href="hub.php" class="back-hub-btn"><i class="fa-solid fa-arrow-left"></i> Voltar para Seleção</a>
        <div class="header-content" style="margin-top: 15px;">
            <div>
                <h1 class="header-title">Painel API PP</h1>
                <p class="header-subtitle">Gerencie o sistema antigo integrado (apipp)</p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-power-off"></i> Sair
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-number"><?php echo number_format($usersCountPP, 0, ',', '.'); ?></div>
            <div class="stat-label">Total de Usuários</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
            <div class="stat-number"><?php echo number_format($agentsCountPP, 0, ',', '.'); ?></div>
            <div class="stat-label">Total de Agentes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
            <div class="stat-number">R$ <?php echo number_format($totalBalancePP, 2, ',', '.'); ?></div>
            <div class="stat-label">Saldo Total em Circulação</div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="actions-section" style="background: transparent; border: none; padding: 0; box-shadow: none;">
        <h2 class="section-title" style="color: #212529; margin-bottom: 20px;">
            <i class="fa-solid fa-bolt"></i> Ações Rápidas
        </h2>
        
        <div class="actions-grid">
            <!-- Gerenciar Usuários -->
            <a href="users-pp.php" class="action-card">
                <div class="action-icon-box">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="action-title">Gerenciar Usuários</div>
                    <div class="action-desc">Visualize e gerencie usuários cadastrados no banco API PP.</div>
                </div>
            </a>

            <!-- Gerenciar Agentes -->
            <a href="agents-pp.php" class="action-card">
                <div class="action-icon-box">
                    <i class="fa-solid fa-user-secret"></i>
                </div>
                <div>
                    <div class="action-title">Gerenciar Agentes</div>
                    <div class="action-desc">Controle de agentes, sub-agentes e comissões do sistema antigo.</div>
                </div>
            </a>

            <!-- Configurações -->
            <a href="settings-pp.php" class="action-card">
                <div class="action-icon-box">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <div>
                    <div class="action-title">Configurações & API</div>
                    <div class="action-desc">Veja suas credenciais (Token/Key), configure Endpoint e RTP.</div>
                </div>
            </a>
        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>

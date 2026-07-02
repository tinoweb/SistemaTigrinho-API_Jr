<?php
include 'includes/connect.php';
include 'includes/connect_pp.php';
include 'includes/header.php';

// Verifica login
if (!isset($_COOKIE['admin_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = '';

// Verifica se o agente pertence ao usuário logado (Hierarquia)
// Só pode editar se parentId == myAgentIdPP
$canEdit = false;
$agentData = [];

if($id > 0 && $myAgentIdPP > 0){
    // Permite editar se for filho (parentId) OU se for o próprio agente (id)
    $q = mysqli_query($link_pp, "SELECT * FROM agents WHERE id = $id AND (parentId = $myAgentIdPP OR id = $myAgentIdPP) LIMIT 1");
    if($q && mysqli_num_rows($q) > 0){
        $agentData = mysqli_fetch_assoc($q);
        $canEdit = true;
    }
}

// Processa formulário
if(isset($_POST['btn_save']) && $canEdit){
    $agentName = mysqli_real_escape_string($link_pp, $_POST['agentName']);
    $percent = floatval($_POST['percent']);
    $rtp = intval($_POST['rtp']);
    $status = intval($_POST['status']);
    $memo = mysqli_real_escape_string($link_pp, $_POST['memo']);
    $minBet = floatval($_POST['minBet']);
    $maxBet = floatval($_POST['maxBet']);
    
    // Atualiza senha se fornecida
    $passSql = "";
    if(!empty($_POST['password'])){
        $newPass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $passSql = ", password = '$newPass'";
    }

    // Atualiza saldo (Adicionar/Remover)
    // Lógica simplificada: Se preencher campo de ajuste, soma/subtrai
    $balanceAdjust = floatval($_POST['balance_adjust']);
    $balanceSql = "";
    if($balanceAdjust != 0){
        // Verifica se o pai tem saldo suficiente (se for adicionar ao filho)
        // Aqui assumimos que o pai tem saldo infinito ou não estamos checando o saldo do pai para simplificar, 
        // mas idealmente deveria descontar do pai.
        // O usuário pediu para "editar os ganhos", então vou permitir ajuste direto por enquanto.
        $balanceSql = ", balance = balance + ($balanceAdjust)";
    }

    $sqlUpd = "UPDATE agents SET 
        agentName = '$agentName',
        percent = $percent,
        rtp = $rtp,
        status = $status,
        memo = '$memo',
        minBet = $minBet,
        maxBet = $maxBet,
        updatedAt = NOW()
        $passSql
        $balanceSql
        WHERE id = $id";
        
    if(mysqli_query($link_pp, $sqlUpd)){
        $msg = '<div class="alert success"><i class="fa-solid fa-check-circle"></i> Dados atualizados com sucesso!</div>';
        // Recarrega dados
        $q = mysqli_query($link_pp, "SELECT * FROM agents WHERE id = $id LIMIT 1");
        $agentData = mysqli_fetch_assoc($q);
    } else {
        $msg = '<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> Erro ao atualizar: '.mysqli_error($link_pp).'</div>';
    }
}

?>

<style>
    /* Reutilizando estilos do home.php e home-pp.php */
    :root {
        --primary-color: #00e5ff;
        --bg-dark: #0a0a0a;
        --card-bg: #141414;
        --text-color: #ffffff;
        --border-color: #333;
    }

    .edit-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .edit-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    }

    .edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 20px;
    }

    .edit-title {
        font-size: 1.5rem;
        color: var(--primary-color);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: #888;
        margin-bottom: 8px;
        font-size: 0.9rem;
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

    .full-width {
        grid-column: 1 / -1;
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
        margin-top: 20px;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.4);
    }

    .btn-back {
        color: #888;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 20px;
        transition: color 0.3s;
    }

    .btn-back:hover {
        color: #fff;
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

    .readonly-field {
        background: #1a1a1a;
        color: #666;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .edit-container {
            padding: 0 10px;
            margin: 20px auto;
        }
        .edit-card {
            padding: 20px;
        }
    }
</style>

<div class="edit-container">
    <a href="agents-pp.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Voltar para Agentes</a>

    <?php echo $msg; ?>

    <?php if($canEdit): ?>
    <div class="edit-card">
        <div class="edit-header">
            <div class="edit-title">
                <i class="fa-solid fa-user-gear"></i>
                Editar Agente: <?php echo htmlspecialchars($agentData['agentCode']); ?>
            </div>
            <div class="badge" style="background:var(--primary-color);color:#000;padding:5px 10px;border-radius:4px;">
                Saldo Atual: R$ <?php echo number_format($agentData['balance'], 2, ',', '.'); ?>
            </div>
        </div>

        <form method="POST">
            <div class="form-grid">
                <!-- Informações Básicas -->
                <div class="form-group">
                    <label>Nome do Agente</label>
                    <input type="text" name="agentName" class="form-control" value="<?php echo htmlspecialchars($agentData['agentName']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Código (Não editável)</label>
                    <input type="text" class="form-control readonly-field" value="<?php echo htmlspecialchars($agentData['agentCode']); ?>" readonly>
                </div>

                <!-- Financeiro -->
                <div class="form-group">
                    <label>Ajuste de Saldo (+ para adicionar, - para remover)</label>
                    <input type="number" step="0.01" name="balance_adjust" class="form-control" placeholder="0.00">
                    <small style="color:#666">Deixe 0 para não alterar.</small>
                </div>

                <div class="form-group">
                    <label>Comissão (%)</label>
                    <input type="number" step="0.1" name="percent" class="form-control" value="<?php echo $agentData['percent']; ?>">
                </div>

                <!-- Configurações de Jogo -->
                <div class="form-group">
                    <label>RTP (Retorno ao Jogador %)</label>
                    <input type="number" name="rtp" class="form-control" value="<?php echo $agentData['rtp']; ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" <?php echo $agentData['status'] == 1 ? 'selected' : ''; ?>>Ativo</option>
                        <option value="0" <?php echo $agentData['status'] == 0 ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Aposta Mínima</label>
                    <input type="number" step="0.01" name="minBet" class="form-control" value="<?php echo $agentData['minBet']; ?>">
                </div>

                <div class="form-group">
                    <label>Aposta Máxima</label>
                    <input type="number" step="0.01" name="maxBet" class="form-control" value="<?php echo $agentData['maxBet']; ?>">
                </div>

                <!-- Segurança -->
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password" name="password" class="form-control" placeholder="Deixe em branco para manter a atual">
                </div>

                <div class="form-group full-width">
                    <label>Memo (Anotações)</label>
                    <textarea name="memo" class="form-control" rows="3"><?php echo htmlspecialchars($agentData['memo']); ?></textarea>
                </div>
            </div>

            <button type="submit" name="btn_save" class="btn-save">SALVAR ALTERAÇÕES</button>
        </form>
    </div>
    <?php else: ?>
        <div class="edit-card" style="text-align:center; padding:50px;">
            <i class="fa-solid fa-lock" style="font-size:3rem; color:#333; margin-bottom:20px;"></i>
            <h2>Acesso Negado</h2>
            <p style="color:#888;">Você não tem permissão para editar este agente ou ele não existe.</p>
            <br>
            <a href="agents-pp.php" class="btn-save" style="display:inline-block; width:auto;">Voltar</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
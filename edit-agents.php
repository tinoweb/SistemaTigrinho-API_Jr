<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (!empty($_SESSION['admin_root_auth']) && $_SESSION['admin_root_auth'] === '1') {
    include __DIR__ . '/includes/admin_header.php';
} else {
    include __DIR__ . '/includes/header.php';
}
				$data=[];

				$act = $_GET['act'];
				if($act == "edit") {
					$id = $_GET['id'];
					$users = getById("agents", $id);
				}
				?>

<style>
    body {
        background-color: #f0f0f0;
        color: #333;
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
        color: #333;
        text-align: center;
        margin-bottom: 30px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }
    .btn-success {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .btn-success:hover {
        background-color: #555;
    }
    .back-button {
        display: inline-block;
        padding: 10px 15px;
        margin-bottom: 20px;
        background-color: #333;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    .back-button:hover {
        background-color: #555;
        color: #fff;
        text-decoration: none;
    }
    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .btn-cancel {
        flex: 1;
        padding: 12px;
        background-color: #666;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.3s ease;
    }
    .btn-cancel:hover {
        background-color: #888;
        color: #fff;
        text-decoration: none;
    }
    .btn-success {
        flex: 1;
        padding: 12px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
</style>

<div class="container">
    <?php if (!empty($_SESSION['admin_root_auth']) && $_SESSION['admin_root_auth'] === '1') { ?>
        <a href="/admin/agents.php" class="back-button">← Voltar</a>
    <?php } else { ?>
        <a href="agents.php" class="back-button">← Voltar</a>
    <?php } ?>
    <h1><?= ($act === 'add') ? 'CRIAR AGENTE' : 'EDITAR AGENTE' ?></h1>

    <form method="post" action="save-agents.php" enctype='multipart/form-data'>
        <input name="cat" type="hidden" value="agents">
        <input name="id" type="hidden" value="<?=$id?>">
        <input name="act" type="hidden" value="<?=$act?>">
        <?php if ($act === 'add') {
            $users = [
                'agentCode' => '', 'senha' => '', 'saldo' => '', 'agentToken' => '', 'secretKey' => '',
                'probganho' => '', 'probbonus' => '', 'probganhortp' => '', 'probganhoinfluencer' => '',
                'probbonusinfluencer' => '', 'probganhoaposta' => '', 'probganhosaldo' => '', 'callbackurl' => ''
            ];
        } ?>

        <div class="form-group">
            <label>Agent Code</label>
            <input class="form-control" type="text" name="agentCode" value="<?=$users['agentCode'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Senha</label>
            <input class="form-control" type="text" name="senha" value="<?=$users['senha'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Saldo</label>
            <input class="form-control" type="text" name="saldo" value="<?=$users['saldo'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Agent Token</label>
            <input class="form-control" type="text" name="agentToken" value="<?=$users['agentToken'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Secret Key</label>
            <input class="form-control" type="text" name="secretKey" value="<?=$users['secretKey'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Ganho</label>
            <input class="form-control" type="text" name="probganho" value="<?=$users['probganho'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Bonus</label>
            <input class="form-control" type="text" name="probbonus" value="<?=$users['probbonus'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Ganho RTP</label>
            <input class="form-control" type="text" name="probganhortp" value="<?=$users['probganhortp'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Ganho Influencer</label>
            <input class="form-control" type="text" name="probganhoinfluencer" value="<?=$users['probganhoinfluencer'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Bonus Influencer</label>
            <input class="form-control" type="text" name="probbonusinfluencer" value="<?=$users['probbonusinfluencer'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Ganho Aposta</label>
            <input class="form-control" type="text" name="probganhoaposta" value="<?=$users['probganhoaposta'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Prob Ganho Saldo</label>
            <input class="form-control" type="text" name="probganhosaldo" value="<?=$users['probganhosaldo'] ?? ''?>" />
        </div>
        
        <div class="form-group">
            <label>Callback URL</label>
            <input class="form-control" type="text" name="callbackurl" value="<?=$users['callbackurl'] ?? ''?>" />
        </div>
        
        <div class="button-group">
            <a href="agents.php" class="btn-cancel">Cancelar</a>
            <input type="submit" value="Salvar" class="btn btn-success">
        </div>
    </form>
</div>

<?php include "includes/footer.php";?>


<?php
include "includes/header.php";
$data=[];

$act = $_GET['act'];
if($act == "edit") {
    $id = $_GET['id'];
    $users = getById("users", $id);
}
?>

<style>
/* Reset e base styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 20px;
}

/* Container principal */
.edit-users-container {
    max-width: 800px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
}

/* Header do formulário */
.form-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 30px;
    text-align: center;
    position: relative;
}

.form-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="90" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.form-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}

.form-subtitle {
    font-size: 16px;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

/* Formulário */
.form-content {
    padding: 40px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

/* Grupos de campos */
.field-group {
    position: relative;
}

.field-label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.field-input {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e8ecef;
    border-radius: 12px;
    font-size: 16px;
    background: #ffffff;
    transition: all 0.3s ease;
    color: #2c3e50;
}

.field-input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    transform: translateY(-2px);
}

.field-input:hover {
    border-color: #bdc3c7;
}

/* Campos especiais */
.field-group.highlight .field-input {
    border-color: #e74c3c;
    background: linear-gradient(135deg, #ffffff 0%, #fdf2f2 100%);
}

.field-group.highlight .field-label {
    color: #e74c3c;
}

.field-group.financial .field-input {
    border-color: #27ae60;
    background: linear-gradient(135deg, #ffffff 0%, #f2fdf5 100%);
}

.field-group.financial .field-label {
    color: #27ae60;
}

/* Botão de submit */
.submit-container {
    text-align: center;
    padding-top: 20px;
    border-top: 2px solid #ecf0f1;
}

.btn-save {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border: none;
    padding: 18px 40px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 10px 20px rgba(44, 62, 80, 0.3);
}

.btn-save:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(44, 62, 80, 0.4);
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
}

.btn-save:active {
    transform: translateY(-1px);
}

/* Responsividade */
@media (max-width: 768px) {
    .edit-users-container {
        margin: 10px;
        border-radius: 15px;
    }
    
    .form-content {
        padding: 25px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .form-header {
        padding: 25px 20px;
    }
    
    .form-title {
        font-size: 24px;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.field-group {
    animation: fadeInUp 0.6s ease forwards;
}

.field-group:nth-child(1) { animation-delay: 0.1s; }
.field-group:nth-child(2) { animation-delay: 0.2s; }
.field-group:nth-child(3) { animation-delay: 0.3s; }
.field-group:nth-child(4) { animation-delay: 0.4s; }
.field-group:nth-child(5) { animation-delay: 0.5s; }
.field-group:nth-child(6) { animation-delay: 0.6s; }
.field-group:nth-child(7) { animation-delay: 0.7s; }
.field-group:nth-child(8) { animation-delay: 0.8s; }
.field-group:nth-child(9) { animation-delay: 0.9s; }
.field-group:nth-child(10) { animation-delay: 1.0s; }

/* Ícones decorativos */
.field-group::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, transparent 0%, #3498db 50%, transparent 100%);
    border-radius: 2px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.field-group:hover::before {
    opacity: 1;
}
</style>

<div class="edit-users-container">
    <div class="form-header">
        <h1 class="form-title">Editar Usuário</h1>
        <p class="form-subtitle">Gerencie as informações do usuário com facilidade</p>
    </div>
    
    <div class="form-content">
        <form method="post" action="save.php" enctype='multipart/form-data'>
            <fieldset style="border: none;">
                <legend class="hidden-first" style="display: none;">Add New Users</legend>
                <input name="cat" type="hidden" value="users">
                <input name="id" type="hidden" value="<?=$id?>">
                <input name="act" type="hidden" value="<?=$act?>">
                
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label">Username</label>
                        <input class="field-input" type="text" name="username" value="<?=$users['username']?>" placeholder="Digite o nome de usuário" />
                    </div>
                    
                    <div class="field-group highlight">
                        <label class="field-label">Token</label>
                        <input class="field-input" type="text" name="token" value="<?=$users['token']?>" placeholder="Token de acesso" />
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">ATK</label>
                        <input class="field-input" type="text" name="atk" value="<?=$users['atk']?>" placeholder="Valor ATK" />
                    </div>
                    
                    <div class="field-group financial">
                        <label class="field-label">Saldo</label>
                        <input class="field-input" type="text" name="saldo" value="<?=$users['saldo']?>" placeholder="R$ 0,00" />
                    </div>
                    
                    <div class="field-group financial">
                        <label class="field-label">Valor Apostado</label>
                        <input class="field-input" type="text" name="valorapostado" value="<?=$users['valorapostado']?>" placeholder="R$ 0,00" />
                    </div>
                    
                    <div class="field-group financial">
                        <label class="field-label">Valor Debitado</label>
                        <input class="field-input" type="text" name="valordebitado" value="<?=$users['valordebitado']?>" placeholder="R$ 0,00" />
                    </div>
                    
                    <div class="field-group financial">
                        <label class="field-label">Valor Ganho</label>
                        <input class="field-input" type="text" name="valorganho" value="<?=$users['valorganho']?>" placeholder="R$ 0,00" />
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">RTP (%)</label>
                        <input class="field-input" type="text" name="rtp" value="<?=$users['rtp']?>" placeholder="0.00%" />
                    </div>
                    
                    <div class="field-group highlight">
                        <label class="field-label">Influenciador</label>
                        <select class="field-input" name="isinfluencer">
                            <option value="0" <?= $users['isinfluencer'] == '0' ? 'selected' : '' ?>>Não</option>
                            <option value="1" <?= $users['isinfluencer'] == '1' ? 'selected' : '' ?>>Sim</option>
                        </select>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Agente ID</label>
                        <input class="field-input" type="text" name="agentid" value="<?=$users['agentid']?>" placeholder="ID do agente" />
                    </div>
                </div>
                
                <div class="submit-container">
                    <input type="submit" value="Salvar Alterações" class="btn-save">
                </div>
            </fieldset>
        </form>
    </div>
</div>

<script>
// Adicionar interatividade aos campos
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.field-input');
    
    inputs.forEach(input => {
        // Efeito de foco suave
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
        
        // Formatação automática para campos monetários
        if (input.name.includes('saldo') || input.name.includes('valor')) {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value) {
                    value = (parseInt(value) / 100).toFixed(2);
                    this.value = 'R$ ' + value.replace('.', ',');
                }
            });
        }
        
        // Formatação para RTP
        if (input.name === 'rtp') {
            input.addEventListener('input', function() {
                let value = this.value.replace(/[^\d.,]/g, '');
                if (value && !value.includes('%')) {
                    this.value = value + '%';
                }
            });
        }
    });
    
    // Animação do botão de submit
    const submitBtn = document.querySelector('.btn-save');
    submitBtn.addEventListener('click', function(e) {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 150);
    });
});
</script>

<?php include "includes/footer.php";?>


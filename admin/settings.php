<?php
// admin/settings.php
ini_set('display_errors', '1');
error_reporting(E_ALL);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if (empty($_SESSION['admin_root_auth']) || $_SESSION['admin_root_auth'] !== '1') {
    header('Location: /admin/login.php');
    exit;
}

include(__DIR__ . '/../includes/connect.php');
include(__DIR__ . '/../includes/data.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar Logo
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['site_logo']['tmp_name'];
        $fileName = $_FILES['site_logo']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'svg', 'webp', 'ico');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = 'logo_' . time() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../uploads/settings/';
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $logoUrl = '/uploads/settings/' . $newFileName;
                updateSetting('site_logo', $logoUrl);
                $message .= 'Logo atualizado com sucesso. ';
            } else {
                $message .= 'Erro ao mover o arquivo de logo. ';
            }
        } else {
            $message .= 'Formato de logo inválido. ';
        }
    }

    // Processar Favicon
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['site_favicon']['tmp_name'];
        $fileName = $_FILES['site_favicon']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'svg', 'webp', 'ico');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = 'favicon_' . time() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../uploads/settings/';
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $faviconUrl = '/uploads/settings/' . $newFileName;
                updateSetting('site_favicon', $faviconUrl);
                $message .= 'Favicon atualizado com sucesso. ';
            } else {
                $message .= 'Erro ao mover o arquivo de favicon. ';
            }
        } else {
            $message .= 'Formato de favicon inválido. ';
        }
    }
    // Processar Alteração de Credenciais Admin
    if (!empty($_POST['admin_user'])) {
        updateSetting('admin_user', trim($_POST['admin_user']));
        
        if (!empty($_POST['admin_pass'])) {
            updateSetting('admin_pass', trim($_POST['admin_pass']));
            $message .= 'Usuário e Senha atualizados. ';
        } else {
            $message .= 'Usuário atualizado. ';
        }
    }
}

$currentLogo = getSetting('site_logo');
$currentFavicon = getSetting('site_favicon');
$currentAdminUser = getSetting('admin_user');
if(empty($currentAdminUser)) $currentAdminUser = 'admin';

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="container" style="margin-top: 20px;">
    <div class="row">
        <div class="col-md-12">
            <h2>Configurações do Site</h2>
            <a href="index.php" class="btn btn-default">Voltar</a>
            <hr>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="settings.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Logo Atual</label><br>
                    <?php if ($currentLogo): ?>
                        <img src="<?php echo $currentLogo; ?>" style="max-height: 100px; background: #ccc; padding: 5px;">
                    <?php else: ?>
                        <p>Nenhum logo configurado.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="site_logo">Alterar Logo</label>
                    <input type="file" name="site_logo" id="site_logo" class="form-control">
                    <small>Formatos aceitos: jpg, png, gif, svg, webp</small>
                </div>

                <hr>

                <div class="form-group">
                    <label>Favicon Atual</label><br>
                    <?php if ($currentFavicon): ?>
                        <img src="<?php echo $currentFavicon; ?>" style="max-height: 32px; background: #ccc; padding: 5px;">
                    <?php else: ?>
                        <p>Nenhum favicon configurado.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="site_favicon">Alterar Favicon</label>
                    <input type="file" name="site_favicon" id="site_favicon" class="form-control">
                    <small>Formatos aceitos: ico, png, jpg, svg</small>
                </div>

                <hr>
                <h3>Credenciais de Acesso Admin</h3>
                <div class="form-group">
                    <label for="admin_user">Usuário Admin</label>
                    <input type="text" name="admin_user" id="admin_user" class="form-control" value="<?php echo htmlspecialchars($currentAdminUser); ?>" required>
                </div>
                <div class="form-group">
                    <label for="admin_pass">Nova Senha Admin</label>
                    <input type="password" name="admin_pass" id="admin_pass" class="form-control" placeholder="Deixe em branco para não alterar" autocomplete="new-password">
                    <small>Preencha apenas se quiser alterar a senha atual.</small>
                </div>

                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

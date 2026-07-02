<?php
// Cabeçalho do admin raiz (não vinculado a agentes)
include __DIR__ . '/../includes/admin_header.php';
?>

<style>
  body { background-color: #f8f9fa; color: #212529; font-family: 'Inter', sans-serif; }
  .admin-adv-container { max-width: 1200px; margin: 20px auto; padding: 20px; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); }
  .admin-adv-title { display:flex; align-items:center; gap:10px; font-size:1.6rem; font-weight:700; margin-bottom: 16px; }
  .admin-adv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
  .admin-card { background:#f8f9fa; border:2px solid #e9ecef; border-radius:12px; padding:20px; text-decoration:none; color:inherit; transition: .2s; display:block; }
  .admin-card:hover { border-color:#000; transform: translateY(-3px); }
  .admin-card h3 { margin:0 0 10px 0; font-size:1.1rem; font-weight:700; }
  .admin-card p { margin:0; color:#495057; font-size:.95rem; }
  .danger { border-color:#dc3545; }
  .danger:hover { border-color:#b02a37; }
  .back { display:inline-block; padding:10px 14px; background:#212529; color:#fff; border-radius:8px; text-decoration:none; }
</style>

<div class="admin-adv-container">
  <div class="admin-adv-title"><span class="glyphicon glyphicon-cog"></span> Admin Avançado</div>
  <a href="../home.php" class="back">Voltar</a>
  <p style="margin:12px 0 20px; color:#495057">Gerencie usuários, agentes e jogos de forma centralizada.</p>

  <div class="admin-adv-grid">
    <a href="users.php" class="admin-card">
      <h3><span class="glyphicon glyphicon-user"></span> Usuários</h3>
      <p>Editar, localizar, deletar usuários, inclusive operações em massa.</p>
    </a>

    <a href="agents.php" class="admin-card">
      <h3><span class="glyphicon glyphicon-briefcase"></span> Agentes</h3>
      <p>Editar e deletar agentes; ao deletar um agente, deletar usuários vinculados.</p>
    </a>

    <a href="games.php" class="admin-card">
      <h3><span class="glyphicon glyphicon-picture"></span> Jogos</h3>
      <p>Adicionar jogos (nome, ID, imagem), e fazer upload/gestão das imagens.</p>
    </a>

    <a href="settings.php" class="admin-card">
      <h3><span class="glyphicon glyphicon-wrench"></span> Configurações</h3>
      <p>Alterar logo e favicon do site.</p>
    </a>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
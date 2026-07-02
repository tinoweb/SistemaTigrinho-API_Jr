<?php
include __DIR__ . '/../includes/admin_header.php';
?>

<style>
  body { background:#f8f9fa; color:#212529; }
  .container { max-width: 1200px; margin: 20px auto; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:20px; }
  .actions { display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px; }
  .btn { padding:10px 14px; border-radius:8px; text-decoration:none; display:inline-block; }
  .btn-dark { background:#212529; color:#fff; }
  .btn-danger { background:#dc3545; color:#fff; }
  .btn-outline { border:2px solid #212529; color:#212529; background:#fff; }
  .info { color:#495057; margin-bottom: 10px; }
  table { width:100%; border-collapse:collapse; }
  thead th { background:#212529; color:#fff; padding:10px; text-transform:uppercase; font-size:.85rem; }
  tbody td { padding:10px; border-bottom:1px solid #e9ecef; }
</style>

<div class="container">
  <a href="index.php" class="btn btn-outline">Voltar</a>
  <div class="info">
      <strong>Total de usuários no sistema: <?php echo counting("users", "id"); ?></strong><br>
      Visualizando todos os usuários da plataforma (Global Admin).
  </div>
  <div class="actions">
    <a href="#" class="btn btn-dark" onclick="deleteAllUsers(); return false;">Deletar TODOS usuários deste agente</a>
    <a href="#" class="btn btn-danger" onclick="deleteAgentAndUsers(); return false;">Deletar agente e usuários vinculados</a>
  </div>

  <table id="sorted">
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuário</th>
        <th>Token</th>
        <th>Saldo</th>
        <th>Agent ID</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $users = qSELECT("SELECT * FROM users ORDER BY id DESC");
      if ($users) foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
          <td><?= htmlspecialchars($u['usertoken'] ?? '') ?></td>
          <td><?= htmlspecialchars($u['saldo'] ?? '') ?></td>
          <td><?= htmlspecialchars($u['agentid'] ?? '') ?></td>
          <td><a href="../edit-users.php?act=edit&id=<?= $u['id'] ?>">Editar</a> | <a href="#" onclick="deleteUser(<?= (int)$u['id'] ?>); return false;">Deletar</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
async function apiPost(url, body) {
  const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
  return res.json().catch(() => ({}));
}

function confirmAction(msg) { return confirm(msg); }

async function deleteAllUsers() {
  if (!confirmAction('Confirma deletar TODOS os usuários deste agente?')) return;
  // TODO: implementar endpoint PHP para deleção em massa por agentid
  alert('Endpoint de deleção em massa a implementar.');
}

async function deleteAgentAndUsers() {
  if (!confirmAction('Confirma deletar o agente e TODOS os usuários vinculados?')) return;
  alert('Endpoint para deletar agente + usuários a implementar.');
}

async function deleteUser(id) {
  if (!confirmAction('Confirma deletar este usuário?')) return;
  alert('Endpoint para deleção de usuário individual a implementar.');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
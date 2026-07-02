<?php
include __DIR__ . '/../includes/admin_header.php';
?>

<style>
  body { background:#f8f9fa; color:#212529; }
  .container { max-width: 1200px; margin: 20px auto; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:20px; }
  table { width:100%; border-collapse:collapse; }
  thead th { background:#212529; color:#fff; padding:10px; text-transform:uppercase; font-size:.85rem; }
  tbody td { padding:10px; border-bottom:1px solid #e9ecef; }
</style>

<div class="container">
  <a href="index.php" class="back-button">Voltar</a>
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
    <h3 style="margin:0">Agentes</h3>
    <a href="../edit-agents.php?act=add" class="back-button" style="display:inline-block; background:#28a745;">+ Novo agente</a>
  </div>
  <p style="margin:8px 0 16px;color:#495057">Crie, edite e delete agentes.</p>
  <table id="sorted">
    <thead>
      <tr>
        <th>ID</th>
        <th>Agent Code</th>
        <th>Saldo</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      // Admin avançado: listar todos agentes sem filtro de agentid
      $agents = qSELECT('SELECT * FROM agents');
      if($agents) foreach ($agents as $a): ?>
      <tr>
        <td><?= $a['id'] ?></td>
        <td><?= htmlspecialchars($a['agentCode'] ?? '') ?></td>
        <td><?= htmlspecialchars($a['saldo'] ?? '') ?></td>
        <td>
          <a href="../edit-agents.php?act=edit&id=<?= $a['id'] ?>">Editar</a> |
          <a href="#" onclick="deleteAgent(<?= (int)$a['id'] ?>); return false;">Deletar agente + usuários</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
async function deleteAgent(id) {
  if (!confirm('Confirma deletar o agente e TODOS os usuários vinculados?')) return;
  const res = await fetch('api/agents_delete.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ id }) });
  const out = await res.json().catch(()=>({ok:false}));
  if (out.ok) { alert('Agente e usuários deletados.'); location.reload(); }
  else { alert('Erro: ' + (out.error || 'falha')); }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
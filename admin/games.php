<?php
include __DIR__ . '/../includes/admin_header.php';
?>

<style>
  body { background:#f8f9fa; color:#212529; }
  .container { max-width: 1200px; margin: 20px auto; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:20px; }
  .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:16px; }
  .card { background:#f8f9fa; border:2px solid #e9ecef; border-radius:12px; padding:14px; }
  .card img { width:100%; height:140px; object-fit:cover; border-radius:8px; background:#e9ecef; }
  .card h4 { margin:10px 0 4px; font-weight:700; }
  .actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
  .btn { padding:8px 12px; border-radius:8px; text-decoration:none; display:inline-block; cursor:pointer; }
  .btn-dark { background:#212529; color:#fff; }
  .btn-outline { border:2px solid #212529; color:#212529; background:#fff; }
  .btn-danger { background:#dc3545; color:#fff; }
  .form-row { display:grid; grid-template-columns: 2fr 1fr 2fr 1fr; gap:10px; margin-top: 16px; }
  input[type="text"], input[type="number"] { width:100%; padding:10px; border:2px solid #e9ecef; border-radius:8px; }
</style>

<div class="container">
  <a href="index.php" class="btn btn-outline">Voltar</a>
  <h3>Gerenciar Jogos</h3>
  <p style="color:#495057">Adicione, edite e remova jogos (nome, ID, imagem). Upload de imagens será salvo no diretório uploads/.</p>

  <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
    <button class="btn btn-dark" onclick="bulkSetStatus('ativo','manutencao')">PG: Ativo | PP: Manutenção</button>
    <button class="btn btn-outline" onclick="bulkSetStatus('ativo')">Ativar todos (PG)</button>
    <small style="color:#6c757d">Ação rápida para atualizar status por provedor</small>
  </div>

  <div class="form-row">
    <input type="text" id="gname" placeholder="Nome do jogo" />
    <input type="text" id="gid" placeholder="Código do jogo" />
    <div>
      <label for="gvendor" style="display:block; font-size:.85rem; color:#6c757d; margin-bottom:4px">Empresa</label>
      <select id="gvendor" style="width:100%; padding:10px; border:2px solid #e9ecef; border-radius:8px">
        <option value="">Selecionar</option>
        <option value="PG">PG</option>
        <option value="PP">PP</option>
      </select>
    </div>
    <div>
      <label for="gstatus" style="display:block; font-size:.85rem; color:#6c757d; margin-bottom:4px">Status</label>
      <select id="gstatus" style="width:100%; padding:10px; border:2px solid #e9ecef; border-radius:8px">
        <option value="">Selecionar</option>
        <option value="ativo">Ativo</option>
        <option value="manutencao">Manutenção</option>
        <option value="inativo">Inativo</option>
      </select>
    </div>
    <div>
      <input type="file" id="gfile" accept="image/*" />
      <small style="color:#6c757d">PNG/JPG até 2MB</small>
    </div>
    <button class="btn btn-dark" onclick="addGame()">Adicionar</button>
  </div>

  <div id="gamesGrid" class="grid"></div>

  <div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:20px; width:100%; max-width:480px; box-shadow:0 8px 24px rgba(0,0,0,0.08);">
      <h4 style="margin:0 0 12px">Editar Jogo</h4>
      <div style="display:grid; gap:10px;">
        <input type="text" id="editName" placeholder="Nome" />
        <input type="text" id="editId" placeholder="Código do jogo" />
        <div>
          <label for="editVendor" style="display:block; font-size:.85rem; color:#6c757d; margin-bottom:4px">Empresa</label>
          <select id="editVendor" style="width:100%; padding:10px; border:2px solid #e9ecef; border-radius:8px">
            <option value="">Selecionar</option>
            <option value="PG">PG</option>
            <option value="PP">PP</option>
          </select>
        </div>
        <div>
          <label for="editStatus" style="display:block; font-size:.85rem; color:#6c757d; margin-bottom:4px">Status</label>
          <select id="editStatus" style="width:100%; padding:10px; border:2px solid #e9ecef; border-radius:8px">
            <option value="">Selecionar</option>
            <option value="ativo">Ativo</option>
            <option value="manutencao">Manutenção</option>
            <option value="inativo">Inativo</option>
          </select>
        </div>
        <div>
          <input type="file" id="editFile" accept="image/*" />
          <small style="color:#6c757d">PNG/JPG até 2MB</small>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
          <button class="btn btn-outline" type="button" onclick="closeModal()">Cancelar</button>
          <button class="btn btn-dark" type="button" onclick="submitEdit()">Salvar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const jsonPath = '../includes/games.json';

async function loadGames() {
  const res = await fetch(jsonPath + '?t=' + Date.now());
  const data = await res.json().catch(() => []);
  renderGrid(data);
}

function renderGrid(games) {
  const grid = document.getElementById('gamesGrid');
  grid.innerHTML = '';
  games.forEach((g, idx) => {
    const div = document.createElement('div');
    div.className = 'card';
    const status = (g.status||'').toLowerCase();
    const badgeColor = status==='ativo' ? '#2ecc71' : status==='manutencao' ? '#f1c40f' : status==='inativo' ? '#e74c3c' : '#6c757d';
    const statusLabel = status ? (status==='ativo'?'Ativo':status==='manutencao'?'Manutenção':'Inativo') : 'Sem status';
    div.innerHTML = `
      <img src="${g.image || ''}" alt="${g.name}" onerror="this.style.background='#e9ecef';this.src='';" />
      <h4>${escapeHtml(g.name)} <small style="color:#6c757d;">#${escapeHtml(g.id)}</small></h4>
      <div style="margin-top:6px"><span style="display:inline-block;padding:4px 8px;border-radius:6px;background:${badgeColor};color:#fff;font-size:.8rem;font-weight:700;">${statusLabel}</span></div>
      <div class="actions">
        <a class="btn btn-outline" href="#" onclick="editGame(${idx});return false;">Editar</a>
        <a class="btn btn-danger" href="#" onclick="deleteGame(${idx});return false;">Deletar</a>
      </div>
    `;
    grid.appendChild(div);
  });
}

function escapeHtml(str) { return String(str).replace(/[&<>"]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s])); }

async function addGame() {
  const name = document.getElementById('gname').value.trim();
  const id = document.getElementById('gid').value.trim();
  const vendor = document.getElementById('gvendor').value.trim();
  const status = document.getElementById('gstatus').value.trim();
  const file = document.getElementById('gfile').files[0] || null;
  if (!name || !id) { alert('Preencha nome e Código corretamente.'); return; }
  let image = null;
  if (file) {
    const form = new FormData();
    form.append('file', file);
    form.append('game_id', String(id));
  const up = await fetch('api/upload_game_image.php', { method:'POST', body: form, credentials: 'same-origin' });
    const upRes = await up.json().catch(()=>({ok:false}));
    if (!upRes.ok) { alert('Upload falhou: '+(upRes.error||'erro')); return; }
    image = upRes.url || null;
  }
  const payload = { action:'add', name, id, image };
  if (vendor) payload.vendor = vendor;
  if (status) payload.status = status;
  const res = await fetch('api/games_mutation.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials: 'same-origin' });
  const out = await res.json().catch(()=>({ok:false}));
  if (out.ok) { alert('Jogo adicionado.'); loadGames(); } else { alert('Erro: '+(out.error||'falha')); }
}

let currentEdit = null;
function editGame(idx) {
  fetch(jsonPath).then(r=>r.json()).then(games => {
    const g = games[idx];
    currentEdit = { originalId: g.id };
    document.getElementById('editName').value = g.name;
    document.getElementById('editId').value = g.id;
    document.getElementById('editVendor').value = (g.vendor || '');
    document.getElementById('editStatus').value = (g.status || '');
    document.getElementById('editFile').value = '';
    openModal();
  });
}

function openModal(){
  const m = document.getElementById('editModal');
  m.style.display = 'flex';
}
function closeModal(){
  const m = document.getElementById('editModal');
  m.style.display = 'none';
  currentEdit = null;
}

async function submitEdit(){
  if (!currentEdit) return;
  const name = document.getElementById('editName').value.trim();
  const id = document.getElementById('editId').value.trim();
  const vendor = document.getElementById('editVendor').value.trim();
  const status = document.getElementById('editStatus').value.trim();
  const file = document.getElementById('editFile').files[0] || null;
  if (!name || !id) { alert('Preencha nome e Código corretamente.'); return; }
  let image = null;
  if (file) {
    const form = new FormData();
    form.append('file', file);
    form.append('game_id', String(id));
    const up = await fetch('api/upload_game_image.php', { method:'POST', body: form, credentials: 'same-origin' });
    const upRes = await up.json().catch(()=>({ok:false}));
    if (!upRes.ok) { alert('Upload falhou: '+(upRes.error||'erro')); return; }
    image = upRes.url || null;
  }
  const payload = { action:'update', targetId: currentEdit.originalId, name, id };
  if (image !== null) payload.image = image;
  if (vendor) payload.vendor = vendor;
  if (status) payload.status = status;
  const res = await fetch('api/games_mutation.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials: 'same-origin' });
  const out = await res.json().catch(()=>({ok:false}));
  if (out.ok) { closeModal(); alert('Jogo atualizado.'); loadGames(); }
  else { alert('Erro: '+(out.error||'falha')); }
}

function deleteGame(idx) {
  if (!confirm('Confirma deletar este jogo?')) return;
  fetch(jsonPath).then(r=>r.json()).then(async games => {
    const g = games[idx];
    const res = await fetch('api/games_mutation.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'delete', targetId: g.id }) });
    const out = await res.json().catch(()=>({ok:false}));
    if (out.ok) { alert('Jogo removido.'); loadGames(); } else { alert('Erro: '+(out.error||'falha')); }
  });
}

loadGames();

async function bulkSetStatus(pg, pp){
  const ok = confirm(`Aplicar status em massa?\nPG → ${pg}\nPP → ${pp}`);
  if (!ok) return;
  const res = await fetch('api/games_mutation.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'bulk_set_status', pg_status: pg, pp_status: pp }), credentials: 'same-origin' });
  const out = await res.json().catch(()=>({ok:false}));
  if (out.ok) { alert('Status atualizado por provedor.'); loadGames(); } else { alert('Erro: '+(out.error||'falha')); }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
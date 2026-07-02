<?php
include "includes/header.php";
?>

<style>
  body {
    background-color: #f8f9fa;
    color: #212529;
    font-family: 'Inter', sans-serif;
  }

  .games-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  }

  .games-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .games-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.6rem;
    font-weight: 700;
  }

  .back-button {
    display: inline-block;
    padding: 10px 15px;
    background-color: #212529;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: background-color 0.2s ease, transform 0.2s ease;
  }

  .back-button:hover {
    background-color: #000000;
    transform: translateY(-1px);
  }

  .games-table {
    width: 100%;
    border-collapse: collapse;
  }

  .games-table thead th {
    background-color: #212529;
    color: #fff;
    padding: 12px 15px;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    border: none;
  }

  .games-table tbody td {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
  }

  .game-id {
    font-weight: 700;
    color: #212529;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 6px;
    display: inline-block;
  }

  .img-placeholder {
    width: 80px;
    height: 48px;
    border: 2px dashed #ced4da;
    border-radius: 6px;
    color: #6c757d;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
  }
  /* Chip de empresa (PG/PP) — versão compacta e discreta */
  .vendor-chip {
    display:inline-flex; align-items:center; gap:4px; padding:2px 6px; border-radius:6px; font-weight:600;
    background:#f1f3f5; color:#495057; border:1px solid #dee2e6; font-size:.75rem;
  }
  .vendor-logo { width:18px; height:auto; display:inline-block; }
  @media (max-width:768px) { .vendor-chip { font-size:.7rem; padding:2px 5px; } }
</style>

  <div class="games-container">
  <div class="games-header">
    <div class="games-title">
      <i class="glyphicon glyphicon-console"></i>
      Jogos Disponíveis
    </div>
    <a href="home.php" class="back-button">Voltar</a>
  </div>

  <p style="margin-bottom: 16px; color:#495057">Lista de jogos com Nome, ID e espaço para imagem (em breve).</p>

  <style>
    .grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; }
    .card { background:#f8f9fa; border:2px solid #e9ecef; border-radius:12px; padding:16px; }
    /* Contêiner da imagem para permitir overlay do logo */
    .img-wrap { position:relative; width:100%; margin:0; }
    /* APLICAR estilo só na imagem principal, não no logo */
    .img-wrap .game-image { width:100%; height:220px; object-fit:contain; border-radius:8px; background:transparent; padding:0; display:block; }
    /* Marca de vendor sobreposta no canto superior esquerdo (bem pequena) */
    .vendor-mark { position:absolute; top:2px; left:2px; display:inline-flex; align-items:center; pointer-events:none; z-index:5; }
    .vendor-icon { height:8px; width:auto; display:block; opacity:.85; }
    .card h4 { margin:12px 0 0; font-weight:800; font-size:1.2rem; display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .badge { background:#212529; color:#fff; padding:8px 12px; border-radius:8px; font-size:1.2rem; display:inline-flex; align-items:center; gap:8px; }
    .copy-btn { background:#28a745; color:#fff; border:none; border-radius:6px; padding:4px 8px; cursor:pointer; font-size:.85rem; }
    /* Badge de status do jogo */
    .status-badge { display:inline-block; padding:4px 8px; border-radius:6px; color:#fff; font-size:.8rem; font-weight:700; }
    .status-ativo { background:#2ecc71; }
    .status-manutencao { background:#f1c40f; color:#212529; }
    .status-inativo { background:#e74c3c; }
    @media (max-width:1200px){ .grid { grid-template-columns: repeat(3, 1fr); } .img-wrap .game-image { height:200px; } }
    @media (max-width:992px){ .grid { grid-template-columns: repeat(2, 1fr); } .img-wrap .game-image { height:180px; } }
    @media (max-width:768px){ .grid { grid-template-columns: repeat(2, 1fr); } .img-wrap .game-image { height:160px; } .vendor-icon { height:8px; } }
    @media (max-width:480px){ .grid { grid-template-columns: 1fr; } .img-wrap .game-image { height:140px; } }
  </style>
  <style>
    .section-header { display:flex; align-items:center; gap:8px; font-weight:700; font-size:1rem; color:#343a40; margin:8px 0; }
    .section-logo { height:16px; width:auto; display:block; opacity:.9; }
    .vendor-section { margin-bottom:20px; }
  </style>
  <div class="vendor-section">
    <div class="section-header"><img class="section-logo" src="includes/img/PGSOFT.webp" alt="PG" onerror="this.style.display='none'"> PG Soft</div>
    <div id="pgGrid" class="grid"></div>
  </div>
  <div class="vendor-section">
    <div class="section-header"><img class="section-logo" src="includes/img/PRAGMATIC.webp" alt="PP" onerror="this.style.display='none'"> Pragmatic Play</div>
    <div id="ppGrid" class="grid"></div>
  </div>
  <script>
    (function(){
      const pgGrid = document.getElementById('pgGrid');
      const ppGrid = document.getElementById('ppGrid');
      const jsonPath = 'includes/games.json';
      function renderGrid(target, list){
        target.innerHTML = '';
        list.forEach(g => {
          const vendor = (g.vendor||'').toUpperCase();
          const logo = vendor === 'PG' ? 'includes/img/PGSOFT.webp' : (vendor === 'PP' ? 'includes/img/PRAGMATIC.webp' : '');
          const vendorMark = vendor ? (logo
              ? `<span class="vendor-mark"><img class="vendor-icon" src="${logo}" alt="${vendor}" onerror="this.replaceWith(document.createTextNode('${vendor}'))"/></span>`
              : `<span class="vendor-mark">${vendor}</span>`
            ) : '';
          const img = g.image
            ? `<div class="img-wrap"><img class="game-image" src="${escapeHtml(g.image)}" alt="${escapeHtml(g.name)}" onerror="this.style.background='#e9ecef';this.src='';" />${vendorMark}</div>`
            : `<div class="img-wrap"><div class='img-placeholder' style='width:100%;height:160px;'>Em breve</div>${vendorMark}</div>`;
          const status = (g.status||'').toLowerCase();
          const statusClass = status === 'ativo' ? 'status-ativo' : status === 'manutencao' ? 'status-manutencao' : status === 'inativo' ? 'status-inativo' : '';
          const statusLabel = status ? (status==='ativo'?'Ativo':status==='manutencao'?'Manutenção':'Inativo') : '';
          const card = document.createElement('div');
          card.className = 'card';
          card.innerHTML = `${img}
            <h4>${escapeHtml(g.name)}
              <span class='badge'>#${escapeHtml(String(g.id))} <button class='copy-btn' onclick=\"copyId('${escapeHtml(String(g.id))}')\">Copiar</button></span>
            </h4>
            ${statusLabel ? `<div style='margin-top:6px'><span class='status-badge ${statusClass}'>${statusLabel}</span></div>` : ''}
          `;
          target.appendChild(card);
        });
      }

      fetch(jsonPath + '?t=' + Date.now()).then(r=>r.json()).then(games => {
        const pgList = games.filter(g => (g.vendor||'').toUpperCase() === 'PG');
        const ppList = games.filter(g => (g.vendor||'').toUpperCase() === 'PP');
        renderGrid(pgGrid, pgList);
        renderGrid(ppGrid, ppList);
      }).catch(()=>{
        pgGrid.innerHTML = '<div style="color:#dc3545">Falha ao carregar jogos.</div>';
        ppGrid.innerHTML = '';
      });
      function escapeHtml(str){ return String(str).replace(/[&<>"]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s])); }
      window.copyId = function(id){
        navigator.clipboard.writeText(String(id)).then(function(){
          const n = document.createElement('div');
          n.textContent = 'ID copiado: ' + id;
          n.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#28a745;color:#fff;padding:10px 14px;border-radius:8px;box-shadow:0 6px 16px rgba(0,0,0,.15);z-index:9999;';
          document.body.appendChild(n);
          setTimeout(()=>n.remove(), 1500);
        }).catch(function(){ alert('Falha ao copiar'); });
      }
    })();
  </script>
</div>

<?php include "includes/footer.php"; ?>
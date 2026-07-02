<?php
include "includes/header.php";
$gamesArr = [];
$gamesCount = 0;
$jsonPath = __DIR__ . '/includes/games.json';
if (file_exists($jsonPath)) {
  $tmp = json_decode(file_get_contents($jsonPath), true);
  if (is_array($tmp)) { $gamesArr = $tmp; $gamesCount = count($tmp); }
}
$agentsCount = 0;
$usersCount = 0;
$agentId = isset($_COOKIE['admin_id']) ? intval($_COOKIE['admin_id']) : 0;
if (isset($isAdmin) && $isAdmin) {
  $agentsRow = qSELECT("SELECT COUNT(*) AS c FROM agents");
  $agentsCount = ($agentsRow && isset($agentsRow[0]['c'])) ? (int)$agentsRow[0]['c'] : 0;
  $usersRow = qSELECT("SELECT COUNT(*) AS c FROM users");
  $usersCount = ($usersRow && isset($usersRow[0]['c'])) ? (int)$usersRow[0]['c'] : 0;
} else {
  $agentsRow = qSELECT("SELECT COUNT(*) AS c FROM agents WHERE id=".$agentId);
  $agentsCount = ($agentsRow && isset($agentsRow[0]['c'])) ? (int)$agentsRow[0]['c'] : 0;
  $usersRow = qSELECT("SELECT COUNT(*) AS c FROM users WHERE agentid=".$agentId);
  $usersCount = ($usersRow && isset($usersRow[0]['c'])) ? (int)$usersRow[0]['c'] : 0;
}
?>
<?php if (!isset($isRootAdmin) || !$isRootAdmin): ?>
<style>
  /* Oculta cards avançados para agentes admin normais */
  .stats-grid { display: none !important; }
  .action-card[href^="/admin"],
  .action-card[href*="/admin"],
  .action-card[href*="admin/index.php"] { display: none !important; }
</style>
<?php endif; ?>
<!-- Página Home: conteúdo dentro do layout do header -->
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #212529;
            line-height: 1.6;
            min-height: 100vh;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .header-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #ffffff, #cccccc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-subtitle {
            font-size: 1.1rem;
            opacity: 0.8;
            font-weight: 300;
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
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #000000, #666666);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #000000, #333333);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #f1f3f5; /* texto mais claro */
            margin-bottom: 5px;
        }

        .stat-label {
            color: #ced4da; /* texto mais claro */
            font-size: 0.9rem;
            font-weight: 500;
        }

        .actions-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #000000;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #666666;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: #0f1113;
            border: 2px solid #262a2e;
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #e9ecef; /* texto mais claro */
            display: block;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
            transition: left 0.5s ease;
        }

        .action-card:hover {
            border-color: #000000;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover::before {
            left: 100%;
        }

        .action-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1c1f22, #2b2f34);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e9ecef;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .action-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #000000;
            margin-bottom: 10px;
        }

        .action-description {
            color: #666666;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Suporte WhatsApp extras */
        .support-badge {
            display: inline-block;
            background: #25D366; /* WhatsApp green */
            color: #fff;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: 10px;
            vertical-align: middle;
        }
        .support-hours {
            margin-top: 8px;
            font-size: 0.9rem;
            color: #495057;
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .quick-btn {
            background: linear-gradient(135deg, #000000, #333333);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-btn:hover {
            background: linear-gradient(135deg, #333333, #555555);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .search-bar {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: border-color 0.3s ease;
        }

        .search-bar:focus-within {
            border-color: #000000;
        }

        .search-bar input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 1rem;
            background: transparent;
        }

        .search-bar i {
            color: #666666;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }

            .header-title {
                font-size: 2rem;
            }

            .header-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .logout-btn {
                align-self: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                flex-direction: column;
            }
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #000000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="admin-container fade-in">
        <!-- Header Section -->
        <div class="header-section">
            <a href="hub.php" class="back-hub-btn"><i class="fas fa-arrow-left"></i> Voltar para Seleção</a>
            <div class="header-content">
                <div>
                    <h1 class="header-title">
                        <i class="fas fa-cogs"></i>
                        Stell Games — API de Jogos
                    </h1>
                    <p class="header-subtitle">Gerencie jogos, agentes e usuários com uma experiência moderna</p>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </div>

        <style>
          body { background: linear-gradient(135deg, #0f1115 0%, #0b0f16 100%); color:#e5e7eb; }
          .stat-card { background:#0b0f16; border:1px solid #1f2937; }
          .stat-number { color:#ffffff; }
          .actions-section { background:#0b0f16; border:1px solid #1f2937; }
          .action-card { background:#0f1115; border:1px solid #1f2937; }
          .action-card:hover { border-color:#2563eb; box-shadow:0 10px 25px rgba(0,0,0,0.35); }
          .action-icon { background:#111827; border:1px solid #1f2937; }
          .action-title { color:#ffffff; font-weight:700; }
          .action-title { color:#f3f4f6; font-weight:600; }
          .action-description { color:#e5e7eb; }
          .section-title { color:#f8fafc; }
          .action-illustration { height:140px; background:#111827; border:1px solid #1f2937; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:64px; margin-top:8px; }
          
          .back-hub-btn {
              display: inline-flex; align-items: center; gap: 8px;
              background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8);
              padding: 8px 16px; border-radius: 6px; text-decoration: none;
              font-size: 0.9rem; margin-bottom: 10px;
              position: relative; z-index: 2;
          }
          .back-hub-btn:hover { background: rgba(255,255,255,0.2); color: white; text-decoration: none; }
        </style>

        <div class="stats-grid" style="margin-top:20px;">
            <div class="stat-card">
                <div class="stat-icon"><i class="glyphicon glyphicon-console"></i></div>
                <div class="stat-number"><?= $gamesCount ?></div>
                <div class="stat-label">Jogos ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="glyphicon glyphicon-user"></i></div>
                <div class="stat-number"><?= $agentsCount ?></div>
                <div class="stat-label">Agentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="glyphicon glyphicon-list"></i></div>
                <div class="stat-number"><?= $usersCount ?></div>
                <div class="stat-label">Usuários</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="glyphicon glyphicon-upload"></i></div>
                <div class="stat-number">—</div>
                <div class="stat-label">Uploads hoje</div>
            </div>
        </div>

        <!-- Main Actions Section -->
        <div class="actions-section">
            <h2 class="section-title">
                <i class="fas fa-tasks"></i>
                Ações Principais
            </h2>
            
            <div class="actions-grid">
                <a href="users.php" class="action-card">
                    <div class="action-icon" style="background:#111827;border:1px solid #1f2937">
                        <i class="glyphicon glyphicon-user" style="opacity:0.95"></i>
                    </div>
                    <div class="action-illustration"><i class="glyphicon glyphicon-user"></i></div>
                    <div class="action-title">Gerenciar Usuários</div>
                    <div class="action-description">
                        Edite, adicione ou remova usuários do sistema. Gerencie permissões e configurações de acesso.
                    </div>
                </a>

                <a href="agents.php" class="action-card">
                    <div class="action-icon" style="background:#111827;border:1px solid #1f2937">
                        <i class="glyphicon glyphicon-cog" style="opacity:0.95"></i>
                    </div>
                    <div class="action-illustration"><i class="glyphicon glyphicon-cog"></i></div>
                    <div class="action-title">Configurar Agente + RTP</div>
                    <div class="action-description">
                        Personalize e configure seus agentes de IA. Ajuste comportamentos e parâmetros de funcionamento.
                    </div>
                </a>
                <a href="jogos.php" class="action-card">
                    <div class="action-icon" style="background:#111827;border:1px solid #1f2937">
                        <i class="glyphicon glyphicon-console" style="opacity:0.95"></i>
                    </div>
                    <div class="action-illustration"><i class="glyphicon glyphicon-console"></i></div>
                    <div class="action-title">Jogos Disponíveis</div>
                    <div class="action-description">
                        Consulte nomes e IDs dos jogos e visualize as imagens futuramente.
                    </div>
                </a>
                <a href="api-docs.php" class="action-card">
                    <div class="action-icon" style="background:#111827;border:1px solid #1f2937">
                        <i class="glyphicon glyphicon-book" style="opacity:0.95"></i>
                    </div>
                    <div class="action-illustration"><i class="glyphicon glyphicon-book"></i></div>
                    <div class="action-title">Documentação API</div>
                    <div class="action-description">
                        Acesse a documentação completa da API para integrar os jogos ao seu cassino.
                    </div>
                </a>
                
                <a href="https://api.whatsapp.com/send/?phone=5511982316892" target="_blank" class="action-card">
                    <div class="action-icon" style="background:#111827;border:1px solid #1f2937">
                        <i class="glyphicon glyphicon-comment" style="opacity:0.95"></i>
                    </div>
                    <div class="action-illustration"><i class="glyphicon glyphicon-comment"></i></div>
                    <div class="action-title">Suporte WhatsApp <span class="support-badge">Resposta rápida</span></div>
                    <div class="action-description">
                        Precisa de ajuda? Clique e chame no WhatsApp para suporte.
                        <div class="support-hours">Atendimento: 08h às 18h (segunda a segunda)</div>
                    </div>
                </a>

                

            
            </div>

            <div style="margin-top:24px">
              <h2 class="section-title"><i class="fas fa-gamepad"></i> Últimos jogos</h2>
              <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
                <?php 
                $lastGames = array_slice($gamesArr, max(0, count($gamesArr) - 6));
                $lastGames = array_reverse($lastGames);
                foreach ($lastGames as $g):
                  $name = htmlspecialchars($g['name'] ?? 'Jogo');
                  $id = htmlspecialchars((string)($g['id'] ?? ''));
                  $img = isset($g['image']) && $g['image'] ? '<img src="'.htmlspecialchars($g['image']).'" alt="" style="width:100%;height:140px;object-fit:cover;border-radius:10px;margin-top:8px" onerror="this.style.display=\'none\'">' : '<div style="height:140px;border:2px dashed #1f2937;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#9ca3af;margin-top:8px">Sem imagem</div>';
                ?>
                  <a href="jogos.php" class="action-card" style="padding:16px">
                    <div class="action-icon" style="width:52px;height:52px"><i class="fas fa-gamepad"></i></div>
                    <div class="action-title" style="display:flex;justify-content:space-between;align-items:center"><?= $name ?> <span class="badge" style="background:#212529;color:#fff;padding:6px 10px;border-radius:8px">#<?= $id ?></span></div>
                    <?= $img ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Quick Actions -->
           

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const spinner = document.getElementById('loadingSpinner');
            
            if (searchTerm.length > 2) {
                spinner.style.display = 'block';
                
                // Simulate search delay
                setTimeout(() => {
                    spinner.style.display = 'none';
                    // Here you would implement actual search functionality
                    console.log('Searching for:', searchTerm);
                }, 500);
            } else {
                spinner.style.display = 'none';
            }
        });

        // Refresh statistics
        function refreshStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(stat => {
                stat.style.opacity = '0.5';
            });

            // Simulate data refresh
            setTimeout(() => {
                statNumbers.forEach(stat => {
                    stat.style.opacity = '1';
                });
                
                // Show success message
                showNotification('Dados atualizados com sucesso!', 'success');
            }, 1000);
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#17a2b8'};
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Add slide-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchInput').focus();
            }
        });

        // Welcome message
        window.addEventListener('load', function() {
            setTimeout(() => {
                showNotification('Bem-vindo ao Painel Administrativo!', 'success');
            }, 500);
        });
    </script>

<?php include "includes/footer.php"; ?>


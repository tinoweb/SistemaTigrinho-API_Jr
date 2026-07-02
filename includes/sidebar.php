<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    :root {
        --pg-color: #00b4db;
        --pp-color: #ff9900;
        --sidebar-width: 280px;
        --bg-dark: #09090b;
        --card-bg: #121214;
        --text-primary: #ffffff;
        --text-secondary: #a1a1aa;
        --hover-bg: rgba(255, 255, 255, 0.05);
    }

    /* Reset básico e tipografia */
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-dark);
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Sidebar Styling */
    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        background: #18181b;
        position: fixed;
        top: 0;
        left: 0;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
        padding: 20px 0;
        overflow-y: auto;
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 0 24px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 20px;
    }

    .brand {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(to right, #fff, #ccc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .menu-item {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu-btn {
        width: 100%;
        padding: 16px 24px;
        background: none;
        border: none;
        color: var(--text-secondary);
        text-align: left;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s ease;
    }

    .menu-btn:hover, .menu-btn.active {
        color: #fff;
        background: var(--hover-bg);
    }

    .menu-btn i.icon {
        width: 24px;
        margin-right: 10px;
        text-align: center;
    }

    .menu-btn .arrow {
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .menu-btn.active .arrow {
        transform: rotate(180deg);
    }

    /* Submenu Styling */
    .submenu {
        max-height: 0;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.2);
        transition: max-height 0.4s ease-out;
    }

    .submenu.open {
        max-height: 500px;
    }

    .submenu a {
        display: block;
        padding: 12px 24px 12px 58px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .submenu a:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.03);
        border-left-color: rgba(255, 255, 255, 0.5);
    }

    .submenu-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #52525b;
        padding: 20px 24px 10px;
        font-weight: 700;
    }

    /* Content Area Adjustment */
    .main-content {
        margin-left: var(--sidebar-width);
        padding: 40px;
        min-height: 100vh;
        background-color: var(--bg-dark);
        transition: margin-left 0.3s;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
        .sidebar.show { transform: translateX(0); }
        .main-content { margin-left: 0; padding: 20px; padding-top: 60px; /* Space for toggle button */ }
        .mobile-toggle { display: block !important; position: fixed; top: 15px; left: 15px; z-index: 2000; background: #212529; color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
    }
    .mobile-toggle { display: none; }
</style>

<button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">
    <i class="fa-solid fa-bars"></i>
</button>

<div class="sidebar">
    <div class="sidebar-header">
        <a href="hub.php" class="brand">
            <i class="fa-solid fa-gamepad"></i> Stell Games
        </a>
    </div>

    <!-- PG Soft Menu -->
    <div class="menu-item">
        <button class="menu-btn" onclick="toggleMenu('pg-menu', this)">
            <span><i class="fa-solid fa-bolt icon" style="color: var(--pg-color);"></i> PG Soft (API 90)</span>
            <i class="fa-solid fa-chevron-down arrow"></i>
        </button>
        <div id="pg-menu" class="submenu">
            <a href="users.php">
                <i class="fa-solid fa-users" style="margin-right: 8px;"></i> Gerenciar Usuários
            </a>
            <a href="agents.php">
                <i class="fa-solid fa-robot" style="margin-right: 8px;"></i> Configurar Agente + RTP
            </a>
            <a href="jogos.php">
                <i class="fa-solid fa-gamepad" style="margin-right: 8px;"></i> Jogos Disponíveis
            </a>
            <a href="api-docs.php">
                <i class="fa-solid fa-book" style="margin-right: 8px;"></i> Documentação API
            </a>
            <a href="https://wa.me/5511999999999" target="_blank">
                <i class="fa-brands fa-whatsapp" style="margin-right: 8px;"></i> Suporte WhatsApp
            </a>
        </div>
    </div>

    <!-- Pragmatic Play Menu -->
    <div class="menu-item">
        <button class="menu-btn" onclick="toggleMenu('pp-menu', this)">
            <span><i class="fa-solid fa-gem icon" style="color: var(--pp-color);"></i> Pragmatic Play (PP)</span>
            <i class="fa-solid fa-chevron-down arrow"></i>
        </button>
        <div id="pp-menu" class="submenu">
            <a href="users-pp.php">
                <i class="fa-solid fa-users" style="margin-right: 8px;"></i> Gerenciar Usuários
            </a>
            <a href="agents-pp.php">
                <i class="fa-solid fa-user-tie" style="margin-right: 8px;"></i> Gerenciar Agentes
            </a>
            <a href="settings-pp.php">
                <i class="fa-solid fa-cogs" style="margin-right: 8px;"></i> Configurações & API
            </a>
        </div>
    </div>
    
    <div style="margin-top: auto; padding: 20px; font-size: 0.8rem; color: #52525b; text-align: center;">
        &copy; <?php echo date('Y'); ?> Stell Games
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Recuperar estado do menu
        const openMenu = localStorage.getItem('openMenu');
        if (openMenu) {
            const menu = document.getElementById(openMenu);
            const btn = document.querySelector(`button[onclick="toggleMenu('${openMenu}', this)"]`);
            if (menu && btn) {
                menu.classList.add('open');
                btn.classList.add('active');
            }
        }
        
        // Marcar link ativo
        const currentPath = window.location.pathname.split('/').pop();
        const links = document.querySelectorAll('.submenu a');
        links.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.style.color = '#fff';
                link.style.background = 'rgba(255, 255, 255, 0.05)';
                link.style.borderLeftColor = '#fff';
            }
        });
    });

    function toggleMenu(menuId, btn) {
        const menu = document.getElementById(menuId);
        
        if (menu.classList.contains('open')) {
            menu.classList.remove('open');
            btn.classList.remove('active');
            localStorage.removeItem('openMenu');
        } else {
            // Opcional: fechar outros menus
            // document.querySelectorAll('.submenu').forEach(m => m.classList.remove('open'));
            // document.querySelectorAll('.menu-btn').forEach(b => b.classList.remove('active'));

            menu.classList.add('open');
            btn.classList.add('active');
            localStorage.setItem('openMenu', menuId);
        }
    }
</script>

<div class="main-content">

<?php
include "includes/header.php";
?>
    <!-- O estilo específico do Hub pode ser mínimo, já que o header traz a sidebar -->
    <style>
        .welcome-card {
            max-width: 800px;
            animation: fadeIn 0.8s ease-out;
            margin: 0 auto;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(to right, #fff, #a1a1aa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text {
            color: #a1a1aa;
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <!-- O main-content já está aberto pelo header.php/sidebar.php -->
    
    <div class="welcome-card">
        <h1 class="welcome-title">Bem-vindo ao Hub</h1>
        <p class="welcome-text">
            Selecione uma das integrações no menu lateral para começar.<br>
            Gerencie seus jogos, usuários e configurações em um só lugar.
        </p>
        <div style="display: flex; gap: 20px; justify-content: center;">
            <div style="padding: 20px; background: rgba(0, 180, 219, 0.1); border-radius: 12px; border: 1px solid rgba(0, 180, 219, 0.2);">
                <i class="fa-solid fa-bolt" style="font-size: 2rem; color: #00b4db; margin-bottom: 10px;"></i>
                <h3 style="color: #fff; margin: 0;">PG Soft</h3>
            </div>
            <div style="padding: 20px; background: rgba(255, 106, 0, 0.1); border-radius: 12px; border: 1px solid rgba(255, 106, 0, 0.2);">
                <i class="fa-solid fa-gem" style="font-size: 2rem; color: #ff9900; margin-bottom: 10px;"></i>
                <h3 style="color: #fff; margin: 0;">Pragmatic</h3>
            </div>
        </div>
    </div>

    <!-- O fechamento da div main-content e do body é implícito ou pode ser feito aqui se desejado -->
    </div> <!-- fecha main-content -->
</body>
</html>
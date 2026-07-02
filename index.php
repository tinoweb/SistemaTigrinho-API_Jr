<?php
	error_reporting(0);
	session_start();
	if ($_COOKIE['auth'] == "admin_in"){header("location:"."home.php");}
    
    include_once 'includes/connect.php';
    include_once 'includes/data.php';
    
    $siteLogo = getSetting('site_logo');
    if (empty($siteLogo)) {
        $siteLogo = 'https://i.ibb.co/7tMYpYRg/GYPSY.png';
    }
    
    $siteFavicon = getSetting('site_favicon');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="@housamz">
    <meta name="description" content="Mass Admin Panel">
    <title>Acesso Restrito - Gamer Login</title>
    
    <?php if (!empty($siteFavicon)): ?>
        <link rel="icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
        <link rel="shortcut icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
    <?php endif; ?>
    
    <!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<style>
		:root {
			--primary-color: #6a00ff;
			--secondary-color: #00e5ff;
			--bg-dark: #050505;
			--card-bg: rgba(20, 20, 30, 0.85);
			--text-main: #ffffff;
			--text-muted: #8b9bb4;
		}

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(106, 0, 255, 0.2) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(0, 229, 255, 0.2) 0%, transparent 40%),
                linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                url('https://images.unsplash.com/photo-1542751371-adc38448a05e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        
            overflow-y: auto;   /* rolagem liberada */
            padding: 40px 0;
        }
		
		/* Grid overlay effect */
		body::before {
			content: "";
			position: absolute;
			top: 0; left: 0; width: 100%; height: 100%;
			background: 
				linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
				linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
			background-size: 100% 2px, 3px 100%;
			pointer-events: none;
			z-index: 0;
		}

		.login-container {
			background: var(--card-bg);
			backdrop-filter: blur(15px);
			-webkit-backdrop-filter: blur(15px);
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-top: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 16px;
			padding: 3rem 2.5rem;
			width: 100%;
			max-width: 420px;
			box-shadow: 
				0 0 40px rgba(0, 0, 0, 0.6),
				0 0 0 1px rgba(255, 255, 255, 0.05),
				inset 0 0 20px rgba(0, 0, 0, 0.5);
			position: relative;
			z-index: 1;
			transform: translateY(0);
			animation: cardEntrance 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
		}
		
		/* Neon border effect */
		.login-container::after {
			content: '';
			position: absolute;
			inset: -2px;
			border-radius: 18px;
			padding: 2px;
			background: linear-gradient(45deg, var(--primary-color), transparent, var(--secondary-color));
			-webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
			-webkit-mask-composite: xor;
			mask-composite: exclude;
			pointer-events: none;
			opacity: 0.7;
		}

		@keyframes cardEntrance {
			from { opacity: 0; transform: translateY(30px) scale(0.95); }
			to { opacity: 1; transform: translateY(0) scale(1); }
		}

		.logo-container {
			text-align: center;
			margin-bottom: 2rem;
			position: relative;
		}

		.logo {
			max-width: 140px;
			height: auto;
			filter: drop-shadow(0 0 15px rgba(106, 0, 255, 0.6));
			transition: transform 0.3s ease;
		}
		
		.logo:hover {
			transform: scale(1.05);
			filter: drop-shadow(0 0 25px rgba(0, 229, 255, 0.8));
		}

		.brand-title {
			font-family: 'Orbitron', sans-serif;
			font-size: 1.8rem;
			font-weight: 700;
			color: var(--text-main);
			margin-top: 1rem;
			margin-bottom: 0.2rem;
			text-transform: uppercase;
			letter-spacing: 2px;
			text-shadow: 0 0 10px rgba(0, 229, 255, 0.5);
		}

		.brand-subtitle {
			font-size: 0.9rem;
			color: var(--text-muted);
			font-weight: 500;
			letter-spacing: 1px;
			margin-bottom: 2rem;
		}

		.form-group {
			margin-bottom: 1.5rem;
			position: relative;
		}

		.form-label {
			display: block;
			font-size: 0.85rem;
			font-weight: 600;
			color: var(--secondary-color);
			margin-bottom: 0.5rem;
			letter-spacing: 1px;
			text-transform: uppercase;
		}

		.input-wrapper {
			position: relative;
		}

		.form-input {
			width: 100%;
			padding: 1rem 1rem 1rem 3rem;
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-radius: 8px;
			font-size: 1rem;
			font-family: 'Rajdhani', sans-serif;
			font-weight: 500;
			color: var(--text-main);
			background: rgba(0, 0, 0, 0.4);
			transition: all 0.3s ease;
			outline: none;
		}

		.form-input:focus {
			border-color: var(--secondary-color);
			box-shadow: 0 0 15px rgba(0, 229, 255, 0.2);
			background: rgba(0, 0, 0, 0.6);
		}

		.form-input::placeholder {
			color: rgba(255, 255, 255, 0.3);
		}

		.input-icon {
			position: absolute;
			left: 1rem;
			top: 50%;
			transform: translateY(-50%);
			color: var(--text-muted);
			font-size: 1.1rem;
			transition: color 0.3s ease;
			pointer-events: none;
		}

		.form-input:focus + .input-icon {
			color: var(--secondary-color);
		}

		.login-button {
			width: 100%;
			padding: 1rem;
			background: linear-gradient(90deg, #6a00ff, #aa00ff);
			color: white;
			border: none;
			border-radius: 8px;
			font-size: 1.1rem;
			font-family: 'Orbitron', sans-serif;
			font-weight: 700;
			letter-spacing: 2px;
			text-transform: uppercase;
			cursor: pointer;
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
			margin-top: 1.5rem;
			box-shadow: 0 4px 15px rgba(106, 0, 255, 0.4);
		}

		.login-button:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(106, 0, 255, 0.6);
			background: linear-gradient(90deg, #5500cc, #9900cc);
		}
        .register-button {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 12px;
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-decoration: none;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .register-button:hover {
            background: linear-gradient(90deg, #00e5ff, #6a00ff);
            color: #fff;
            box-shadow: 0 0 15px rgba(0,229,255,0.6);
            transform: translateY(-2px);
        }
		
		.login-button::after {
			content: '';
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: linear-gradient(rgba(255,255,255,0.2), transparent);
			transform: rotate(45deg);
			transition: 0.5s;
			opacity: 0;
		}
		
		.login-button:hover::after {
			opacity: 1;
			left: 100%;
		}

		.footer-text {
			text-align: center;
			margin-top: 2rem;
			font-size: 0.85rem;
			color: var(--text-muted);
			border-top: 1px solid rgba(255,255,255,0.05);
			padding-top: 1.5rem;
		}

		.footer-text a {
			color: var(--secondary-color);
			text-decoration: none;
			font-weight: 600;
			transition: color 0.3s;
		}

		.footer-text a:hover {
			color: #fff;
			text-shadow: 0 0 8px var(--secondary-color);
		}
		
		/* Loading spinner */
		.loading {
			display: none;
			width: 18px;
			height: 18px;
			border: 2px solid rgba(255, 255, 255, 0.3);
			border-radius: 50%;
			border-top-color: white;
			animation: spin 1s linear infinite;
			margin-right: 8px;
			vertical-align: middle;
		}

		@keyframes spin {
			to { transform: rotate(360deg); }
		}
		
		.login-button.loading .loading {
			display: inline-block;
		}

	</style>
</head>

<body>
	<div class="login-container">
		<div class="logo-container">
			<img src="<?php echo $siteLogo; ?>" alt="Logo" class="logo">
			<div class="brand-title">Acesso Restrito</div>
			<div class="brand-subtitle">Identifique-se para continuar</div>
		</div>

		<form action="login.php" method="post" name="login" id="loginForm">
			<div class="form-group">
				<label class="form-label" for="agentCode">ID do Usuário</label>
				<div class="input-wrapper">
					<input type="text" 
						   class="form-input" 
						   id="agentCode"
						   name="agentCode" 
						   placeholder="Digite seu ID" 
						   required
						   autocomplete="off">
					<i class="fa-solid fa-user-astronaut input-icon"></i>
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="senha">Senha de Acesso</label>
				<div class="input-wrapper">
					<input type="password" 
						   class="form-input" 
						   id="senha"
						   name="senha" 
						   placeholder="Digite sua senha" 
						   required>
					<i class="fa-solid fa-lock input-icon"></i>
				</div>
			</div>

			<button type="submit" class="login-button" id="submitBtn">
				<span class="loading"></span>
				<span class="button-text">ENTRAR</span>
			</button>
			<a href="criar-conta.php" class="register-button">CRIAR CONTA</a>
		</form>

		<div class="footer-text">
			&copy; <?php echo date('Y'); ?> Stell Games. All rights reserved.<br>
			<a href="https://api.whatsapp.com/send/?phone=5511982316892&text&type=phone_number&app_absent=0">Precisa de ajuda?</a>
			<span class="separator">•</span>
			<a href="api-docs.php" class="docs-link"><i class="fa-solid fa-book"></i> Documentação API</a>
		</div>
	</div>

	<style>
		.separator { margin: 0 10px; color: var(--text-muted); opacity: 0.5; }
		.docs-link { color: var(--secondary-color); text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
		.docs-link:hover { color: var(--primary-color); text-shadow: 0 0 8px rgba(106, 0, 255, 0.4); }
	</style>

	<script>
		document.getElementById('loginForm').addEventListener('submit', function(e) {
			var btn = document.getElementById('submitBtn');
			btn.classList.add('loading');
			// Allow form submission to proceed
		});
	</script>
</body>
</html>

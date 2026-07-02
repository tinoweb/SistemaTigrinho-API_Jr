<?php
	error_reporting(0);
	session_start();
	if ($_COOKIE['auth'] == "admin_in"){header("location:"."home.php");}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
	<title>PAINEL-PG</title>
	    <style>
        /* Ajustes para que a imagem se adapte à tela */
        .left-login img {
            width: 100%;
            height: auto;
        }
        /* Aumenta o tamanho da fonte para o título e ajusta a cor */
        .card-login h1 {
            font-size: 24px; /* Tamanho maior para o título */
            color: #fff; /* Cor das letras para branco */
        }
        /* Ajustes para o tamanho das letras do formulário e a cor */
        .card-login .textfield label, .card-login .textfield input, .btn-login {
            font-size: 16px;
            color: #FFFFFF; /* Cor das letras para branco */
        }
        /* Ajusta a cor do fundo do botão para manter a legibilidade do texto branco */
        .btn-login {
            background-color: #FF69B4; /* Fundo escuro para o botão */
            border: none;
            color: #000;
        }
        /* Estilo para as informações adicionais abaixo do botão */
        .additional-info {
            margin-top: 20px;
            font-size: 14px; /* Tamanho menor para as informações adicionais */
            text-align: center;
            color: #FFFFFF; /* Cor das letras para branco */
        }
        .additional-info a {
            color: #FF69B4; /* Cor dos links para pink */
        }
        /* Estilo geral para garantir que o texto seja branco */
        body {
            color: #FFFFFF; /* Cor do texto para branco */
            background-color: #333; /* Fundo escuro */
        }
        /* Estiliza os inputs para melhor visualização */
        .card-login .textfield input {
            background-color: #555; /* Fundo mais claro para os inputs */
            border: 1px solid #777;
            color: #FFFFFF; /* Texto branco nos inputs */
        }
    </style>

	<!-- Latest compiled and minified CSS -->
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"> -->

	<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body>
    <div class="main-login">
        <div class="left-login">
            <img src="https://s13.gifyu.com/images/SJwoa.png" class="left-login-image" alt="imagem animada">
        </div>
        <div class="right-login">
            <div class="card-login">
                <h1>Login</h1>
                <form action="login.php" method="post" name="login">
                    <div class="textfield">
                        <label for="agentCode">Agent Code</label>
                        <input type="text" name="agentCode" placeholder="Digite seu Agent Code" required>
                    </div>
                    <div class="textfield">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" placeholder="Digite sua senha" required>
                    </div>
                    <?php
                    if (isset($erro)) {
                        echo '<div class="error-message">' . $erro . '</div>';
                    }
                    ?>
                    <button class="btn-login">Acessar</button>
                    <!-- Informações adicionais logo abaixo do botão Acessar -->
                    <div class="additional-info">
                        ACESSE <a href="https://candybrasill.online/"></a> - games solucoes dev<br>
                        Copyright &copy;<br>
                        <p>Desenvolvedor by <a href="https://t.me/gamesolucoes">GAMESOLUCOES</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .main-login {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #000;
        }
        .left-login {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .left-login-image {
            max-width: 90%;
            height: auto;
        }
        .right-login {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-login {
            background-color: #000;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }
        .textfield {
            margin-bottom: 20px;
        }
        .textfield label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .textfield input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .additional-info {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
        }
        .additional-info a {
            color: #007bff;
            text-decoration: none;
        }
        .additional-info a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-bottom: 10px;
        }
    </style>
</body>



</html>
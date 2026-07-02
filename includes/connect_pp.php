<?php
$db_host = getenv('DB_HOST') ?: "localhost";
$db_user_pp = getenv('DB_USERNAME_PP') ?: "apipp";
$db_pass_pp = getenv('DB_PASSWORD_PP') ?: "13211321";
$db_name_pp = getenv('DB_NAME_PP') ?: "apipp";
$db_port = getenv('DB_PORT') ?: "3306";

$link_pp = mysqli_connect($db_host, $db_user_pp, $db_pass_pp, $db_name_pp, $db_port);
if (!$link_pp) {
    die("Erro ao conectar ao banco PP: " . mysqli_connect_error());
}
mysqli_set_charset($link_pp, "utf8");

// Identificação do Usuário Logado no Contexto PP
// 1. Pega o ID do cookie (que é do banco api90)
$loggedAdminId = isset($_COOKIE['admin_id']) ? intval($_COOKIE['admin_id']) : 0;

$myAgentIdPP = 0;
$myAgentCodePP = '';
$myAgentRolePP = '';

if ($loggedAdminId > 0) {
    // 2. Conecta ao banco api90 temporariamente para pegar o agentCode
    // Nota: Como o script pode já ter incluído connect.php, usamos a variável global $link se disponível
    // Se não, criamos uma conexão rápida.
    
    $db_user_90 = getenv('DB_USERNAME') ?: "api90";
    $db_pass_90 = getenv('DB_PASSWORD') ?: "13211321";
    $db_name_90 = getenv('DB_NAME') ?: "api90";
    
    $link_90_temp = mysqli_connect($db_host, $db_user_90, $db_pass_90, $db_name_90, $db_port);
    if ($link_90_temp) {
        $qCode = mysqli_query($link_90_temp, "SELECT agentCode FROM agents WHERE id = $loggedAdminId LIMIT 1");
        if ($qCode && mysqli_num_rows($qCode) > 0) {
            $rCode = mysqli_fetch_assoc($qCode);
            $myAgentCodePP = $rCode['agentCode'];
        }
        mysqli_close($link_90_temp);
    }

    // 3. Com o agentCode, busca os dados dele no banco apipp
    if (!empty($myAgentCodePP)) {
        $escapedCode = mysqli_real_escape_string($link_pp, $myAgentCodePP);
        $qPP = mysqli_query($link_pp, "SELECT id, agentCode, role, agentType FROM agents WHERE agentCode = '$escapedCode' LIMIT 1");
        if ($qPP && mysqli_num_rows($qPP) > 0) {
            $rPP = mysqli_fetch_assoc($qPP);
            $myAgentIdPP = $rPP['id'];
            // Se precisar de mais dados globais do usuário PP, adicione aqui
        }
    }
}
?>

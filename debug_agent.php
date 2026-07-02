<?php
include "includes/connect.php"; // Tentar conexão padrão primeiro
// Se falhar, tentar connect_pp
if (!isset($link)) {
    include "includes/connect_pp.php";
    $link = $link_pp;
}

$agentCode = 'trevo10';
$query = mysqli_query($link, "SELECT * FROM agents WHERE agentCode = '$agentCode'");
if ($query) {
    $agent = mysqli_fetch_assoc($query);
    print_r($agent);
} else {
    echo "Agente não encontrado ou erro na query: " . mysqli_error($link);
}
?>
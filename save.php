<?php
include("includes/connect.php");

// Captura segura dos parâmetros
$cat = $_POST['cat'] ?? ($_GET['cat'] ?? null);
$act = $_POST['act'] ?? ($_GET['act'] ?? null);
$id  = $_POST['id'] ?? ($_GET['id'] ?? null);

if ($cat === "users") {
    // Sanitização básica
    $username      = isset($_POST["username"]) ? addslashes(htmlentities($_POST["username"], ENT_QUOTES)) : '';
    $token         = $_POST["token"]         ?? '';
    $atk           = $_POST["atk"]           ?? '';
    $saldo         = isset($_POST["saldo"]) ? floatval(str_replace(",", ".", $_POST["saldo"])) : 0;
    $valorapostado = isset($_POST["valorapostado"]) ? floatval(str_replace(",", ".", $_POST["valorapostado"])) : 0;
    $valordebitado = isset($_POST["valordebitado"]) ? floatval(str_replace(",", ".", $_POST["valordebitado"])) : 0;
    $valorganho    = isset($_POST["valorganho"]) ? floatval(str_replace(",", ".", $_POST["valorganho"])) : 0;
    $rtp           = isset($_POST["rtp"]) ? floatval(str_replace(",", ".", $_POST["rtp"])) : 0;
    $isinfluencer  = $_POST["isinfluencer"] ?? '';
    $agentid       = $_POST["agentid"]      ?? '';

    if ($act === "add") {
        mysqli_query($link, "
            INSERT INTO `users` 
            (`username`, `token`, `atk`, `saldo`, `valorapostado`, `valordebitado`, `valorganho`, `rtp`, `isinfluencer`, `agentid`)
            VALUES (
                '$username',
                '$token',
                '$atk',
                '$saldo',
                '$valorapostado',
                '$valordebitado',
                '$valorganho',
                '$rtp',
                '$isinfluencer',
                '$agentid'
            )
        ");
    } elseif ($act === "edit" && $id) {
        mysqli_query($link, "
            UPDATE `users` SET  
                `username` = '$username',
                `token` = '$token',
                `atk` = '$atk',
                `saldo` = '$saldo',
                `valorapostado` = '$valorapostado',
                `valordebitado` = '$valordebitado',
                `valorganho` = '$valorganho',
                `rtp` = '$rtp',
                `isinfluencer` = '$isinfluencer',
                `agentid` = '$agentid'
            WHERE `id` = '$id'
        ");
    } elseif ($act === "delete" && $id) {
        mysqli_query($link, "DELETE FROM `users` WHERE id = '$id' ");
    }

    header("Location: users.php");
    exit;
}
?>

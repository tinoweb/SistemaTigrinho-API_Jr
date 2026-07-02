<?php
    include("includes/connect.php");

    $admin_agentCode = mysqli_real_escape_string($link, $_POST['agentCode']);
    $admin_senha = mysqli_real_escape_string($link, $_POST['senha']);
    $auth = 'admin_in';

    $query = mysqli_query($link, "SELECT * FROM agents WHERE agentCode = '".$admin_agentCode."' AND senha = '".$admin_senha."'");
    if (!$query) {
        header("Location: index.php");
        exit;
    }

    if (mysqli_num_rows($query) === 0) {
        header("Location: index.php");
        exit;
    } else {
        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
        // Garantir que os cookies funcionem em todo o site
        setcookie("admin_id", $row["id"], 0, "/");
        setcookie("admin_pass", $admin_senha, 0, "/");
        setcookie("auth", $auth, 0, "/");
        header("Location: hub.php");
        exit;
    }
?>

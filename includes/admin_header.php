<?php
// Temporarily enable error display for admin root to diagnose blank page
ini_set('display_errors', '1');
error_reporting(E_ALL);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// Guard para admin raiz (não vinculado a agente)
if (empty($_SESSION['admin_root_auth']) || $_SESSION['admin_root_auth'] !== '1') {
    header('Location: /admin/login.php');
    exit;
}

include(__DIR__ . '/connect.php');
include(__DIR__ . '/data.php');

$siteFavicon = getSetting('site_favicon');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="@housamz">
  <title>Admin Raiz - Stell Games</title>
  <?php if (!empty($siteFavicon)): ?>
      <link rel="icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
      <link rel="shortcut icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
  <?php endif; ?>
  <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">
  <link rel="stylesheet" href="/includes/style.css">
  <link href="//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <div class="wrapper">
    <div id="content">
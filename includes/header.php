<?php
// Temporarily enable error display to diagnose blank screen
ini_set('display_errors', '1');
error_reporting(E_ALL);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Autenticação básica: considerar logado se existir admin_id e admin_pass
$isLogged = isset($_COOKIE['admin_id']) && isset($_COOKIE['admin_pass']);
$isAdmin = isset($_COOKIE['auth']) && $_COOKIE['auth'] === 'admin_in';
// Root admin flag (acesso via /admin)
$isRootAdmin = isset($_COOKIE['admin_root_auth']) && $_COOKIE['admin_root_auth'] !== '';

// Se não estiver logado, volta para login
if (!$isLogged) {
    header("Location: /");
    exit;
}

include_once(__DIR__ . "/connect.php");
include_once(__DIR__ . "/data.php");

$siteFavicon = getSetting('site_favicon');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="@housamz">

    <meta name="description" content="Mass Admin Panel">
    <title>Stell Games API</title>
    <?php if (!empty($siteFavicon)): ?>
        <link rel="icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
        <link rel="shortcut icon" href="<?php echo $siteFavicon; ?>" type="image/x-icon" />
    <?php endif; ?>
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-ARo7C8H8V3jVY9vVbH7c7a3Eo4wG3G3z5mF3BvQFfYVqFZ2Qv9u2w2W6CjFZK9kU5F3k5YpB2zv3gO9EJtQWw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/style.css">
    <link href="//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<?php include_once __DIR__ . "/sidebar.php"; ?>

<!-- Conteúdo legado envolvido, mas a sidebar.php já abre a div main-content -->
<!-- Para compatibilidade, podemos manter algumas divs se o CSS antigo depender delas, mas o sidebar.php sobrescreve o layout -->
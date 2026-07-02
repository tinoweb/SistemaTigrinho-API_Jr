<?php
$db_host = getenv('DB_HOST') ?: "localhost";
$db_user = getenv('DB_USERNAME') ?: "api90";
$db_pass = getenv('DB_PASSWORD') ?: "13211321";
$db_name = getenv('DB_NAME') ?: "api90";
$db_port = getenv('DB_PORT') ?: "3306";

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$link) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}
mysqli_query($link, "SET CHARACTER SET utf8");
?>
		
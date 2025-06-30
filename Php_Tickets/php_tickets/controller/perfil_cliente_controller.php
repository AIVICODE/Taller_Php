<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/ticket.php';
require_once __DIR__ . '/../model/evento.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit;
}
$conn = conectar();
$usuario_id=$_SESSION['usuario_id'];
list($user, $eventos) = Cliente::getInfoCliente($conn, $usuario_id);
desconectar($conn);
?>

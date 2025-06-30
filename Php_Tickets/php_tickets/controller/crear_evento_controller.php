<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/evento.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit;
}
// Solo organizador puede crear eventos
$conn = conectar();
$usuario_id = $_SESSION['usuario_id'];
if (!Evento::usuarioEsOrganizador($conn, $usuario_id)) {
    desconectar($conn);
    die('Acceso denegado. Solo organizadores pueden crear eventos.');
}
$categorias = Evento::obtenerCategorias($conn);
desconectar($conn);
?>

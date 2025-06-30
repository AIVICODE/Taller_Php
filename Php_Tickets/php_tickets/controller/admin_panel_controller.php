<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/admin.php';
session_start();

// Verificar que sea admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit;
}

$conn = conectar();
$mensaje = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $usuario_id = $_POST['usuario_id'];
    
    switch ($accion) {
        case 'aprobar':
            if (Admin::aprobarOrganizador($conn, $usuario_id)) {
                $mensaje = 'Organizador aprobado exitosamente.';
            } else {
                $mensaje = 'Error al aprobar organizador.';
            }
            break;
            
        case 'remover':
            if (Admin::removerOrganizador($conn, $usuario_id)) {
                $mensaje = 'Organizador desaprobado exitosamente. Ahora está pendiente de nueva aprobación.';
            } else {
                $mensaje = 'Error al desaprobar organizador.';
            }
            break;
    }
}

// Obtener datos
$usuariosPendientes = Admin::getOrganizadoresPendientes($conn);
$organizadoresAprobados = Admin::getOrganizadoresAprobados($conn);
$estadisticas = Admin::getEstadisticasGenerales($conn);

desconectar($conn);
?>

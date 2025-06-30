<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/organizador.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/admin.php';
session_start();
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['pass'];
    $conn = conectar();
    $usuario = Usuario::login($conn, $email, $pass);
    if ($usuario) {
        $usuario_id = $usuario->getId();
        
        // Verificar si es organizador pendiente de aprobación
        if (Organizador::esOrganizadorPendiente($conn, $usuario_id)) {
            // Aún crear la sesión pero redirigir a página de espera
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_nombre'] = $usuario->getNom();
            $_SESSION['usuario_email'] = $usuario->getEmail();
            $_SESSION['tipo_usuario'] = 'organizador_pendiente';
            
            header('Location: ../view/organizador_pendiente.php');
            exit;
        } else {
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_nombre'] = $usuario->getNom();
            $_SESSION['usuario_email'] = $usuario->getEmail();
            
            // Determinar tipo de usuario para la sesión
            if (Admin::esAdmin($conn, $usuario_id)) {
                $_SESSION['tipo_usuario'] = 'admin';
            } elseif (Organizador::esOrganizador($conn, $usuario_id)) {
                $_SESSION['tipo_usuario'] = 'organizador';
            } else {
                $_SESSION['tipo_usuario'] = 'cliente';
            }
            
            $mensaje = 'Login exitoso. Bienvenido, ' . $usuario->getNom() . '!';
            header('Location: ../view/dashboard.php');
            exit;
        }
    } else {
        $mensaje = 'Email o contraseña incorrectos.';
    }
    desconectar($conn);
}
?>

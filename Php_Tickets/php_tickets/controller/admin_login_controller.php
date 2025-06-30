<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/admin.php';
session_start();

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['pass'];
    
    $conn = conectar();
    $admin = Admin::loginAdmin($conn, $email, $pass);
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin->getId();
        $_SESSION['admin_nombre'] = $admin->getNom();
        $_SESSION['admin_email'] = $admin->getEmail();
        
        header('Location: ../admin/panel.php');
        exit;
    } else {
        $mensaje = 'Credenciales incorrectas o no tienes permisos de administrador.';
    }
    
    desconectar($conn);
}
?>

<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/organizador.php';

$mensaje = '';
$exito = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname']);
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $pass = $_POST['pass'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    $img = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img = 'uploads/profile_images/' . basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], __DIR__ . '/../uploads/profile_images/' . basename($_FILES['img']['name']));
    }
    
    $conn = conectar();
    $res = Usuario::existeUsuario($conn, $nickname, $email);
    if ($res) {
        $mensaje = 'El nickname o email ya existen.';
    } else {
        $fecha = date('Y-m-d H:i:s');
        $usuario = new Usuario();
        $id = $usuario->registrar($conn, $nombre, $email, $pass, $fecha, $img);
        if ($id) {
            if ($tipo_usuario === 'cliente') {
                Cliente::registrarCliente($conn, $id);
                $mensaje = 'Registro exitoso como Cliente. Ahora puedes iniciar sesión.';
            } else if ($tipo_usuario === 'organizador') {
                // Registrar como organizador pero sin aprobar
                Organizador::registrarOrganizador($conn, $id);
                $mensaje = 'Registro exitoso. Tu solicitud como Organizador está pendiente de aprobación por un administrador.';
            }
            $exito = true;
        } else {
            $mensaje = 'Error al registrar usuario.';
        }
    }
    desconectar($conn);
}
?>

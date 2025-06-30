<?php
require_once __DIR__ . '/../conection/sql.php';
session_start();

// Función para manejar errores
function handleError($message) {
    $_SESSION['upload_message'] = $message;
    $_SESSION['upload_status'] = 'error';
    header('Location: ../view/perfil_cliente.php?upload=error&message=' . urlencode($message));
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar si se ha subido un archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../uploads/profile_images/';
    
    // Verificar que la carpeta existe, si no, crearla
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Obtener información del archivo
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_name = $_FILES['profile_image']['name'];
    $file_size = $_FILES['profile_image']['size'];
    $file_type = $_FILES['profile_image']['type'];
      // Verificar el tipo de archivo
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
    if (!in_array($file_type, $allowed_types)) {
        handleError('Error: Solo se permiten archivos JPG, PNG, GIF o WebP.');
    }
    
    // Verificar el tamaño del archivo (máximo 2MB)
    $max_size = 2 * 1024 * 1024; // 2MB en bytes
    if ($file_size > $max_size) {
        handleError('Error: El tamaño máximo permitido es 2MB.');
    }
    
    // Generar un nombre único para el archivo para evitar colisiones
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = 'profile_' . $usuario_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_file_name;
    
    // Mover el archivo a la carpeta de destino
    if (move_uploaded_file($file_tmp, $upload_path)) {        // Conexión a la base de datos
        $conn = conectar();
        
        // Primero, obtener la imagen actual del usuario
        $sql_get_imagen = "SELECT imagen FROM Usuario WHERE id = ?";
        $stmt_get = $conn->prepare($sql_get_imagen);
        $stmt_get->bind_param("i", $usuario_id);
        $stmt_get->execute();
        $stmt_get->bind_result($imagen_actual);
        $stmt_get->fetch();
        $stmt_get->close();
        
        // Eliminar la imagen anterior si existe
        if (!empty($imagen_actual) && $imagen_actual != 'default_profile.jpg') {
            $ruta_imagen_anterior = $upload_dir . $imagen_actual;
            if (file_exists($ruta_imagen_anterior)) {
                unlink($ruta_imagen_anterior);
            }
        }
        
        // Actualizar la base de datos con la nueva ruta de la imagen
        $sql = "UPDATE Usuario SET imagen = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_file_name, $usuario_id);
        
        if ($stmt->execute()) {
            $_SESSION['upload_message'] = 'Imagen de perfil actualizada correctamente.';
            $_SESSION['upload_status'] = 'success';
        } else {
            $_SESSION['upload_message'] = 'Error al actualizar la imagen en la base de datos.';
            $_SESSION['upload_status'] = 'error';
        }
        
        $stmt->close();
        desconectar($conn);    } else {
        handleError('Error al subir la imagen. Por favor, inténtalo de nuevo.');
    }
} else {
    handleError('Error: No se seleccionó ninguna imagen o hubo un problema con la carga.');
}

// Redirigir de vuelta al perfil con el estado de la operación
header('Location: ../view/perfil_cliente.php?upload=' . $_SESSION['upload_status'] . '&message=' . urlencode($_SESSION['upload_message']));
exit;
?>

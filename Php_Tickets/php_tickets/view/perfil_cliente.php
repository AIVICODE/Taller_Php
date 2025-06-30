<?php
require_once __DIR__ . '/../controller/perfil_cliente_controller.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <link rel="stylesheet" href="../stylesheets/variables.css">
    <link rel="stylesheet" href="../stylesheets/dashboard/body.css">
    <link rel="stylesheet" href="../stylesheets/profile/profile.css?v=<?php echo time(); ?>">
    <script src="../stylesheets/js/profile.js?v=<?php echo time(); ?>"></script>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
</head>
<body>    <header>
        <?php include __DIR__ . '/components/navbar.php'; ?>
    </header><h2>Mi Perfil</h2>
      <div class="profile-container">
        <div class="profile-card">
            <!-- Sección de imagen de perfil -->
            <div class="profile-header">                <?php if (!empty($user->img)): ?>
                    <div class="profile-image-container">
                        <img src="../uploads/profile_images/<?php echo htmlspecialchars($user->img); ?>" alt="Imagen de perfil" onerror="this.src='../stylesheets/images/profile.svg'" class="profile-image" id="profile-image-preview">
                        <div class="image-overlay">
                            <label for="profile_image" class="camera-icon">
                                <img src="../stylesheets/images/upload-photo.svg" alt="Cambiar foto">
                            </label>
                        </div>
                    </div>                <?php else: ?>
                    <div class="profile-image-container">
                        <img src="../stylesheets/images/default-profile.svg" alt="Imagen de perfil por defecto" onerror="this.src='../stylesheets/images/profile.svg'" class="profile-image" id="profile-image-preview">
                        <div class="image-overlay">
                            <label for="profile_image" class="camera-icon">
                                <img src="../stylesheets/images/upload-photo.svg" alt="Cambiar foto">
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
              <!-- Sección de información personal -->
            <div class="profile-info">
                <div class="info-row">
                    <div class="info-label">Nombre</div>
                    <div class="info-value"><?php echo htmlspecialchars($user ? $user->nombre : ''); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($user ? $user->email : ''); ?></div>
                </div>
                <div class="info-row logout-row">
                    <a href="../controller/logout_controller.php" class="profile-logout-button">
                        <svg class="logout-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                            <path d="M16 17v-3H9v-4h7V7l5 5-5 5M14 2a2 2 0 012 2v2h-2V4H5v16h9v-2h2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V4a2 2 0 012-2h9z" fill="currentColor"/>
                        </svg>
                        Cerrar sesión
                    </a>
                </div>
            </div>
              <!-- Formulario invisible para cargar imagen -->
            <form action="../controller/upload_profile_image.php" method="post" enctype="multipart/form-data" id="profile-form" class="upload-form">
                <input type="file" name="profile_image" id="profile_image" accept="image/jpeg, image/png, image/gif, image/webp" class="hidden-file-input">
                <button type="submit" id="upload-button" class="upload-btn" style="display: none;">Guardar cambios</button>
                <?php if (isset($_GET['upload'])): ?>
                    <div class="<?php echo $_GET['upload'] == 'success' ? 'success-message' : 'error-message'; ?>">
                        <?php 
                        if ($_GET['upload'] == 'success') {
                            echo "Imagen de perfil actualizada correctamente";
                        } else {
                            echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : "Error al subir la imagen";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="button-container">
        <a href="dashboard.php" class="back-link">Volver al dashboard</a>
    </div>
</body>
</html>
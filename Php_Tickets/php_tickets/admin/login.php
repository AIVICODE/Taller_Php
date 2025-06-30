<?php require_once __DIR__ . '/../controller/admin_login_controller.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Iniciar Sesión</title>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <link rel="stylesheet" href="../stylesheets/admin/login.css">
</head>
<body>
    <div class="login-container">
        <div class="admin-header">
            <h1>🛡️ Admin Panel</h1>
            <p>Acceso restringido para administradores</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="pass">Contraseña:</label>
                <input type="password" id="pass" name="pass" required>
            </div>
            
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="../view/login.php">← Volver al login principal</a>
        </div>
    </div>
</body>
</html>

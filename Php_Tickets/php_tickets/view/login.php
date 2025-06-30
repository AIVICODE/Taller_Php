<?php
require_once __DIR__ . '/../controller/login_controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <link rel="stylesheet" href="../stylesheets/login/login.css">
</head>
<body>
    <div class="container">
        <!-- Lado izquierdo con logo -->
        <div class="left-side">
            <div class="logo-box">
                <a href="dashboard.php" class="logo-link">
                    <img src="../stylesheets/images/favicon.png" alt="Logo TicketLand" class="logo-img">
                    <span class="logo-text">TicketLand</span>
                </a>
            </div>
        </div>

        <!-- Lado derecho con formulario -->
        <div class="right-side">
            <div class="login-container">
                <h2>¡Bienvenido!</h2>
                <p class="alt-text">¿Aún no estás registrado? <a href="registro.php">Registrate</a></p>

                <?php if ($mensaje): ?>
                    <?php 
                    $esOrganizadorPendiente = strpos($mensaje, 'pendiente de aprobación') !== false;
                    $claseEstilo = $esOrganizadorPendiente ? 'warning' : 'error';
                    ?>
                    <p class="<?= $claseEstilo ?>"><?= htmlspecialchars($mensaje) ?></p>
                <?php endif; ?>

                <form method="post">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Exp: John.doe@gmail.com" required>

                        <label for="pass">Contraseña</label>
                        <div class="input-password">
                            <input type="password" id="pass" name="pass" placeholder="****" required>
                    
                        </div>
                        <small>Mínimo 8 caracteres</small>

                    <button type="submit">Iniciar Sesión</button>
                </form>

                <p class="terminos">
                    <span class="lock-icon">🔒</span>
                    Continuando acepto los <a href="#">términos y condiciones de TicketLandia</a>.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("pass");
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
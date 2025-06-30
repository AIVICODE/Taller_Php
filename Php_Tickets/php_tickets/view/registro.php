<?php
require_once __DIR__ . '/../controller/registro_controller.php';
?>
<?php
$mensaje = '';
$exito = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = "¡Registro exitoso!";
    $exito = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <link rel="stylesheet" href="../stylesheets/login/registro.css">
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
            <div class="registro-container">
                <h2>¡Bienvenido!</h2>

                <?php if ($mensaje): ?>
                    <p class="<?= $exito ? 'success' : 'error' ?>"><?= htmlspecialchars($mensaje) ?></p>
                <?php endif; ?>

                <?php if (!$exito): ?>
                    <form method="post" enctype="multipart/form-data">
                        <label for="nickname">Usuario</label>
                        <input type="text" id="nickname" name="nickname" placeholder="Exp: 0812345678" required>

                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Exp: John.doe@gmail.com" required>

                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Exp: John Doe" required>
                       
                        <label for="tipo_usuario">Tipo de Usuario</label>
                        <select id="tipo_usuario" name="tipo_usuario" required>
                            <option value="">Selecciona tu tipo de usuario</option>
                            <option value="cliente"> Cliente - Comprar tickets</option>
                            <option value="organizador"> Organizador - Crear eventos</option>
                        </select>
                        <small>ℹ️ Los organizadores requieren aprobación del administrador</small>

                        <label for="pass">Contraseña</label>
                        <input type="password" id="pass" name="pass" placeholder="****" required>
                        <small>Mínimo 8 caracteres</small>

                        <button type="submit">Registrate</button> 
                    </form>
                <?php endif; ?>

                <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoUsuarioSelect = document.getElementById('tipo_usuario');
            const form = tipoUsuarioSelect.closest('form');
            
            // Añadir efecto visual cuando se selecciona un tipo
            tipoUsuarioSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                
                // Remover clases anteriores
                this.classList.remove('cliente-selected', 'organizador-selected');
                
                // Añadir clase según selección
                if (selectedValue === 'cliente') {
                    this.classList.add('cliente-selected');
                } else if (selectedValue === 'organizador') {
                    this.classList.add('organizador-selected');
                }
                
                // Añadir animación sutil
                this.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });
    </script>
    
    <style>
        /* Estilos adicionales para las clases dinámicas */
        #tipo_usuario.cliente-selected {
            border-color: #28a745;
            background-color: #f8fff8;
        }
        
        #tipo_usuario.organizador-selected {
            border-color: #ffc107;
            background-color: #fffef8;
        }
        
        /* Transición suave para todos los cambios */
        #tipo_usuario {
            transition: all 0.3s ease, transform 0.15s ease;
        }
    </style>
</body>
</html>
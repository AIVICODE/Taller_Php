<link rel="stylesheet" href="../stylesheets/components/navbar.css?v=<?php echo time(); ?>">
<script src="../stylesheets/js/navbar.js"></script>

<?php
// Include required files for functions
require_once __DIR__ . '/../../controller/dashboard_controller.php';

// Skip session_start if a session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Permitir acceso sin sesi車n iniciada
$usuario_logueado = isset($_SESSION['usuario_id']);
$usuario_id = $usuario_logueado ? $_SESSION['usuario_id'] : null;
$esOrganizador = $usuario_logueado ? esOrganizador($usuario_id) : false;
$esCliente = $usuario_logueado ? esCliente($usuario_id) : false;
?>
<head><meta charset="UTF-8"></head>

<nav class="navbar">
    <div class="navbar-content">
        <div class="top-bar">
            <a href="dashboard.php"><img src="../stylesheets/images/icon.svg" alt="Ticketera Logo" class="logo-img" /></a>
            <div class="search-container">
                <form action="dashboard.php" method="get">
                    <span class="search-icon" aria-label="Buscar"></span>
                    <input type="text" name="buscar_evento" placeholder="Buscar eventos..." value="<?php echo isset($_GET['buscar_evento']) ? htmlspecialchars($_GET['buscar_evento']) : ''; ?>">
                </form>
                <button class="hamburger-menu" aria-label="Menu">
                    <span class="hamburger-icon"></span>
                </button>
            </div>
        </div>        <div class="actions">
            <?php if ($usuario_logueado): ?>
                <span class="welcome-message">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</span>                <a href="perfil_cliente.php" class="profile-link">
                    <img src="../stylesheets/images/profile.svg" alt="Perfil" class="profile-icon">
                    Mi Perfil
                </a>            <?php if ($esCliente): ?>
                <a href="mis_tickets.php">Mis tickets</a>
            <?php endif; ?>

            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a class="logout-btn" href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

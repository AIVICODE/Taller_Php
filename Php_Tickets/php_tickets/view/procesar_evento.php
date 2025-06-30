<?php
require_once __DIR__ . '/../controller/procesar_evento_controller.php';
?>
<?php include __DIR__ . '/components/navbar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesar Evento</title>
    <link rel="stylesheet" href="../stylesheets/perfil_cliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/procesar_evento.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container-perfil">
        <?php if ($exito): ?>
            <div class="evento-exito-msg">
                <span class="tick-icon">&#10003;</span>
                <span><strong>Evento</strong> creado correctamente</span>
            </div>
            <div class="perfil-botones">
                <a class="volver-btn" href="dashboard.php">Volver al dashboard</a>
            </div>
        <?php else: ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
            <div class="perfil-botones">
                <a class="volver-btn" href="crear_evento.php">Volver</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
require_once '../controller/dashboard_controller.php';
session_start();

// Permitir acceso sin sesión iniciada
$usuario_logueado = isset($_SESSION['usuario_id']);
$usuario_id = $usuario_logueado ? $_SESSION['usuario_id'] : null;
$esOrganizador = $usuario_logueado ? esOrganizador($usuario_id) : false;
$esCliente = $usuario_logueado ? esCliente($usuario_id) : false;


$buscar = isset($_GET['buscar_evento']) ? trim($_GET['buscar_evento']) : '';
if ($buscar !== '') {
    $categorias = buscarEventosPorTituloOCategoria($buscar);
} else {
    $categorias = obtenerCategoriasConEventos();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../stylesheets/variables.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/dashboard/body.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/dashboard/events.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <script src="../stylesheets/js/category-slider.js?v=<?php echo time(); ?>"></script>
</head>

<body> <?php if(isset($_SESSION['ticketComprado'])){
    if($_SESSION['ticketComprado'] == true){
        echo '<script>alert("Ticket comprado con exito, se le ha enviado un mail con la informacion de su compra")</script>';
        $_SESSION['ticketComprado'] = false;
    }
}
       ?>
        <header>
        <?php include __DIR__ . '/components/navbar.php'; ?>
    </header>
    
    <div class="categories-wrapper">
        <button class="scroll-btn scroll-left" id="scrollLeft" aria-label="Desplazar a la izquierda">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        
        <div class="categories-container" id="categoriesContainer">
            <?php foreach ($categorias as $cat): ?>
                <div class="category-item" onclick="toggleCategory('cat-<?php echo $cat->id; ?>')">
                    <h3><?php echo htmlspecialchars($cat->desc); ?></h3>
                    <p><?php echo count($cat->eventos); ?> eventos</p>
                </div>
            <?php endforeach; ?>
        </div>
          <button class="scroll-btn scroll-right" id="scrollRight" aria-label="Desplazar a la derecha">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
    
    <!-- Indicador visual de scroll -->
    <div class="scroll-indicator" id="scrollIndicator"></div>
      <div class="events-section">
        <?php foreach ($categorias as $cat): ?>            
            <div class="category-events" id="cat-<?php echo $cat->id; ?>">
                <h3 class="category-title">Categoría: <?php echo htmlspecialchars($cat->desc); ?></h3>                
                <?php if (count($cat->eventos) > 0): ?>
                    <div class="eventos-grid">
                    <?php foreach ($cat->eventos as $ev): ?>
                        <?php 
                            $isActive = strtotime($ev->fecha) > time();
                            $statusClass = $isActive ? 'active' : 'expired';
                            $statusIcon = $isActive ? 'bi-calendar-check' : 'bi-calendar-x';
                            
                            // Formatear la fecha del evento
                            $fechaEvento = date('d/m/Y H:i', strtotime($ev->fecha));
                        ?>
                        <div class="evento-card">
                            <div class="evento-header">
                                <h3><?php echo htmlspecialchars($ev->titulo); ?></h3>
                                <div class="evento-status <?php echo $statusClass; ?>">
                                    <i class="bi <?php echo $statusIcon; ?>"></i>
                                </div>
                            </div>
                              <div class="evento-body">                                <div class="evento-image">
                                    <?php 
                                    if (!empty($ev->imagen_url)) {
                                        // Corregir la ruta de la imagen si es necesario
                                        $imgPath = $ev->imagen_url;
                                        // Si la ruta comienza con "/", aseguramos que sea relativa a la raíz del sitio
                                        if (strpos($imgPath, '/') === 0) {
                                            $imgPath = '..' . $imgPath;
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Imagen del evento" class="evento-img">
                                    <?php
                                    } else {
                                        // No hay imagen, mostrar un placeholder
                                        ?>
                                        <div class="evento-img-placeholder">
                                            <i class="bi bi-image"></i>
                                            <span><?php echo htmlspecialchars($ev->titulo); ?></span>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="evento-info">
                                    <div class="evento-info-row">
                                        <div class="evento-info-label">Fecha:</div>
                                        <div class="evento-info-value"><?php echo $fechaEvento; ?></div>
                                    </div>
                                    <div class="evento-info-row">
                                        <div class="evento-info-label">Lugar:</div>
                                        <div class="evento-info-value"><?php echo htmlspecialchars($ev->lugar); ?></div>
                                    </div>
                                    <?php if(!empty($ev->desc)): ?>
                                    <div class="evento-info-row">
                                        <div class="evento-info-label">Descripción:</div>
                                        <div class="evento-info-value"><?php echo htmlspecialchars($ev->desc); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                              <div class="evento-footer">
                                <?php if (strtotime($ev->fecha) > time()): ?>
                                    <?php if ($esCliente): ?>
                                        <form action="comprar_ticket.php" method="get">
                                            <input type="hidden" name="evento_id" value="<?php echo $ev->id; ?>">
                                            <button type="submit" class="comprar-btn">Comprar tickets</button>
                                        </form>
                                    <?php elseif ($usuario_logueado && !$esCliente): ?>
                                        <div class="evento-info-message">Solo los clientes pueden comprar tickets</div>
                                    <?php else: ?>
                                        <div class="evento-info-message">
                                            <a href="login.php" style="color: var(--primary-color); text-decoration: none;">
                                                Inicia sesión para comprar tickets
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="evento-finalizado">Evento finalizado</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-eventos">
                        <div class="no-eventos-icon"><i class="bi bi-calendar-x"></i></div>
                        <p>No hay eventos para esta categoría.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>    </div>
      <?php if ($usuario_logueado && $esOrganizador): ?>
        <a href="../view/crear_evento.php" class="create-event-btn">Crear nuevo evento</a>    
    <?php elseif (!$usuario_logueado): ?>
        <div style="text-align: center; margin: 2rem 0;">
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                ¿Eres organizador de eventos?
            </p>
            <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                Inicia sesión para crear eventos
            </a>
        </div>
    <?php endif; ?>

    <script>
        // Función para animar los eventos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const eventos = document.querySelectorAll('.evento-card');
            eventos.forEach((evento, index) => {
                setTimeout(() => {
                    evento.style.opacity = '1';
                    evento.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>

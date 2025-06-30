<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/evento.php';
require_once __DIR__ . '/../model/ticket.php';
require_once __DIR__ . '/../model/pago.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$conn = conectar();
$cliente_id = $_SESSION['usuario_id'];

// Obtener tickets del usuario
$tickets = array();
$sql = "SELECT t.*, 
        e.titulo as evento_titulo, e.fecha as evento_fecha, e.lugar as evento_lugar, e.descripcion as evento_descripcion,
        p.metodoPago, p.estado as pago_estado, p.fechaPago, p.monto,
        (SELECT ei.url_imagen FROM EventoImagen ei WHERE ei.evento_id = e.id LIMIT 1) as evento_img
        FROM Ticket t 
        JOIN Evento e ON t.evento_id = e.id 
        JOIN Pago p ON t.pago_id = p.id 
        WHERE t.cliente_id = $cliente_id 
        ORDER BY t.fechaCompra DESC";

$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $tickets[] = $row;
}
desconectar($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets</title>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../stylesheets/variables.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/dashboard/body.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/mis_tickets.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <?php include __DIR__ . '/components/navbar.php'; ?>
    </header>

    <div class="tickets-container">
        <h1 class="tickets-title">Mis Tickets</h1>
        
        <?php if (empty($tickets)): ?>
            <div class="no-tickets">
                <div class="no-tickets-icon"><i class="bi bi-ticket-perforated"></i></div>
                <p>No has comprado ningún ticket todavía</p>
                <a href="dashboard.php" class="download-ticket">Explorar eventos</a>
            </div>
        <?php else: ?>
            <div class="tickets-grid">
                <?php foreach ($tickets as $ticket): ?>
                    <?php 
                        $isActive = strtotime($ticket['evento_fecha']) > time(); 
                        $statusClass = $isActive ? 'active' : 'expired';
                        $statusIcon = $isActive ? 'bi-calendar-check' : 'bi-calendar-x';
                        
                        // Formatear la fecha de compra
                        $fechaCompra = date('d/m/Y H:i', strtotime($ticket['fechaCompra']));
                        
                        // Formatear la fecha del evento
                        $fechaEvento = date('d/m/Y H:i', strtotime($ticket['evento_fecha']));
                    ?>
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <h3><?php echo htmlspecialchars($ticket['evento_titulo']); ?></h3>
                            <div class="ticket-date">Comprado el: <?php echo $fechaCompra; ?></div>
                            <div class="ticket-status <?php echo $statusClass; ?>">
                                <i class="bi <?php echo $statusIcon; ?>"></i>
                            </div>
                        </div>
                        
                        <div class="ticket-body">
                            <?php if (!empty($ticket['evento_img'])): ?>
                            <div class="ticket-image">
                                <?php 
                                // Corregir la ruta de la imagen si es necesario
                                $imgPath = $ticket['evento_img'];
                                // Si la ruta comienza con "/", aseguramos que sea relativa a la raíz del sitio
                                if (strpos($imgPath, '/') === 0) {
                                    $imgPath = '..' . $imgPath;
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Imagen del evento" class="ticket-event-image">
                            </div>
                            <?php endif; ?>
                            <div class="ticket-info">
                                <div class="ticket-info-row">
                                    <div class="ticket-info-label">Fecha:</div>
                                    <div class="ticket-info-value"><?php echo $fechaEvento; ?></div>
                                </div>
                                <div class="ticket-info-row">
                                    <div class="ticket-info-label">Lugar:</div>
                                    <div class="ticket-info-value"><?php echo htmlspecialchars($ticket['evento_lugar']); ?></div>
                                </div>
                                <div class="ticket-info-row">
                                    <div class="ticket-info-label">Método de pago:</div>
                                    <div class="ticket-info-value"><?php echo htmlspecialchars($ticket['metodoPago']); ?></div>
                                </div>
                                <div class="ticket-info-row">
                                    <div class="ticket-info-label">Estado del pago:</div>
                                    <div class="ticket-info-value">
                                        <?php if ($ticket['pago_estado'] === 'completado'): ?>
                                            <span style="color: green;">Completado</span>
                                        <?php elseif ($ticket['pago_estado'] === 'pendiente'): ?>
                                            <span style="color: orange;">Pendiente</span>
                                        <?php else: ?>
                                            <span style="color: red;">Fallido</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ticket-footer">
                            <div class="ticket-quantity">
                                <?php echo $ticket['cantidad']; ?> entrada<?php echo $ticket['cantidad'] > 1 ? 's' : ''; ?>
                            </div>
                            <div class="ticket-price">
                                Total: $<?php echo number_format($ticket['totalPago'], 2); ?>
                            </div>
                        </div>                        <div class="ticket-actions">
                            <a href="../pdf/generar_pdf.php?ticket_id=<?php echo $ticket['id']; ?>" target="_blank" class="download-ticket">
                                <i class="bi bi-file-pdf"></i> Descargar PDF
                            </a>
                            <a href="../pdf/generar_ticket_html.php?ticket_id=<?php echo $ticket['id']; ?>" target="_blank" class="view-ticket" style="display: block; margin-top: 10px; text-align: center; color: #1F5673; text-decoration: underline;">
                                <i class="bi bi-eye"></i> Ver en navegador
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Función para animar los tickets al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const tickets = document.querySelectorAll('.ticket-card');
            tickets.forEach((ticket, index) => {
                setTimeout(() => {
                    ticket.style.opacity = '1';
                    ticket.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>

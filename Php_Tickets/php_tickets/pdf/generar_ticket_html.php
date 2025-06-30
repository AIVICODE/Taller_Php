<?php
/**
 * Simple PDF Generator without external dependencies
 * Based on Basic PDF Generation techniques
 */

require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/evento.php';
require_once __DIR__ . '/../model/ticket.php';
require_once __DIR__ . '/../model/pago.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit;
}

if (!isset($_GET['ticket_id']) || empty($_GET['ticket_id'])) {
    die('ID del ticket no proporcionado');
}

$ticket_id = intval($_GET['ticket_id']);
$cliente_id = $_SESSION['usuario_id'];

$conn = conectar();

// Verificar que el ticket pertenezca al usuario logueado
$sql = "SELECT t.*, e.titulo as evento_titulo, e.fecha as evento_fecha, e.lugar as evento_lugar, 
        e.descripcion as evento_descripcion, p.metodoPago, p.estado as pago_estado, 
        p.fechaPago, p.monto, u.nombre as cliente_nombre, u.email as cliente_email,
        (SELECT ei.url_imagen FROM EventoImagen ei WHERE ei.evento_id = e.id LIMIT 1) as evento_img
        FROM Ticket t 
        JOIN Evento e ON t.evento_id = e.id 
        JOIN Pago p ON t.pago_id = p.id 
        JOIN Usuario u ON t.cliente_id = u.id
        WHERE t.id = $ticket_id AND t.cliente_id = $cliente_id";

$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    // Formatear la información para mostrarla en HTML
    $fechaEvento = date('d/m/Y H:i', strtotime($row['evento_fecha']));
    $fechaCompra = date('d/m/Y H:i', strtotime($row['fechaCompra']));
    
    // Generar un código aleatorio para simular un código de barras o QR
    $codigo = 'TKT-' . strtoupper(substr(md5($row['id'] . $row['cliente_id'] . $row['evento_titulo']), 0, 10));
    
    // Comenzar la generación del HTML
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket: <?php echo htmlspecialchars($row['evento_titulo']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .ticket-container {
            border: 2px solid #1F5673;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .ticket-header {
            background-color: #1F5673;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            position: relative;
        }
        .ticket-title {
            margin: 0;
            font-size: 24px;
        }
        .ticket-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .ticket-section-title {
            font-weight: bold;
            color: #1F5673;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .ticket-detail {
            display: flex;
            margin-bottom: 8px;
        }
        .ticket-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }
        .ticket-value {
            flex-grow: 1;
        }
        .code-container {
            background-color: #f9f9f9;
            text-align: center;
            padding: 15px;
            margin: 15px 0;
            border: 1px dashed #ccc;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .ticket-notes {
            font-style: italic;
            color: #666;
            text-align: center;
            padding: 15px 0;
            border-top: 1px solid #eee;
            font-size: 14px;
        }
        .event-image {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 20px auto;
            max-height: 250px;
        }
        .print-btn {
            background-color: #F5D250;
            color: #1F5673;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            display: block;
            margin: 30px auto;
            font-size: 16px;
        }
        .evento-status {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #F5D250;
            color: #232323;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        @media print {
            .print-btn {
                display: none;
            }
            body {
                padding: 0;
                max-width: 100%;
            }
            .ticket-container {
                border: 1px solid #ccc;
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1 class="ticket-title">TICKET DE EVENTO</h1>
            <div class="evento-status">
                <?php echo strtotime($row['evento_fecha']) > time() ? 'ACTIVO' : 'EXPIRADO'; ?>
            </div>
        </div>
        
        <h2 style="text-align: center; color: #1F5673;"><?php echo htmlspecialchars($row['evento_titulo']); ?></h2>
        
        <?php if (!empty($row['evento_img'])): ?>
            <?php 
            // Corregir la ruta de la imagen si es necesario
            $imgPath = $row['evento_img'];
            // Si la ruta comienza con "/", aseguramos que sea relativa a la raíz del sitio
            if (strpos($imgPath, '/') === 0) {
                $imgPath = '..' . $imgPath;
            }
            ?>
            <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Imagen del evento" class="event-image">
        <?php endif; ?>
        
        <div class="ticket-section">
            <div class="ticket-section-title">Detalles del evento</div>
            <div class="ticket-detail">
                <div class="ticket-label">Fecha:</div>
                <div class="ticket-value"><?php echo $fechaEvento; ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Lugar:</div>
                <div class="ticket-value"><?php echo htmlspecialchars($row['evento_lugar']); ?></div>
            </div>
            <?php if (!empty($row['evento_descripcion'])): ?>
            <div class="ticket-detail">
                <div class="ticket-label">Descripción:</div>
                <div class="ticket-value"><?php echo htmlspecialchars($row['evento_descripcion']); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="ticket-section">
            <div class="ticket-section-title">Datos del comprador</div>
            <div class="ticket-detail">
                <div class="ticket-label">Nombre:</div>
                <div class="ticket-value"><?php echo htmlspecialchars($row['cliente_nombre']); ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Email:</div>
                <div class="ticket-value"><?php echo htmlspecialchars($row['cliente_email']); ?></div>
            </div>
        </div>
        
        <div class="ticket-section">
            <div class="ticket-section-title">Detalles del ticket</div>
            <div class="ticket-detail">
                <div class="ticket-label">Número de ticket:</div>
                <div class="ticket-value"><?php echo $row['id']; ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Cantidad de entradas:</div>
                <div class="ticket-value"><?php echo $row['cantidad']; ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Fecha de compra:</div>
                <div class="ticket-value"><?php echo $fechaCompra; ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Método de pago:</div>
                <div class="ticket-value"><?php echo htmlspecialchars($row['metodoPago']); ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Estado del pago:</div>
                <div class="ticket-value"><?php echo htmlspecialchars($row['pago_estado']); ?></div>
            </div>
            <div class="ticket-detail">
                <div class="ticket-label">Total pagado:</div>
                <div class="ticket-value">$<?php echo number_format($row['totalPago'], 2); ?></div>
            </div>
        </div>
        
        <div class="code-container">
            <div class="ticket-section-title">Código de validación</div>
            <div class="barcode"><?php echo $codigo; ?></div>
            <div style="margin-top: 15px; border: 2px solid #333; height: 80px; width: 200px; margin: 15px auto; display: flex; align-items: center; justify-content: center;">
                QR Code / Barcode
            </div>
        </div>
        
        <div class="ticket-notes">
            <p>Este ticket es válido únicamente para la persona que realizó la compra.</p>
            <p>Presenta este documento al ingresar al evento.</p>
        </div>
    </div>
    
    <button class="print-btn" onclick="window.print()">Imprimir Ticket</button>
    
    <script>
        // Auto print para facilitar la descarga como PDF
        document.addEventListener('DOMContentLoaded', function() {
            // Dar un breve tiempo para que se carguen las imágenes antes de imprimir
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>
<?php
} else {
    echo "No se encontró el ticket solicitado o no tienes permisos para acceder a él.";
}

desconectar($conn);
?>

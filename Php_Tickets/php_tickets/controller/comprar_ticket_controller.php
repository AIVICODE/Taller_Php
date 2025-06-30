<?php
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/evento.php';
require_once __DIR__ . '/../model/ticket.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/usuario.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit;
}
$conn = conectar();
$msg = '';
$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
$evento = null;
if ($evento_id) {
    $evento = Evento::getEventoDisponible($conn, $evento_id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $evento) {
    $cantidad = intval($_POST['cantidad']);
    $metodo_pago = $_POST['metodo_pago'];
    $cliente_id = $_SESSION['usuario_id'];
    $totalPago = $cantidad * floatval($evento->precio);
    $cupo = $evento->getCupo();

    if($cupo>=$cantidad){
        // Usar el método del modelo Ticket
        $ticket = new Ticket();
        list($ok, $result) = $ticket->registrarCompra($conn, $cliente_id, $evento_id, $cantidad, $totalPago, $metodo_pago);
        if ($ok) {            // Enviar email de confirmación
            $resUser = mysqli_query($conn, "SELECT email, nombre FROM Usuario WHERE id = $cliente_id");
            $rowUser = mysqli_fetch_assoc($resUser);
            $email = $rowUser['email'];
            $nombreUsuario = $rowUser['nombre'];
            
            $asunto = "✅ Confirmación de Compra - Ticket #" . $result;
            
            // Crear contenido del email más detallado
            $mensaje = "
¡Hola $nombreUsuario!

Tu compra ha sido procesada exitosamente. Aquí están los detalles:

═══════════════════════════════════════
    DETALLES DE LA COMPRA
═══════════════════════════════════════

🎫 Número de Ticket: #$result
🎉 Evento: " . htmlspecialchars($evento->titulo) . "
📅 Fecha del Evento: " . date('d/m/Y H:i', strtotime($evento->fecha)) . "
📍 Ubicación: " . htmlspecialchars($evento->lugar) . "
🎟️ Cantidad de Tickets: $cantidad
💰 Precio por Ticket: $" . number_format($evento->precio, 2) . "
💳 Método de Pago: " . ucfirst($metodo_pago) . "
💵 Total Pagado: $" . number_format($totalPago, 2) . "

═══════════════════════════════════════
    INFORMACIÓN IMPORTANTE
═══════════════════════════════════════

• Conserva este email como comprobante de compra
• Presenta tu ticket (digital o impreso) el día del evento
• Llega con 30 minutos de anticipación
• En caso de dudas, contacta al organizador

¡Esperamos que disfrutes el evento!

---
Ticketera PHP - Sistema de Gestión de Eventos
Fecha de compra: " . date('d/m/Y H:i:s') . "
            ";
            
            // Configurar headers para email
            $headers = "From: noreply@ticketera.com\r\n";
            $headers .= "Reply-To: support@ticketera.com\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($email, $asunto, $mensaje, $headers);
            $msg = "¡Compra realizada! Se ha enviado un email de confirmación.";
            desconectar($conn);
            $_SESSION['ticketComprado']=true;
            header("location: ../view/dashboard.php");
        } else {
            $msg = $result;
            desconectar($conn);
        }
    }
}
?>


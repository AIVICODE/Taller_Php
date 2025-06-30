<?php
// Ya que FPDF está instalado, incluimos directamente el archivo
require_once __DIR__ . '/../fpdf/fpdf.php';
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
    // Crear PDF
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 10, 'TICKET DE EVENTO', 0, 1, 'C');
            $this->Ln(5);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Título del evento
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode($row['evento_titulo']), 0, 1, 'C');
      // Agregar imagen del evento si existe
    if (!empty($row['evento_img'])) {
        try {
            $imgPath = $row['evento_img'];
            
            // Corregir la ruta de la imagen para que sea accesible desde el sistema de archivos
            if (strpos($imgPath, '../') === 0) {
                // Convertir ruta relativa a absoluta basada en la raíz del proyecto
                $imgPath = __DIR__ . '/../' . substr($imgPath, 3);
            } else if (strpos($imgPath, '/') === 0) {
                // Si comienza con /, asumimos que es relativa a la raíz del proyecto
                $imgPath = __DIR__ . '/..' . $imgPath;
            }
            
            // Intentar añadir la imagen al PDF
            if (file_exists($imgPath)) {
                $pdf->Image($imgPath, 70, $pdf->GetY(), 70);
                $pdf->Ln(75); // Espacio después de la imagen
            }
        } catch (Exception $e) {
            // Si hay algún problema con la imagen, simplemente la ignoramos
        }
    }
    
    $pdf->Ln(5);
    
    // Información del evento
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Detalles del evento:', 0, 1);
    
    $pdf->SetFont('Arial', '', 12);
    $fechaEvento = date('d/m/Y H:i', strtotime($row['evento_fecha']));
    $pdf->Cell(30, 8, 'Fecha:', 0);
    $pdf->Cell(0, 8, $fechaEvento, 0, 1);
    
    $pdf->Cell(30, 8, 'Lugar:', 0);
    $pdf->Cell(0, 8, utf8_decode($row['evento_lugar']), 0, 1);
    
    if (!empty($row['evento_descripcion'])) {
        $pdf->Cell(30, 8, 'Descripcion:', 0);
        $pdf->MultiCell(0, 8, utf8_decode($row['evento_descripcion']), 0);
    }
    
    $pdf->Ln(5);
    
    // Información del comprador
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Datos del comprador:', 0, 1);
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(30, 8, 'Nombre:', 0);
    $pdf->Cell(0, 8, utf8_decode($row['cliente_nombre']), 0, 1);
    
    $pdf->Cell(30, 8, 'Email:', 0);
    $pdf->Cell(0, 8, $row['cliente_email'], 0, 1);
    
    $pdf->Ln(5);
    
    // Información del ticket
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Detalles del ticket:', 0, 1);
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(60, 8, 'Numero de ticket:', 0);
    $pdf->Cell(0, 8, $row['id'], 0, 1);
    
    $pdf->Cell(60, 8, 'Cantidad de entradas:', 0);
    $pdf->Cell(0, 8, $row['cantidad'], 0, 1);
    
    $pdf->Cell(60, 8, 'Fecha de compra:', 0);
    $fechaCompra = date('d/m/Y H:i', strtotime($row['fechaCompra']));
    $pdf->Cell(0, 8, $fechaCompra, 0, 1);
    
    $pdf->Cell(60, 8, 'Metodo de pago:', 0);
    $pdf->Cell(0, 8, utf8_decode($row['metodoPago']), 0, 1);
    
    $pdf->Cell(60, 8, 'Estado del pago:', 0);
    $pdf->Cell(0, 8, utf8_decode($row['pago_estado']), 0, 1);
    
    $pdf->Cell(60, 8, 'Total pagado:', 0);
    $pdf->Cell(0, 8, '$' . number_format($row['totalPago'], 2), 0, 1);
    
    $pdf->Ln(10);
    
    // Código QR ficticio
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Codigo de validacion:', 0, 1, 'C');
    
    // Generar un código aleatorio para simular un código de barras o QR
    $codigo = 'TKT-' . strtoupper(substr(md5($row['id'] . $row['cliente_id'] . $row['evento_titulo']), 0, 10));
    $pdf->SetFont('Courier', 'B', 14);
    $pdf->Cell(0, 10, $codigo, 0, 1, 'C');
    
    // Mostrar un rectángulo como representación gráfica del código
    $pdf->Rect(75, $pdf->GetY(), 60, 30);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(75, $pdf->GetY() + 15);
    $pdf->Cell(60, 10, 'QR Code / Barcode', 0, 1, 'C');
    
    $pdf->Ln(10);
    
    // Nota al pie
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Este ticket es valido unicamente para la persona que realizo la compra.', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Presenta este documento al ingresar al evento.', 0, 1, 'C');
    
    // Generar el PDF
    $pdf->Output('D', 'Ticket_' . $row['id'] . '.pdf');
} else {
    echo "No se encontró el ticket solicitado o no tienes permisos para acceder a él.";
}

desconectar($conn);

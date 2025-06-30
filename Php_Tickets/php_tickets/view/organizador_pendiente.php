<?php
session_start();
require_once __DIR__ . '/../conection/sql.php';
require_once __DIR__ . '/../model/organizador.php';

// Verificar si hay un usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$conn = conectar();
$usuario_id = $_SESSION['usuario_id'];
$esPendiente = Organizador::esOrganizadorPendiente($conn, $usuario_id);
desconectar($conn);

if (!$esPendiente) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Pendiente - TicketLand</title>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .status-message {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .info-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #0066cc;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: left;
        }

        .info-box h3 {
            margin-bottom: 1rem;
            color: #0066cc;
        }

        .info-box ul {
            margin-left: 1.5rem;
        }

        .info-box li {
            margin-bottom: 0.5rem;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⏳</div>
        
        <h1>Cuenta en Proceso de Aprobación</h1>
        
        <div class="status-message">
            <strong>Tu solicitud como organizador está siendo revisada</strong><br>
            Un administrador debe aprobar tu cuenta antes de que puedas crear eventos.
        </div>
        
        <div class="info-box">
            <h3>¿Qué sigue?</h3>
            <ul>
                <li>Tu solicitud está en la cola de revisión</li>
                <li>Un administrador revisará tu perfil</li>
                <li>Recibirás acceso completo una vez aprobado</li>
                <li>Podrás crear y gestionar eventos</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>Mientras tanto...</h3>
            <ul>
                <li>Puedes explorar eventos como cliente</li>
                <li>Revisar la documentación de organizadores</li>
                <li>Contactar al soporte si tienes dudas</li>
            </ul>
        </div>
        
        <a href="../controller/logout_controller.php" class="btn btn-secondary">Cerrar Sesión</a>
        <a href="dashboard.php" class="btn">Ver Dashboard</a>
    </div>
</body>
</html>

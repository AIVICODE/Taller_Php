<?php
require_once __DIR__ . '/usuario.php';

class Admin {
    private $id;
    
    public function getId() { 
        return $this->id; 
    }
    
    // Verificar si un usuario es admin
    public static function esAdmin($conn, $usuario_id) {
        $stmt = $conn->prepare("SELECT id FROM Admin WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $esAdmin = $result->num_rows > 0;
        $stmt->close();
        return $esAdmin;
    }
    
    // Login específico para admin
    public static function loginAdmin($conn, $email, $pass) {
        // Primero hacer login como usuario normal
        $usuario = Usuario::login($conn, $email, $pass);
        if ($usuario) {
            // Verificar si es admin
            if (self::esAdmin($conn, $usuario->getId())) {
                return $usuario;
            }
        }
        return null;
    }
    
    // Obtener todos los usuarios organizadores pendientes
    public static function getOrganizadoresPendientes($conn) {
        $sql = "SELECT u.id, u.nombre, u.email, u.fechaRegistro 
                FROM Usuario u 
                INNER JOIN Organizador o ON u.id = o.id 
                WHERE o.aprobado = FALSE
                ORDER BY u.fechaRegistro DESC";
        
        $result = mysqli_query($conn, $sql);
        $usuarios = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $usuarios[] = $row;
        }
        
        return $usuarios;
    }
    
    // Aprobar usuario como organizador
    public static function aprobarOrganizador($conn, $usuario_id) {
        $stmt = $conn->prepare("UPDATE Organizador SET aprobado = TRUE, fechaAprobacion = NOW() WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Obtener todos los organizadores aprobados
    public static function getOrganizadoresAprobados($conn) {
        $sql = "SELECT u.id, u.nombre, u.email, u.fechaRegistro, o.fechaAprobacion 
                FROM Usuario u 
                INNER JOIN Organizador o ON u.id = o.id
                WHERE o.aprobado = TRUE
                ORDER BY o.fechaAprobacion DESC";
        
        $result = mysqli_query($conn, $sql);
        $organizadores = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $organizadores[] = $row;
        }
        
        return $organizadores;
    }
    
    // Desaprobar organizador (cambiar a pendiente)
    public static function removerOrganizador($conn, $usuario_id) {
        $stmt = $conn->prepare("UPDATE Organizador SET aprobado = FALSE, fechaAprobacion = NULL WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    // Obtener estadísticas generales del sistema
    public static function getEstadisticasGenerales($conn) {
        $estadisticas = [];
        
        // Total de usuarios registrados
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM Usuario");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_usuarios'] = $row['total'];
        
        // Total de clientes
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM Cliente");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_clientes'] = $row['total'];
        
        // Total de organizadores aprobados
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM Organizador WHERE aprobado = TRUE");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_organizadores_aprobados'] = $row['total'];
        
        // Total de organizadores pendientes
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM Organizador WHERE aprobado = FALSE");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_organizadores_pendientes'] = $row['total'];
        
        // Total de eventos activos
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM Evento WHERE estado = 'activo'");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_eventos_activos'] = $row['total'];
        
        // Total de eventos (todos los estados)
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM Evento");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_eventos'] = $row['total'];
        
        // Total de tickets vendidos
        $result = mysqli_query($conn, "SELECT SUM(cantidad) as total FROM Ticket");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_tickets_vendidos'] = $row['total'] ?? 0;
        
        // Total de ingresos
        $result = mysqli_query($conn, "SELECT SUM(totalPago) as total FROM Ticket");
        $row = mysqli_fetch_assoc($result);
        $estadisticas['total_ingresos'] = $row['total'] ?? 0;
        
        // Eventos por categoría
        $result = mysqli_query($conn, "
            SELECT c.descripcion, COUNT(e.id) as cantidad 
            FROM Categoria c 
            LEFT JOIN Evento e ON c.id = e.categoria_id 
            GROUP BY c.id, c.descripcion
            ORDER BY cantidad DESC
        ");
        $estadisticas['eventos_por_categoria'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $estadisticas['eventos_por_categoria'][] = $row;
        }
        
        // Eventos recientes (últimos 5)
        $result = mysqli_query($conn, "
            SELECT e.titulo, e.fecha, u.nombre as organizador, e.estado
            FROM Evento e
            INNER JOIN Organizador o ON e.organizador_id = o.id
            INNER JOIN Usuario u ON o.id = u.id
            ORDER BY e.fecha DESC
            LIMIT 5
        ");
        $estadisticas['eventos_recientes'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $estadisticas['eventos_recientes'][] = $row;
        }
        
        // Tickets vendidos por mes (últimos 6 meses)
        $result = mysqli_query($conn, "
            SELECT 
                DATE_FORMAT(fechaCompra, '%Y-%m') as mes,
                SUM(cantidad) as tickets,
                SUM(totalPago) as ingresos
            FROM Ticket 
            WHERE fechaCompra >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(fechaCompra, '%Y-%m')
            ORDER BY mes DESC
        ");
        $estadisticas['tickets_por_mes'] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $estadisticas['tickets_por_mes'][] = $row;
        }
        
        return $estadisticas;
    }
}
?>
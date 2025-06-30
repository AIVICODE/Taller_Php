<?php
require_once "usuario.php";

class Organizador extends Usuario{

    private $eventosOrganizados = array();

    public function getEventos() { return $$eventosOrganizados; }

    public static function esOrganizador($conn, $usuario_id) {
        $res = mysqli_query($conn, "SELECT * FROM Organizador WHERE id = $usuario_id AND aprobado = TRUE");
        $esOrg = mysqli_num_rows($res) > 0;
        return $esOrg;
    }
    
    // Verificar si es organizador pero pendiente de aprobación
    public static function esOrganizadorPendiente($conn, $usuario_id) {
        $res = mysqli_query($conn, "SELECT * FROM Organizador WHERE id = $usuario_id AND aprobado = FALSE");
        $esPendiente = mysqli_num_rows($res) > 0;
        return $esPendiente;
    }

    public static function registrarOrganizador($conn, $id) {
        $sql = "INSERT INTO Organizador (id, aprobado) VALUES ($id, FALSE)";
        return mysqli_query($conn, $sql);
    }
}
?>
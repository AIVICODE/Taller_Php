<?php
require_once "usuario.php";
require_once "ticket.php";

class Cliente extends Usuario{
    
    private $ticketComprados = array();

    public function getTickets() { return $this->ticketComprados; }

    public static function getInfoCliente($conn, $usuario_id) {
        $resUser = mysqli_query($conn, "SELECT * FROM Usuario WHERE id = $usuario_id");
        $user = null;
        if ($row = mysqli_fetch_assoc($resUser)) {
            $user = new Cliente();
            $user->id = $row['id'];
            $user->nombre = $row['nombre'];
            $user->email = $row['email'];
            $user->img = $row['imagen'];
        }
        // Obtener eventos comprados
        $eventos = array();
        $resTickets = mysqli_query($conn, "SELECT E.*, T.cantidad FROM Ticket T JOIN Evento E ON T.evento_id = E.id WHERE T.cliente_id = $usuario_id");
        while ($row = mysqli_fetch_assoc($resTickets)) {
            $evento = new Evento();
            $evento->id = $row['id'];
            $evento->titulo = $row['titulo'];
            $evento->fecha = $row['fecha'];
            $evento->lugar = $row['lugar'];
            $evento->cantidad = $row['cantidad'];
            $eventos[] = $evento;
        }
        return [$user, $eventos];
    }
    public static function esCliente($conn, $usuario_id) {
        $res = mysqli_query($conn, "SELECT * FROM Cliente WHERE id = $usuario_id");
        $esCliente = mysqli_num_rows($res) > 0;
        return $esCliente;
    }
    public static function registrarCliente($conn, $id) {
        $sql = "INSERT INTO Cliente (id) VALUES ($id)";
        return mysqli_query($conn, $sql);
    }
}
?>
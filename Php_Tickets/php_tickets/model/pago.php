<?php
class Pago{
    private $id, $metodoPago, $estado, $fechaPago;

    public function getId() { return $id; }
    public function getMetPag() { return $metodoPago; }
    public function getEstado() { return $estado; }
    public function getFecha() { return $fechaPago; }

    // Métodos estáticos delegados desde Ticket
    public static function crearPago($conn, $metodo_pago, $totalPago) {
        $sqlPago = "INSERT INTO Pago (metodoPago, estado, fechaPago, monto) VALUES ('" . mysqli_real_escape_string($conn, $metodo_pago) . "', 'completado', NOW(), $totalPago)";
        if (mysqli_query($conn, $sqlPago)) {
            return mysqli_insert_id($conn);
        }
        return false;
    }

    public static function eliminarPago($conn, $pago_id) {
        return mysqli_query($conn, "DELETE FROM Pago WHERE id = $pago_id");
    }
}
?>
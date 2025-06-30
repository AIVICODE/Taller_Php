<?php
require_once "cliente.php";
require_once "evento.php";
require_once "pago.php";

class Ticket{
    private $id, $cantidad, $totalPago, $fechaCompra;
    private $pago; //el pos asociado
    private $cli; //el cliente que compró las entradas
    private $evento; //el evento en cuestión

    public function getId() { return $this->id; }
    public function getCant() { return $this->cantidad; }
    public function getTotPag() { return $this->totalPago; }
    public function getFecha() { return $this->fechaCompra; }
    public function getPago() { return $this->pago; }
    public function getCli() { return $this->cli; }
    public function getEvento() { return $this->evento; }

    public static function actualizarCupoEvento($conn, $evento_id, $cantidad) {
        $updateCupo = "UPDATE Evento SET cupo = cupo - $cantidad WHERE id = $evento_id AND cupo >= $cantidad";
        return mysqli_query($conn, $updateCupo);
    }

    public static function getCupoEvento($conn, $evento_id) {
        $resCupo = mysqli_query($conn, "SELECT cupo FROM Evento WHERE id = $evento_id");
        return mysqli_fetch_assoc($resCupo);
    }

    public static function eliminarTicket($conn, $ticket_id) {
        return mysqli_query($conn, "DELETE FROM Ticket WHERE id = $ticket_id");
    }

    public function registrarCompra($conn, $cliente_id, $evento_id, $cantidad, $totalPago, $metodo_pago) {
        $pago_id = Pago::crearPago($conn, $metodo_pago, $totalPago);
        if ($pago_id) {
            $query = "INSERT INTO Ticket (cliente_id, evento_id, cantidad, totalPago, fechaCompra, pago_id) VALUES ($cliente_id, $evento_id, $cantidad, $totalPago, NOW(), $pago_id)";
            if (mysqli_query($conn, $query)) {
                self::actualizarCupoEvento($conn, $evento_id, $cantidad);
                $rowCupo = self::getCupoEvento($conn, $evento_id);
                if ($rowCupo['cupo'] < 0) {
                    self::eliminarTicket($conn, mysqli_insert_id($conn));
                    Pago::eliminarPago($conn, $pago_id);
                    return [false, "No hay suficientes cupos disponibles."];
                } else {
                    return [true, $pago_id];
                }
            } else {
                return [false, "Error al registrar el ticket."];
            }
        } else {
            return [false, "Error al registrar el pago."];
        }
    }
}
?>
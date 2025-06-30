<?php
require_once 'sql.php';

$conn = conectar();
if ($conn) {
    echo "Conexión exitosa a MariaDB";
    desconectar($conn);
} else {
    echo "Error de conexión";
}
?>
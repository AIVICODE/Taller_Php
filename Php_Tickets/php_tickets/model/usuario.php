<?php
class Usuario{
    private $id, $nombre, $email, $pass, $fechaReg, $img;
    
    public function getId() { return $this->id; }
    public function getNom() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getPass() { return $this->pass; }
    public function getFecha() { return $this->fechaReg; }
    public function getImg() { return $this->img; }
      public static function login($conn, $email_or_username, $pass) {
        $stmt = $conn->prepare("SELECT * FROM Usuario WHERE (nombre = ? OR email = ?) AND contrasena = ?");
        $stmt->bind_param("sss", $email_or_username, $email_or_username, $pass);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $usuario = new Usuario();
            $usuario->id = $row['id'];
            $usuario->nombre = $row['nombre'];
            $usuario->email = $row['email'];
            $usuario->pass = $row['contrasena'];
            $usuario->fechaReg = $row['fechaRegistro'];
            $usuario->img = $row['imagen'];
            $stmt->close();
            return $usuario;
        }
        $stmt->close();
        return null;
    }
    public static function existeUsuario($conn, $nickname, $email) {
        $sql = "SELECT * FROM Usuario WHERE nombre = '" . mysqli_real_escape_string($conn, $nickname) . "' OR email = '" . mysqli_real_escape_string($conn, $email) . "'";
        $res = mysqli_query($conn, $sql);
        return mysqli_num_rows($res) > 0;
    }    public function registrar($conn, $nickname, $email, $pass, $fecha, $img) {
        $sql = "INSERT INTO Usuario (nombre, email, contrasena, fechaRegistro, imagen) VALUES ('" .
            mysqli_real_escape_string($conn, $nickname) . "', '" .
            mysqli_real_escape_string($conn, $email) . "', '" .
            mysqli_real_escape_string($conn, $pass) . "', '" .
            $fecha . "', '" .
            mysqli_real_escape_string($conn, $img) . "')";
        if (mysqli_query($conn, $sql)) {
            return mysqli_insert_id($conn);
        }
        return false;
    }
}
?>
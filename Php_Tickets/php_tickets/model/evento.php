<?php
require_once "categoria.php";
require_once "ticket.php";

class Evento{
    public $id, $titulo, $desc, $img, $fecha, $lugar, $precio, $cupo, $estado;
    public $categorias = array(); //categorias del evento
    public $entradas = array(); //los tickets comprados para el evento
    public function getId() { return $this->id; }
    public function getTit() { return $this->titulo; }
    public function getDesc() { return $this->desc; }
    public function getImg() { return $this->img; }
    public function getFecha() { return $this->fecha; }
    public function getLugar() { return $this->lugar; }
    public function getPrecio() { return $this->precio; }
    public function getCupo() { return $this->cupo; }
    public function getEstado() { return $this->estado; }
    public function getCat() { return $this->categorias; }
    public function getEntradas() { return $this->entradas; }

    public static function usuarioEsOrganizador($conn, $usuario_id) {
        $res = mysqli_query($conn, "SELECT * FROM Organizador WHERE id = $usuario_id");
        return mysqli_num_rows($res) > 0;
    }

    public static function obtenerCategorias($conn) {
        $categorias = array();
        $resCat = mysqli_query($conn, 'SELECT * FROM Categoria');
        while ($row = mysqli_fetch_assoc($resCat)) {
            $categorias[] = $row;
        }
        return $categorias;
    }
      public static function getEventoDisponible($conn, $evento_id) {
        $resEv = mysqli_query($conn, "SELECT * FROM Evento WHERE id = $evento_id AND fecha > NOW()");
        if ($rowEv = mysqli_fetch_assoc($resEv)) {
            $evento = new Evento();
            $evento->id = $rowEv['id'];
            $evento->titulo = $rowEv['titulo'];
            $evento->desc = $rowEv['descripcion'];
            $evento->fecha = $rowEv['fecha'];
            $evento->lugar = $rowEv['lugar'];
            $evento->precio = $rowEv['precio'];
            $evento->cupo = $rowEv['cupo'];
            $evento->estado = $rowEv['estado'];
            
            // Obtener imagen del evento
            $resImg = mysqli_query($conn, "SELECT url_imagen FROM EventoImagen WHERE evento_id = $evento_id LIMIT 1");
            if ($imgRow = mysqli_fetch_assoc($resImg)) {
                $evento->img = $imgRow['url_imagen'];
            } else {
                $evento->img = null;
            }
            
            return $evento;
        }
        return null;
    }public function crearEvento($conn, $titulo, $descripcion, $fecha, $lugar, $precio, $cupo, $estado, $organizador_id, $categoria_id) {
        $sql = "INSERT INTO Evento (titulo, descripcion, fecha, lugar, precio, cupo, estado, organizador_id, categoria_id) VALUES ('$titulo', '$descripcion', '$fecha', '$lugar', $precio, $cupo, '$estado', $organizador_id, $categoria_id)";
        if (mysqli_query($conn, $sql)) {
            $evento_id = mysqli_insert_id($conn);
            return ['ok' => true, 'evento_id' => $evento_id];
        } else {
            return ['ok' => false];
        }
    }

    public static function buscarEventos($conn, $buscar) {
        $buscar = mysqli_real_escape_string($conn, $buscar);
        $eventos = array();
        
        // Consulta para eventos que coincidan con el texto de búsqueda
        $query = "SELECT e.*, c.id as cat_id, c.descripcion as cat_desc 
                 FROM Evento e 
                 JOIN Categoria c ON e.categoria_id = c.id 
                 WHERE e.titulo LIKE '%$buscar%' OR e.descripcion LIKE '%$buscar%'";
                 
        $resultado = mysqli_query($conn, $query);
          while ($row = mysqli_fetch_assoc($resultado)) {
            // Crear objeto evento
            $evento = self::crearDesdeFilaBD($row);
            
            // Añadir la información de la categoría como propiedad
            $evento->categoriaId = $row['cat_id'];
            $evento->categoriaDesc = $row['cat_desc'];
            
            $eventos[] = $evento;
        }
        
        return $eventos;
    }
    
    public static function crearDesdeFilaBD($row) {
        $evento = new Evento();
        $evento->id = $row['id'];
        $evento->titulo = $row['titulo'];
        $evento->desc = $row['descripcion'];
        $evento->fecha = $row['fecha'];
        $evento->lugar = $row['lugar'];
        
        // Asignar otros campos si están disponibles
        if (isset($row['precio'])) $evento->precio = $row['precio'];
        if (isset($row['cupo'])) $evento->cupo = $row['cupo'];
        if (isset($row['estado'])) $evento->estado = $row['estado'];
        
        return $evento;
    }
    
    /**
     * Obtiene todas las imágenes asociadas a un evento
     * @param mysqli $conn Conexión a la base de datos
     * @param int $evento_id ID del evento
     * @return array Array con las URLs de las imágenes
     */
    public static function obtenerImagenesEvento($conn, $evento_id) {
        $imagenes = array();
        $query = "SELECT url_imagen FROM EventoImagen WHERE evento_id = " . intval($evento_id);
        $resultado = mysqli_query($conn, $query);
        
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $imagenes[] = $fila['url_imagen'];
            }
        }
        
        return $imagenes;
    }
}
?>
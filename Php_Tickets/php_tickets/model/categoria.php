<?php
class Categoria{
    public $id, $desc;
    public $eventos = array(); //eventos con esta categoria
    public function getId() { return $this->id; }
    public function getDesc() { return $this->desc; }    public static function obtenerCategoriasConEventos($conn) {
        $categorias = array();
        $resCat = mysqli_query($conn, 'SELECT * FROM Categoria');
        while ($row = mysqli_fetch_assoc($resCat)) {
            $cat = new Categoria();
            $cat->id = $row['id'];
            $cat->desc = $row['descripcion'];
            $cat->eventos = array();
            $resEv = mysqli_query($conn, 'SELECT * FROM Evento WHERE categoria_id = ' . intval($row['id']));
            while ($rowEv = mysqli_fetch_assoc($resEv)) {
                $ev = new Evento();
                $ev->id = $rowEv['id'];
                $ev->titulo = $rowEv['titulo'];
                $ev->desc = $rowEv['descripcion'];
                $ev->fecha = $rowEv['fecha'];
                $ev->lugar = $rowEv['lugar'];
                
                // Obtener imagen del evento
                $resImg = mysqli_query($conn, "SELECT url_imagen FROM EventoImagen WHERE evento_id = " . $ev->id . " LIMIT 1");
                if ($imgRow = mysqli_fetch_assoc($resImg)) {
                    $ev->imagen_url = $imgRow['url_imagen'];
                } else {
                    $ev->imagen_url = null;
                }
                
                $cat->eventos[] = $ev;
            }
            $categorias[] = $cat;
        }
        return $categorias;
    }

    public static function buscarPorTexto($conn, $buscar) {
        $categorias = array();
        $buscar = mysqli_real_escape_string($conn, $buscar);
        
        // Solo buscar categorías que coincidan con el texto
        $query = "SELECT * FROM Categoria WHERE descripcion LIKE '%$buscar%'";
        $resultado = mysqli_query($conn, $query);
        
        while ($row = mysqli_fetch_assoc($resultado)) {
            $cat = new Categoria();
            $cat->id = $row['id'];
            $cat->desc = $row['descripcion'];
            $cat->eventos = array(); 
            $categorias[] = $cat;
        }
                
        return $categorias;
    }

    private static function cargarEventosParaCategoria($conn, $categoria, $filtro = '') {
        if (empty($categoria->eventos)) {
            $categoria->eventos = array();
        }
        
        $where = 'categoria_id = ' . intval($categoria->id);
        
        if (!empty($filtro)) {
            $filtro = mysqli_real_escape_string($conn, $filtro);
            $where .= " AND (titulo LIKE '%$filtro%' OR descripcion LIKE '%$filtro%')";
        }
        
        $resEv = mysqli_query($conn, "SELECT * FROM Evento WHERE $where");
        
        // Rastreamos IDs de eventos ya incluidos para evitar duplicados
        $eventosIncluidos = array_map(function($ev) { return $ev->id; }, $categoria->eventos);
        
        while ($rowEv = mysqli_fetch_assoc($resEv)) {
            $eventoId = $rowEv['id'];
            
            // Evitar duplicados
            if (in_array($eventoId, $eventosIncluidos)) {
                continue;
            }
            
            // Usar el método de la clase Evento
            $ev = Evento::crearDesdeFilaBD($rowEv);
            $categoria->eventos[] = $ev;
            $eventosIncluidos[] = $eventoId;
        }
    }
}
?>
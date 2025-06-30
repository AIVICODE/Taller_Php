<?php
require_once '../model/categoria.php';
require_once '../model/evento.php';
require_once '../model/organizador.php';
require_once '../model/cliente.php';
require_once '../conection/sql.php';

function obtenerCategoriasConEventos() {
    $conn = conectar();
    $categorias = Categoria::obtenerCategoriasConEventos($conn);
    desconectar($conn);
    return $categorias;
}

function esOrganizador($usuario_id) {
    $conn = conectar();
    $esOrg = Organizador::esOrganizador($conn, $usuario_id);
    desconectar($conn);
    return $esOrg;
}

function esCliente($usuario_id) {
    $conn = conectar();
    $esCliente = Cliente::esCliente($conn, $usuario_id);
    desconectar($conn);
    return $esCliente;
}

function buscarEventosPorTituloOCategoria($buscar) {
    $conn = conectar();
    $categorias = array();
    $catIdsIncluidos = array();
    
    // PASO 1: Obtener categorías que coincidan con la búsqueda
    $categoriasBuscadas = Categoria::buscarPorTexto($conn, $buscar);
    
    // Para cada categoría encontrada, cargar sus eventos
    foreach ($categoriasBuscadas as $cat) {
        $catIdsIncluidos[] = $cat->id;
          // Cargar eventos para esta categoría
        $resEv = mysqli_query($conn, "SELECT * FROM Evento WHERE categoria_id = " . intval($cat->id));
        if ($resEv) {
            while ($rowEv = mysqli_fetch_assoc($resEv)) {
            $ev = Evento::crearDesdeFilaBD($rowEv);
            
            // Obtener imagen del evento
            $resImg = mysqli_query($conn, "SELECT url_imagen FROM EventoImagen WHERE evento_id = " . $ev->id . " LIMIT 1");
            if ($imgRow = mysqli_fetch_assoc($resImg)) {
                $ev->imagen_url = $imgRow['url_imagen'];
            } else {
                $ev->imagen_url = null;            }
            
            $cat->eventos[] = $ev;
            }
        }
        
        $categorias[] = $cat;
    }
    
    // PASO 2: Buscar eventos por título o descripción usando el modelo Evento
    $eventosBuscados = Evento::buscarEventos($conn, $buscar);
    
    // Obtener IDs de eventos ya incluidos para evitar duplicados
    $eventosIncluidos = array();
    foreach ($categorias as $cat) {
        foreach ($cat->eventos as $ev) {
            $eventosIncluidos[] = $ev->id;
        }
    }
    
    // Agrupar los eventos encontrados por categoría
    foreach ($eventosBuscados as $evento) {
        // Evitar duplicados
        if (in_array($evento->id, $eventosIncluidos)) {
            continue;
        }
        
        $categoriaId = $evento->categoriaId;
        
        // Verificar si ya tenemos esta categoría
        $categoriaExistente = false;
        foreach ($categorias as $index => $cat) {
            if ($cat->id == $categoriaId) {
                // Añadir este evento a la categoría existente
                $categorias[$index]->eventos[] = $evento;
                $eventosIncluidos[] = $evento->id;
                $categoriaExistente = true;
                break;
            }
        }
        
        // Si la categoría no existe, crearla con este evento
        if (!$categoriaExistente) {
            $cat = new Categoria();
            $cat->id = $categoriaId;
            $cat->desc = $evento->categoriaDesc;
            $cat->eventos = array($evento);
            $categorias[] = $cat;
            $catIdsIncluidos[] = $categoriaId;
            $eventosIncluidos[] = $evento->id;
        }
    }
    
    desconectar($conn);
    return $categorias;
}

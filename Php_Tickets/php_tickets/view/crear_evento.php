<?php
require_once __DIR__ . '/../controller/crear_evento_controller.php';
?>
<?php include __DIR__ . '/components/navbar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../stylesheets/perfil_cliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/crear_evento.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="mb-4 text-center">Crear Nuevo Evento</h2>
                        <form class="crear-evento-form" method="post" action="procesar_evento.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título:</label>
                                <input type="text" class="form-control form-control-lg" id="titulo" name="titulo" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción:</label>
                                <textarea class="form-control form-control-lg" id="descripcion" name="descripcion" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="lugar" class="form-label">Lugar:</label>
                                <input type="text" class="form-control form-control-lg" id="lugar" name="lugar" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha:</label>
                                <input type="datetime-local" class="form-control form-control-lg" id="fecha" name="fecha" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría:</label>
                                <select class="form-select form-select-lg" id="categoria" name="categoria_id" required>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['descripcion']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio de entrada:</label>
                                <input type="number" class="form-control form-control-lg" id="precio" name="precio" step="0.01" min="0" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cupo" class="form-label">Cantidad máxima de personas:</label>
                                <input type="number" class="form-control form-control-lg" id="cupo" name="cupo" min="1" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="imagenes" class="form-label">Imágenes relevantes:</label>
                                <input type="file" class="form-control form-control-lg" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                            </div>
                            
                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary btn-lg" type="submit">Crear Evento</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a class="btn btn-outline-secondary" href="dashboard.php">Volver al dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Ticket</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../stylesheets/variables.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/perfil_cliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../stylesheets/comprar_ticket.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<?php
require_once __DIR__ . '/../controller/comprar_ticket_controller.php';
?>
<?php include __DIR__ . '/components/navbar.php'; ?>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="card-title mb-0">Comprar Ticket</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($msg && strpos($msg, '¡Compra realizada!') !== false): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <div class="me-2"><i class="bi bi-check-circle-fill"></i></div>
                                <div>¡Compra realizada! Se ha enviado un email de confirmación. <a href="dashboard.php">Puede volver al dashboard</a></div>
                            </div>
                        <?php elseif ($msg): ?>
                            <div class="alert alert-warning" role="alert"><?php echo htmlspecialchars($msg); ?></div>
                        <?php endif; ?>

                        <?php if ($evento): ?>
                        <form class="comprar-ticket-form needs-validation" method="post" novalidate>
                            <input type="hidden" name="evento_id" value="<?php echo is_object($evento) ? $evento->id : (isset($evento['id']) ? $evento['id'] : ''); ?>">
                            
                            <div class="row mb-4">
                                <div class="col-lg-4 col-md-5 col-sm-6 mb-3 mb-sm-0">
                                    <div class="evento-img-container d-flex justify-content-center">
                                        <?php if (is_object($evento) && $evento->img): ?>
                                            <?php 
                                            // Corregir la ruta de la imagen si es necesario
                                            $imgPath = $evento->img;
                                            // Si la ruta comienza con "/", aseguramos que sea relativa a la raíz del sitio
                                            if (strpos($imgPath, '/') === 0) {
                                                $imgPath = '..' . $imgPath;
                                            }
                                            ?>
                                            <img src="<?php echo htmlspecialchars($imgPath); ?>" class="evento-img-grande rounded shadow-sm" onclick="ampliarImagen(this)" alt="Imagen del evento">
                                        <?php else: ?>
                                            <div class="evento-img-placeholder evento-img-grande rounded shadow-sm d-flex justify-content-center align-items-center" onclick="ampliarImagen(this)">Sin imagen</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-7 col-sm-6">
                                    <div class="evento-ticket-datos">
                                        <h4 class="mb-3"><?php echo is_object($evento) ? htmlspecialchars($evento->titulo) : (isset($evento['titulo']) ? htmlspecialchars($evento['titulo']) : ''); ?></h4>
                                        <p class="mb-2"><i class="bi bi-calendar3"></i> <strong>Fecha y hora:</strong> <?php echo is_object($evento) ? date('d/m/Y H:i', strtotime($evento->fecha)) : (isset($evento['fecha']) ? date('d/m/Y H:i', strtotime($evento['fecha'])) : ''); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="cantidad" class="form-label">Cantidad de personas:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="<?php echo $evento->getCupo(); ?>" required>
                                    <span class="input-group-text cupo-badge">Quedan: <?php echo $evento->getCupo(); ?></span>
                                </div>
                                <div class="form-text cupo-restante">Solo quedan <?php echo $evento->getCupo(); ?> entradas disponibles.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label">Método de pago:</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                    <option value="">Seleccione método de pago</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Efectivo">Efectivo</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button class="btn btn-primary comprar-btn" type="submit">Comprar</button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">No se encontró el evento o ya no está disponible.</div>
                        <?php endif; ?>
                        
                        <div class="mt-4 d-flex justify-content-center justify-content-md-start">
                            <a class="btn btn-secondary volver-btn" href="dashboard.php">
                                <i class="bi bi-arrow-left"></i> Volver al dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para la imagen -->
    <div class="modal fade" id="modal-img" tabindex="-1" aria-labelledby="modalImagenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImagenLabel">Vista previa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php if (is_object($evento) && $evento->img): ?>
                        <?php 
                        // Corregir la ruta de la imagen si es necesario
                        $imgPath = $evento->img;
                        // Si la ruta comienza con "/", aseguramos que sea relativa a la raíz del sitio
                        if (strpos($imgPath, '/') === 0) {
                            $imgPath = '..' . $imgPath;
                        }
                        ?>
                        <img id="modal-img-content" src="<?php echo htmlspecialchars($imgPath); ?>" class="img-fluid rounded shadow" alt="Imagen del evento">
                    <?php else: ?>
                        <div id="modal-img-placeholder" class="evento-img-placeholder evento-img-grande mx-auto rounded shadow" style="width:220px;height:220px;font-size:1.5em;display:flex;align-items:center;justify-content:center;">Sin imagen</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <script>
    // Función para mostrar el modal con Bootstrap
    function ampliarImagen(el) {
        if (el.tagName === 'IMG') {
            // Si es una imagen
            if (document.getElementById('modal-img-content')) {
                document.getElementById('modal-img-content').src = el.src;
            }
        } else {
            // Si es un placeholder
            if (document.getElementById('modal-img-placeholder')) {
                document.getElementById('modal-img-placeholder').innerText = el.innerText;
            }
        }
        const modal = new bootstrap.Modal(document.getElementById('modal-img'));
        modal.show();
    }
    
    // Validación de Bootstrap para formularios
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>
</body>
</html>

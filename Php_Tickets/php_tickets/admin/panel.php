<?php require_once __DIR__ . '/../controller/admin_panel_controller.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="icon" type="image/png" href="../stylesheets/images/favicon.png">
    <link rel="stylesheet" href="../stylesheets/admin/panel.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>🛡️ Panel de Administración</h1>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin_nombre']); ?></span>
                <a href="../controller/logout_controller.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo strpos($mensaje, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_usuarios']; ?></div>
                <div class="stat-label">👥 Total Usuarios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_organizadores_pendientes']; ?></div>
                <div class="stat-label">⏳ Organizadores Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_organizadores_aprobados']; ?></div>
                <div class="stat-label">✅ Organizadores Aprobados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_clientes']; ?></div>
                <div class="stat-label">🛒 Clientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_eventos_activos']; ?></div>
                <div class="stat-label">🎪 Eventos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_tickets_vendidos']; ?></div>
                <div class="stat-label">🎫 Tickets Vendidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($estadisticas['total_ingresos'], 0, ',', '.'); ?></div>
                <div class="stat-label">💰 Ingresos Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_eventos']; ?></div>
                <div class="stat-label">📊 Total Eventos</div>
            </div>
        </div>

        <!-- Usuarios Pendientes -->
        <div class="section">
            <div class="section-header">
                <h2>👥 Usuarios Pendientes de Aprobación</h2>
            </div>
            <div class="section-content">
                <?php if (empty($usuariosPendientes)): ?>
                    <div class="empty-state">
                        <p>No hay usuarios pendientes de aprobación.</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuariosPendientes as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['fechaRegistro'])); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                            <input type="hidden" name="accion" value="aprobar">
                                            <button type="submit" class="btn btn-success" onclick="return confirm('¿Aprobar este usuario como organizador?')">
                                                ✓ Aprobar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Organizadores Aprobados -->
        <div class="section">
            <div class="section-header">
                <h2>🎯 Organizadores Aprobados</h2>
            </div>
            <div class="section-content">
                <?php if (empty($organizadoresAprobados)): ?>
                    <div class="empty-state">
                        <p>No hay organizadores aprobados.</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha de Registro</th>
                                <th>Fecha de Aprobación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($organizadoresAprobados as $organizador): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($organizador['id']); ?></td>
                                    <td><?php echo htmlspecialchars($organizador['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($organizador['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($organizador['fechaRegistro'])); ?></td>
                                    <td><?php echo $organizador['fechaAprobacion'] ? date('d/m/Y H:i', strtotime($organizador['fechaAprobacion'])) : 'N/A'; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="usuario_id" value="<?php echo $organizador['id']; ?>">
                                            <input type="hidden" name="accion" value="remover">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Desaprobar este organizador?')">
                                                ⏸️ Desaprobar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Eventos por Categoría -->
        <div class="section">
            <div class="section-header">
                <h2>📊 Eventos por Categoría</h2>
            </div>
            <div class="section-content">
                <?php if (empty($estadisticas['eventos_por_categoria'])): ?>
                    <div class="empty-state">
                        <p>No hay datos de categorías disponibles.</p>
                    </div>
                <?php else: ?>
                    <div class="category-stats">
                        <?php foreach ($estadisticas['eventos_por_categoria'] as $categoria): ?>
                            <div class="category-item">
                                <div class="category-name"><?php echo htmlspecialchars($categoria['descripcion']); ?></div>
                                <div class="category-bar">
                                    <div class="category-fill" style="width: <?php echo $categoria['cantidad'] > 0 ? ($categoria['cantidad'] / max(array_column($estadisticas['eventos_por_categoria'], 'cantidad')) * 100) : 0; ?>%"></div>
                                </div>
                                <div class="category-count"><?php echo $categoria['cantidad']; ?> eventos</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Eventos Recientes -->
        <div class="section">
            <div class="section-header">
                <h2>🎪 Eventos Recientes</h2>
            </div>
            <div class="section-content">
                <?php if (empty($estadisticas['eventos_recientes'])): ?>
                    <div class="empty-state">
                        <p>No hay eventos registrados.</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Organizador</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estadisticas['eventos_recientes'] as $evento): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($evento['organizador']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($evento['fecha'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $evento['estado']; ?>">
                                            <?php echo ucfirst($evento['estado']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ventas por Mes -->
        <div class="section">
            <div class="section-header">
                <h2>📈 Ventas por Mes (Últimos 6 meses)</h2>
            </div>
            <div class="section-content">
                <?php if (empty($estadisticas['tickets_por_mes'])): ?>
                    <div class="empty-state">
                        <p>No hay datos de ventas disponibles.</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Tickets Vendidos</th>
                                <th>Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estadisticas['tickets_por_mes'] as $mes): ?>
                                <tr>
                                    <td><?php echo date('F Y', strtotime($mes['mes'] . '-01')); ?></td>
                                    <td><?php echo number_format($mes['tickets']); ?></td>
                                    <td>$<?php echo number_format($mes['ingresos'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

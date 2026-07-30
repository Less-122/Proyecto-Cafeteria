<?php
include 'seguridad_admin.php';
require_once '../config/conexion.php';

/* Obtener pedidos junto con el nombre del cliente que lo hizo */
$sql = "
    SELECT
        p.id_pedido,
        p.id_usuario,
        DATE_FORMAT(p.fecha_creacion, '%d-%m-%Y %H:%i') AS fecha_creacion,
        DATE_FORMAT(p.fecha_vencimiento, '%d-%m-%Y') AS fecha_vencimiento,
        p.clave_retiro,
        p.total,
        p.estado,
        u.nombre,
        u.apellido
    FROM pedidos p
    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
    ORDER BY p.fecha_creacion DESC, p.id_pedido DESC
";

$resultado = $conexion->query($sql);
$pedidos = $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <!-- Rutas absolutas -->
    <link rel="stylesheet" href="/Proyecto-Cafeteria/css/admin.css">
</head>
<body>

    <!-- Inserción directa con PHP -->
    <?php include 'admin_header.php'; ?>
    <?php include 'admin_menu.php'; ?>

    <main class="main_container">
        <h1 class="titulo">Historial de Pedidos</h1>
        
        <section>
            <div class="filtros_container">
                <div class="search_box">
                    <ion-icon name="search-outline" class="icono_filtro"></ion-icon>
                    <input type="text" id="searchInput" placeholder="Buscar pedidos">
                </div>
            </div>
        </section>

        <table id="tablaPedidos">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha de pedido</th>
                <th>Fecha de vencimiento</th>
                <th>Clave de retiro</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>

            <?php if (!empty($pedidos)): ?>

                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= (int) $pedido['id_pedido'] ?></td>
                        <td><?= htmlspecialchars(
                            $pedido['nombre'] . ' ' . $pedido['apellido'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></td>
                        <td><?= htmlspecialchars($pedido['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pedido['fecha_vencimiento'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pedido['clave_retiro'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>$<?= number_format((float) $pedido['total'], 2) ?></td>
                        <td>
                            <span class="estado-badge estado-<?= htmlspecialchars($pedido['estado'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars(ucfirst($pedido['estado']), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" style="text-align:center;">No hay pedidos registrados.</td>
                </tr>

            <?php endif; ?>

        </table>

    </main>
    
    <!-- Rutas absolutas para scripts -->
    <script src="/Proyecto-Cafeteria/js/admin.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</body>
</html>
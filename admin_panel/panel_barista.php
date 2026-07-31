<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['nuevo_estado'])) {
    try {
        // Obligamos a la base de datos a gritar el error en lugar de callarlo
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $id_pedido = (int)$_POST['id_pedido'];
        $nuevo_estado = $_POST['nuevo_estado'];

        // Si tu llave primaria no se llama id_pedido, el error estallará aquí y te dirá exactamente el nombre correcto
        $sql = "UPDATE pedidos SET estado = :estado WHERE id_pedido = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id_pedido]);

        header("Location: panel_barista.php");
        exit;
    } catch (PDOException $e) {
        // Esto detendrá la página en blanco y te mostrará el problema real
        die("<div style='background: #cc0c39; color: white; padding: 20px; font-family: sans-serif;'>
                <h2>Error Crítico en Base de Datos</h2>
                <p><strong>Detalle:</strong> " . $e->getMessage() . "</p>
                <p>Tu columna no se llama así o los tipos de datos no coinciden. Revisa phpMyAdmin.</p>
             </div>");
    }
}

function obtenerPedidos($conexion, $estado) {
    $stmt = $conexion->prepare(
        "SELECT p.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario " .
        "FROM pedidos p " .
        "LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario " .
        "WHERE LOWER(p.estado) = LOWER(:estado) " .
        "ORDER BY p.fecha_creacion ASC"
    );
    $stmt->execute([':estado' => $estado]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDetallesPedido($conexion, $idPedido) {
    $stmt = $conexion->prepare(
        "SELECT dp.id_producto, dp.cantidad, dp.precio_unitario, pr.nombre " .
        "FROM detalle_pedido dp " .
        "LEFT JOIN productos pr ON dp.id_producto = pr.id_producto " .
        "WHERE dp.id_pedido = :id_pedido " .
        "ORDER BY dp.id_detalle_p ASC"
    );
    $stmt->execute([':id_pedido' => $idPedido]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pendientes  = obtenerPedidos($conexion, 'pendiente');
$preparacion = obtenerPedidos($conexion, 'preparacion');
$listos      = obtenerPedidos($conexion, 'listo');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Barista - Aroma a Café</title>
    <link rel="stylesheet" href="/Proyecto-Cafeteria/css/barista.css">
    <link rel="icon" type="image/jpeg" href="/Proyecto-Cafeteria/img/Logo/isotipoAzul.jpeg">
</head>
<body class="admin-body">

    <!-- Header modificado con Flexbox para alinear el botón de Salir -->
    <header class="barista-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
        <h1 style="margin: 0;">Aroma a Café | Monitor de Pedidos</h1>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="status-indicator">
                <span class="dot pulse"></span> Conectado (Tiempo Real)
            </div>
            <!-- Botón de Salir dirigido a productos.php -->
            <a href="/Proyecto-Cafeteria/admin_panel/productos.php" style="background-color: #cc0c39; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Salir</a>
        </div>
    </header>

    <main class="kanban-board">
        
        <section class="kanban-column" id="col-pendientes">
            <h2>Pendientes <span>(<?= count($pendientes) ?>)</span></h2>
            <?php foreach ($pendientes as $pedido): ?>
            <?php $detallesPedido = obtenerDetallesPedido($conexion, $pedido['id_pedido']); ?>
            <article class="order-card alert">
                <header class="order-header">
                    <h3>Pedido #<?= (int) $pedido['id_pedido'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">
                    Cliente: <strong><?= htmlspecialchars(trim(($pedido['nombre_usuario'] ?? '') . ' ' . ($pedido['apellido_usuario'] ?? '')), ENT_QUOTES, 'UTF-8') ?: 'Cliente sin nombre' ?></strong>
                </div>
                <div class="order-receipt">
                    <?php $subtotalPedido = 0; ?>
                    <?php foreach ($detallesPedido as $detalle): ?>
                        <?php $precioUnitario = (float) ($detalle['precio_unitario'] ?? 0); ?>
                        <?php $cantidad = (int) ($detalle['cantidad'] ?? 0); ?>
                        <?php $subtotalPedido += $precioUnitario * $cantidad; ?>
                        <div class="receipt-row">
                            <span><?= htmlspecialchars($detalle['nombre'] ?: 'Producto', ENT_QUOTES, 'UTF-8') ?> x<?= $cantidad ?></span>
                            <span>$<?= number_format($precioUnitario * $cantidad, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="receipt-row receipt-total">
                        <span>Total</span>
                        <span>$<?= number_format((float) $pedido['total'], 2) ?></span>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="id_pedido" value="<?= (int) $pedido['id_pedido'] ?>">
                    <input type="hidden" name="nuevo_estado" value="preparacion">
                    <button type="submit" class="btn-action start-btn">Preparar</button>
                </form>
            </article>
            <?php endforeach; ?>
        </section>

        <section class="kanban-column" id="col-preparacion">
            <h2>En Preparación <span>(<?= count($preparacion) ?>)</span></h2>
            <?php foreach ($preparacion as $pedido): ?>
            <?php $detallesPedido = obtenerDetallesPedido($conexion, $pedido['id_pedido']); ?>
            <article class="order-card in-progress">
                <header class="order-header">
                    <h3>Pedido #<?= (int) $pedido['id_pedido'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">
                    Cliente: <strong><?= htmlspecialchars(trim(($pedido['nombre_usuario'] ?? '') . ' ' . ($pedido['apellido_usuario'] ?? '')), ENT_QUOTES, 'UTF-8') ?: 'Cliente sin nombre' ?></strong>
                </div>
                <div class="order-receipt">
                    <?php foreach ($detallesPedido as $detalle): ?>
                        <?php $precioUnitario = (float) ($detalle['precio_unitario'] ?? 0); ?>
                        <?php $cantidad = (int) ($detalle['cantidad'] ?? 0); ?>
                        <div class="receipt-row">
                            <span><?= htmlspecialchars($detalle['nombre'] ?: 'Producto', ENT_QUOTES, 'UTF-8') ?> x<?= $cantidad ?></span>
                            <span>$<?= number_format($precioUnitario * $cantidad, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="receipt-row receipt-total">
                        <span>Total</span>
                        <span>$<?= number_format((float) $pedido['total'], 2) ?></span>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="id_pedido" value="<?= (int) $pedido['id_pedido'] ?>">
                    <input type="hidden" name="nuevo_estado" value="listo">
                    <button type="submit" class="btn-action finish-btn">Listo para Entrega</button>
                </form>
            </article>
            <?php endforeach; ?>
        </section>

        <section class="kanban-column" id="col-listos">
            <h2>Listos para Pago <span>(<?= count($listos) ?>)</span></h2>
            <?php foreach ($listos as $pedido): ?>
            <?php $detallesPedido = obtenerDetallesPedido($conexion, $pedido['id_pedido']); ?>
            <article class="order-card">
                <header class="order-header">
                    <h3>Pedido #<?= (int) $pedido['id_pedido'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">
                    Cliente: <strong><?= htmlspecialchars(trim(($pedido['nombre_usuario'] ?? '') . ' ' . ($pedido['apellido_usuario'] ?? '')), ENT_QUOTES, 'UTF-8') ?: 'Cliente sin nombre' ?></strong>
                </div>
                <div class="order-receipt">
                    <?php foreach ($detallesPedido as $detalle): ?>
                        <?php $precioUnitario = (float) ($detalle['precio_unitario'] ?? 0); ?>
                        <?php $cantidad = (int) ($detalle['cantidad'] ?? 0); ?>
                        <div class="receipt-row">
                            <span><?= htmlspecialchars($detalle['nombre'] ?: 'Producto', ENT_QUOTES, 'UTF-8') ?> x<?= $cantidad ?></span>
                            <span>$<?= number_format($precioUnitario * $cantidad, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="receipt-row receipt-total">
                        <span>Total</span>
                        <span>$<?= number_format((float) $pedido['total'], 2) ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </section>

    </main>

</body>
</html>
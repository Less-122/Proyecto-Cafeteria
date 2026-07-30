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
    // Forzamos la comparación insensible a mayúsculas/minúsculas usando LOWER()
    $stmt = $conexion->prepare("SELECT * FROM pedidos WHERE LOWER(estado) = LOWER(:estado) ORDER BY fecha_creacion ASC");
    $stmt->execute([':estado' => $estado]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pendientes  = obtenerPedidos($conexion, 'Pendiente'); // Asegúrate de que coincida con mayúsculas/minúsculas de tu DB
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
            <article class="order-card alert">
                <header class="order-header">
                    <!-- Corrección: id_pedido en lugar de id -->
                    <h3>Pedido #<?= $pedido['id_pedido'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <!-- Corrección: id_usuario_fk en lugar de cliente -->
                <div class="order-customer">Cliente: <strong>Usuario #<?= htmlspecialchars($pedido['id_usuario_fk']) ?></strong></div>
                <div class="order-items">
                    <?= nl2br(htmlspecialchars($pedido['detalle_pedido'])) ?>
                </div>
                <form method="POST">
                    <!-- Corrección: id_pedido en el input hidden -->
                    <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                    <input type="hidden" name="nuevo_estado" value="preparacion">
                    <button type="submit" class="btn-action start-btn">Preparar</button>
                </form>
            </article>
            <?php endforeach; ?>
        </section>

        <section class="kanban-column" id="col-preparacion">
            <h2>En Preparación <span>(<?= count($preparacion) ?>)</span></h2>
            <?php foreach ($preparacion as $pedido): ?>
            <article class="order-card in-progress">
                <header class="order-header">
                    <h3>Pedido #<?= $pedido['id_pedido'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">Cliente: <strong>Usuario #<?= htmlspecialchars($pedido['id_usuario_fk']) ?></strong></div>
                <div class="order-items">
                    <?= nl2br(htmlspecialchars($pedido['detalle_pedido'])) ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                    <input type="hidden" name="nuevo_estado" value="listo">
                    <button type="submit" class="btn-action finish-btn">Listo para Entrega</button>
                </form>
            </article>
            <?php endforeach; ?>
        </section>

        <section class="kanban-column" id="col-listos">
            <h2>Listos para Pago <span>(<?= count($listos) ?>)</span></h2>
            <?php foreach ($listos as $pedido): ?>
            <article class="order-card">
                <header class="order-header">
                    <h3>Pedido #<?= $pedido['id_pedido'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">Cliente: <strong>Usuario #<?= htmlspecialchars($pedido['id_usuario_fk']) ?></strong></div>
                <div class="order-items">
                    <?= nl2br(htmlspecialchars($pedido['detalle_pedido'])) ?>
                </div>
                <div class="order-total">
                    <strong>Total: $<?= number_format($pedido['total'], 2) ?></strong>
                </div>
            </article>
            <?php endforeach; ?>
        </section>

    </main>

</body>
</html>
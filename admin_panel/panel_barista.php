<?php
// 1. Conexión obligatoria a la base de datos
require_once '../conexion.php';

// 2. Lógica para procesar el cambio de estado mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'], $_POST['nuevo_estado'])) {
    $id_pedido = (int)$_POST['id_pedido'];
    $nuevo_estado = $_POST['nuevo_estado'];

    $sql = "UPDATE pedidos SET estado = :estado WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estado' => $nuevo_estado, ':id' => $id_pedido]);

    // Recargar la página para limpiar el formulario y evitar reenvíos
    header("Location: panel_barista.php");
    exit;
}


// 3. Obtener los pedidos separados por estado
function obtenerPedidos($pdo, $estado) {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE estado = :estado ORDER BY fecha_creacion ASC");
    $stmt->execute([':estado' => $estado]);
    return $stmt->fetchAll();
}

$pendientes  = obtenerPedidos($pdo, 'pendiente');
$preparacion = obtenerPedidos($pdo, 'preparacion');
$listos      = obtenerPedidos($pdo, 'listo');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Barista - Aroma a Café</title>
    <!-- Ruta corregida -->
    <link rel="stylesheet" href="../../css/barista.css">
</head>
<body class="admin-body">

    <!-- INCLUDES OBLIGATORIOS PARA MANTENER LA ESTRUCTURA -->
    <?php include 'admin_header.php'; ?>
    <?php include 'admin_menu.php'; ?>

    <header class="barista-header">
        <h1>Aroma a Café | Monitor de Pedidos</h1>
        <div class="status-indicator">
            <span class="dot pulse"></span> Conectado (Tiempo Real)
        </div>
    </header>

    <main class="kanban-board">
        
        <!-- Columna 1: Nuevos Pedidos (PENDIENTES) -->
        <section class="kanban-column" id="col-pendientes">
            <h2>Pendientes <span>(<?= count($pendientes) ?>)</span></h2>
            
            <?php foreach ($pendientes as $pedido): ?>
            <article class="order-card alert">
                <header class="order-header">
                    <h3>Pedido #<?= $pedido['id'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">Cliente: <strong><?= htmlspecialchars($pedido['cliente']) ?></strong></div>
                
                <div class="order-items">
                    <!-- Se muestra el detalle del pedido. nl2br respeta los saltos de línea -->
                    <?= nl2br(htmlspecialchars($pedido['detalle_pedido'])) ?>
                </div>
                
                <!-- Reemplazo del onclick por un formulario funcional -->
                <form method="POST">
                    <input type="hidden" name="id_pedido" value="<?= $pedido['id'] ?>">
                    <input type="hidden" name="nuevo_estado" value="preparacion">
                    <button type="submit" class="btn-action start-btn">Preparar</button>
                </form>
            </article>
            <?php endforeach; ?>

        </section>

        <!-- Columna 2: En Preparación -->
        <section class="kanban-column" id="col-preparacion">
            <h2>En Preparación <span>(<?= count($preparacion) ?>)</span></h2>
            
            <?php foreach ($preparacion as $pedido): ?>
            <article class="order-card in-progress">
                <header class="order-header">
                    <h3>Pedido #<?= $pedido['id'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">Cliente: <strong><?= htmlspecialchars($pedido['cliente']) ?></strong></div>
                
                <div class="order-items">
                    <?= nl2br(htmlspecialchars($pedido['detalle_pedido'])) ?>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="id_pedido" value="<?= $pedido['id'] ?>">
                    <input type="hidden" name="nuevo_estado" value="listo">
                    <button type="submit" class="btn-action finish-btn">Listo para Entrega</button>
                </form>
            </article>
            <?php endforeach; ?>

        </section>

        <!-- Columna 3: Listos para Entrega / Pagar -->
        <section class="kanban-column" id="col-listos">
            <h2>Listos para Pago <span>(<?= count($listos) ?>)</span></h2>
            
            <?php foreach ($listos as $pedido): ?>
            <article class="order-card">
                <header class="order-header">
                    <h3>Pedido #<?= $pedido['id'] ?></h3>
                    <time><?= date('h:i A', strtotime($pedido['fecha_creacion'])) ?></time>
                </header>
                <div class="order-customer">Cliente: <strong><?= htmlspecialchars($pedido['cliente']) ?></strong></div>
                
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
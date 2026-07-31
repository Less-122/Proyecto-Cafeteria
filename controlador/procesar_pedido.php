<?php
// Iniciar sesión para obtener el ID del usuario autenticado
session_start();
require_once '../config/conexion.php';
header('Content-Type: application/json');


$id_usuario = (int)($_SESSION['id_usuario'] ?? 0);

if ($id_usuario <= 0) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para confirmar el pedido.']);
    exit;
}

// Leer el JSON enviado por fetch
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos || empty($datos['carrito'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos o carrito vacío.']);
    exit;
}

$carrito = $datos['carrito'];
$total = floatval($datos['total']);

$carritoNormalizado = [];

foreach ($carrito as $item) {
    $idProducto = null;
    if (isset($item['id_producto'])) {
        $idProducto = filter_var($item['id_producto'], FILTER_VALIDATE_INT);
    } elseif (isset($item['id'])) {
        $idProducto = filter_var($item['id'], FILTER_VALIDATE_INT);
    }

    $cantidad = isset($item['cantidad']) ? max(1, (int) $item['cantidad']) : 1;
    $precioUnitario = isset($item['precio_unitario'])
        ? floatval($item['precio_unitario'])
        : (isset($item['precioFinal']) ? floatval($item['precioFinal']) : floatval($item['precio'] ?? 0));

    if ($idProducto === false || $idProducto <= 0) {
        echo json_encode(['success' => false, 'message' => 'Carrito con datos incompletos.']);
        exit;
    }

    $carritoNormalizado[] = [
        'id_producto' => $idProducto,
        'cantidad' => $cantidad,
        'precio_unitario' => $precioUnitario
    ];
}

if ($carritoNormalizado === []) {
    echo json_encode(['success' => false, 'message' => 'Carrito con datos incompletos.']);
    exit;
}

// Generar clave de retiro (6 dígitos)
$clave_retiro = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

try {
    $conexion->beginTransaction();

    // Insertar el pedido
    $sqlPedido = "INSERT INTO pedidos (id_usuario, fecha_vencimiento, fecha_creacion, clave_retiro, total, estado)
                  VALUES (?, DATE_ADD(NOW(), INTERVAL 1 DAY), NOW(), ?, ?, 'pendiente')";
    $stmtPedido = $conexion->prepare($sqlPedido);
    $stmtPedido->execute([$id_usuario, $clave_retiro, $total]);
    $id_pedido = $conexion->lastInsertId();

    // Insertar los detalles del pedido
    $sqlDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                   VALUES (?, ?, ?, ?)";
    $stmtDetalle = $conexion->prepare($sqlDetalle);
    foreach ($carritoNormalizado as $item) {
        $stmtDetalle->execute([
            $id_pedido,
            $item['id_producto'],
            $item['cantidad'],
            $item['precio_unitario']
        ]);
    }

    $conexion->commit();

    // Respuesta exitosa incluyendo la clave de retiro
    echo json_encode([
        'success' => true,
        'id_pedido' => $id_pedido,
        'clave_retiro' => $clave_retiro
    ]);

} catch (PDOException $e) {
    $conexion->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
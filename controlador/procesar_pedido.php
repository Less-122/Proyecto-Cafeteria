<?php
// Iniciar sesión para obtener el ID del usuario autenticado
session_start();
require_once '../config/conexion.php';
header('Content-Type: application/json');


$id_usuario = (int)$_SESSION['id_usuario'];

// Leer el JSON enviado por fetch
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos || empty($datos['carrito'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos o carrito vacío.']);
    exit;
}

$carrito = $datos['carrito'];
$total = floatval($datos['total']);

// Validar que cada producto tenga los campos necesarios
foreach ($carrito as $item) {
    if (!isset($item['id_producto'], $item['cantidad'], $item['precio_unitario'])) {
        echo json_encode(['success' => false, 'message' => 'Carrito con datos incompletos.']);
        exit;
    }
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
    foreach ($carrito as $item) {
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
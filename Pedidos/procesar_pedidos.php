<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../config/conexion.php';

/* 1. Verificar que el usuario tenga sesión iniciada */
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para confirmar un pedido.'
    ]);
    exit;
}

$idUsuario = (int) $_SESSION['id_usuario'];

/* 2. Leer y validar el JSON enviado desde carrito.js */
$datos = json_decode(file_get_contents('php://input'), true);

if (
    empty($datos)
    || empty($datos['carrito'])
    || !is_array($datos['carrito'])
) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'El carrito está vacío o los datos son inválidos.'
    ]);
    exit;
}

$carrito = $datos['carrito'];

try {
    $conexion->beginTransaction();

    /* 3. Buscar el id_producto real de cada artículo por su nombre */
    $stmtBuscarProducto = $conexion->prepare(
        "SELECT id_producto, precio, precio_descuento, tiene_promocion
         FROM productos
         WHERE nombre = :nombre
         LIMIT 1"
    );

    $itemsValidados = [];
    $totalCalculado = 0;

    foreach ($carrito as $item) {

        $nombre = trim($item['nombre'] ?? '');
        $cantidad = (int) ($item['cantidad'] ?? 0);

        if ($nombre === '' || $cantidad <= 0) {
            throw new Exception("Producto inválido en el carrito.");
        }

        $stmtBuscarProducto->execute(['nombre' => $nombre]);
        $producto = $stmtBuscarProducto->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception("No se encontró el producto \"$nombre\" en el catálogo.");
        }

        /* Usamos el precio con descuento si aplica, si no el precio normal */
        $precioUnitario =
            (!empty($producto['tiene_promocion']) && !empty($producto['precio_descuento']))
                ? (float) $producto['precio_descuento']
                : (float) $producto['precio'];

        $subtotalItem = $precioUnitario * $cantidad;
        $totalCalculado += $subtotalItem;

        $itemsValidados[] = [
            'id_producto' => (int) $producto['id_producto'],
            'nombre'      => $nombre,
            'cantidad'    => $cantidad,
            'precio'      => $precioUnitario,
        ];
    }

    /* 4. Generar una clave de retiro única de 6 dígitos */
    $stmtVerificarClave = $conexion->prepare(
        "SELECT COUNT(*) FROM pedidos WHERE clave_retiro = :clave"
    );

    do {
        $claveRetiro = (string) random_int(100000, 999999);
        $stmtVerificarClave->execute(['clave' => $claveRetiro]);
        $existe = (int) $stmtVerificarClave->fetchColumn();
    } while ($existe > 0);

    /* 5. Preparar fechas y resumen del pedido */
    $fechaPedido = date('Y-m-d');
    $fechaVencimiento = date('Y-m-d', strtotime('+1 day'));

    $detalleTexto = implode(', ', array_map(
        fn($i) => "{$i['cantidad']}x {$i['nombre']}",
        $itemsValidados
    ));

    /* 6. Insertar el pedido */
    $stmtPedido = $conexion->prepare(
        "INSERT INTO pedidos
            (id_usuario_fk, detalle_pedido, fecha_pedido, fecha_vencimiento, clave_retiro, total, estado, fecha_creacion)
         VALUES
            (:id_usuario, :detalle, :fecha_pedido, :fecha_vencimiento, :clave_retiro, :total, 'pendiente', NOW())"
    );

    $stmtPedido->execute([
        'id_usuario'        => $idUsuario,
        'detalle'           => $detalleTexto,
        'fecha_pedido'      => $fechaPedido,
        'fecha_vencimiento' => $fechaVencimiento,
        'clave_retiro'      => $claveRetiro,
        'total'             => $totalCalculado,
    ]);

    $idPedido = (int) $conexion->lastInsertId();

    /* 7. Insertar cada producto del pedido en detalles_pedido */
    $stmtDetalle = $conexion->prepare(
        "INSERT INTO detalles_pedido (id_pedido_fk, id_producto_fk, cantidad, precio_unitario)
         VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)"
    );

    foreach ($itemsValidados as $item) {
        $stmtDetalle->execute([
            'id_pedido'        => $idPedido,
            'id_producto'      => $item['id_producto'],
            'cantidad'         => $item['cantidad'],
            'precio_unitario'  => $item['precio'],
        ]);
    }

    $conexion->commit();

    echo json_encode([
        'success'      => true,
        'id_pedido'    => $idPedido,
        'clave_retiro' => $claveRetiro,
        'total'        => number_format($totalCalculado, 2),
    ]);

} catch (Exception $e) {
    $conexion->rollBack();

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
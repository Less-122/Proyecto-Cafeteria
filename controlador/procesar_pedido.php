<?php
require_once '../config/conexion.php';
header('Content-Type: application/json');

// Leer el JSON enviado por fetch
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos || empty($datos['carrito'])) {
    echo json_encode(['success' => false, 'message' => 'Datos de pedido inválidos o carrito vacío.']);
    exit;
}

$carrito = $datos['carrito'];
$total = floatval($datos['total']);

// Asignamos un ID genérico (ej. 1) hasta que vincules las sesiones de tus usuarios reales
$id_usuario_fk = 1; 

// Formatear el detalle del pedido 
$detalle_pedido = "";
foreach ($carrito as $item) {
    $detalle_pedido .= $item['cantidad'] . "x " . $item['nombre'] . " ($" . $item['precio'] . ")\n";
}

// Generamos una clave de retiro aleatoria de 6 dígitos
$clave_retiro = rand(100000, 999999);

try {
    // La consulta ahora respeta estrictamente los nombres de columna de tu tabla
    $sql = "INSERT INTO pedidos (id_usuario_fk, detalle_pedido, fecha_pedido, clave_retiro, total, estado, fecha_creacion) 
            VALUES (:id_usuario, :detalle, CURDATE(), :clave, :total, 'Pendiente', NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $id_usuario_fk,
        ':detalle' => trim($detalle_pedido),
        ':clave' => $clave_retiro,
        ':total' => $total
    ]);

    $id_pedido = $conexion->lastInsertId();

    echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()]);
}
?>
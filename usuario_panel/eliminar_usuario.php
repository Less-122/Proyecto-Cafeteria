<?php
// Aseguramos que no se imprima ningún texto extra antes del JSON
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Determinar la ruta de conexión
$ruta_conexion = file_exists('../config/conexion.php') ? '../config/conexion.php' : (file_exists('../conexion.php') ? '../conexion.php' : null);

if (!$ruta_conexion) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'No se encontró conexion.php']);
    exit;
}

require_once $ruta_conexion;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;

    if (!$id_usuario) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'ID de usuario no recibido.']);
        exit;
    }

    try {
        // Intentamos borrar primero en tablas dependientes si existen
        try {
            $stmtP = $conexion->prepare("DELETE FROM pedidos WHERE id_usuario = :id");
            $stmtP->execute([':id' => $id_usuario]);
        } catch (Exception $e) {}

        // Eliminamos al usuario
        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        $stmt->execute([':id' => $id_usuario]);

        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente.']);
        exit;

    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()]);
        exit;
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}
?>
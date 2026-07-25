<?php
header('Content-Type: application/json');
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;
    $nombre     = trim($_POST['nombre'] ?? '');
    $apellido   = trim($_POST['apellido'] ?? '');
    $telefono   = trim($_POST['telefono'] ?? '');
    $password   = trim($_POST['password'] ?? '');

    if (!$id_usuario || empty($nombre) || empty($apellido) || empty($telefono)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        exit;
    }

    try {
        if (!empty($password)) {
            // Se actualiza incluyendo la contraseña si se proporcionó una nueva
            $passHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conexion->prepare("UPDATE usuarios SET nombre = :nombre, apellido = :apellido, telefono = :telefono, password = :password WHERE id_usuario = :id");
            $stmt->bindParam(':password', $passHash);
        } else {
            // Se actualiza sin modificar la contraseña existente
            $stmt = $conexion->prepare("UPDATE usuarios SET nombre = :nombre, apellido = :apellido, telefono = :telefono WHERE id_usuario = :id");
        }

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
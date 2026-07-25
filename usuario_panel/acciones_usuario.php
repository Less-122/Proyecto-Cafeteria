<?php
include '../conexion.php';

header('Content-Type: application/json');
$accion = $_POST['accion'] ?? '';

if ($accion === 'modificar') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // Si ingresó una nueva contraseña, la encriptamos y la actualizamos
        $passHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, password = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $res = $stmt->execute([$nombre, $apellido, $telefono, $passHash, $id]);
    } else {
        // Si dejó el campo de contraseña vacío, conservamos la que ya tenía
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $res = $stmt->execute([$nombre, $apellido, $telefono, $id]);
    }

    echo json_encode(['success' => $res]);
    exit;
}

if ($accion === 'eliminar') {
    $id = $_POST['id_usuario'];

    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $res = $stmt->execute([$id]);

    echo json_encode(['success' => $res]);
    exit;
}
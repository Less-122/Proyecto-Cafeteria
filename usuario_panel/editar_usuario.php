<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Determinar la ruta de conexión
$ruta_conexion = file_exists('../config/conexion.php') ? '../config/conexion.php' : (file_exists('../conexion.php') ? '../conexion.php' : (file_exists('conexion.php') ? 'conexion.php' : null));

if (!$ruta_conexion) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'No se encontró el archivo conexion.php']);
    exit;
}

require_once $ruta_conexion;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Los nombres deben coincidir con los 'name' del formulario en usuarios.php
    $id_usuario = $_POST['id_usuario'];
    $nombre     = $_POST['nombre'];
    $apellido   = $_POST['apellido'];
    $correo     = $_POST['correo'];
    $password   = $_POST['password']; // puede venir vacío
    $rol        = $_POST['rol'];

    if (!$id_usuario) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado.']);
        exit;
    }

    try {
        // Verificar si se va a actualizar la contraseña o mantener la actual
        if (!empty(trim($contra))) {
            // Si la contraseña no está vacía, la encriptamos/actualizamos
            // Si en tu BD guardas contraseñas en texto plano usa $contra directamente, o password_hash() si usas hash
            $sql = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo, contrasena = :contra, rol = :rol WHERE id_usuario = :id";
            $params = [
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':correo' => $correo,
                ':contra'   => $contra, // o password_hash($contra, PASSWORD_BCRYPT)
                ':id'       => $id_usuario
            ];
        } else {
            // Si no introdujo nueva contraseña, mantenemos la existente
            $sql = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo, rol = :rol WHERE id_usuario = :id";
            $params = [
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':correo' => $correo,
                ':rol' => $rol,
                ':id'       => $id_usuario
            ];
        }

        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);

        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
        exit;

    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        exit;
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit;
}
?>
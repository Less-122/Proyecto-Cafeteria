<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../admin_panel/seguridad_admin.php';

header('Content-Type: application/json');

$operacion = $_POST['operacion'] ?? '';
$respuesta = ['success' => false, 'message' => 'Operación no válida'];

switch ($operacion) {

    case 'agregar':
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (strlen($nombre) < 3) {
            $respuesta['message'] = 'El nombre debe tener al menos 3 caracteres.';
            break;
        }

        $stmt = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $respuesta['message'] = 'Ya existe una categoría con ese nombre.';
            $stmt->close();
            break;
        }
        $stmt->close();

        $stmt = $conexion->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $descripcion);
        if ($stmt->execute()) {
            $respuesta = ['success' => true, 'message' => 'Categoría agregada correctamente.'];
        } else {
            $respuesta['message'] = 'Error al guardar: ' . $stmt->error;
        }
        $stmt->close();
        break;

    case 'editar':
        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($id <= 0) {
            $respuesta['message'] = 'ID de categoría inválido.';
            break;
        }
        if (strlen($nombre) < 3) {
            $respuesta['message'] = 'El nombre debe tener al menos 3 caracteres.';
            break;
        }

        // Verificar que el nombre no exista en otra categoría
        $stmt = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_categoria != ?");
        $stmt->bind_param("si", $nombre, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $respuesta['message'] = 'Ya existe otra categoría con ese nombre.';
            $stmt->close();
            break;
        }
        $stmt->close();

        $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        if ($stmt->execute()) {
            $respuesta = ['success' => true, 'message' => 'Categoría actualizada correctamente.'];
        } else {
            $respuesta['message'] = 'Error al actualizar: ' . $stmt->error;
        }
        $stmt->close();
        break;

    case 'eliminar':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            $respuesta['message'] = 'ID de categoría inválido.';
            break;
        }

        // Verificar si hay productos asociados
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM productos WHERE id_categoria_fk = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        if ($total > 0) {
            $respuesta['message'] = "No se puede eliminar la categoría porque tiene $total producto(s) asociado(s).";
            break;
        }

        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $respuesta = ['success' => true, 'message' => 'Categoría eliminada correctamente.'];
        } else {
            $respuesta['message'] = 'Error al eliminar: ' . $stmt->error;
        }
        $stmt->close();
        break;

    default:
        $respuesta['message'] = 'Operación no reconocida.';
}

echo json_encode($respuesta);
exit;
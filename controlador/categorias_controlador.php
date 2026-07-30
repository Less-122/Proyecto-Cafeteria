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

        // PDO: Verificar si existe
        $stmt = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ?");
        $stmt->execute([$nombre]);
        
        if ($stmt->rowCount() > 0) {
            $respuesta['message'] = 'Ya existe una categoría con ese nombre.';
            break;
        }

        // PDO: Insertar
        $stmt = $conexion->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        if ($stmt->execute([$nombre, $descripcion])) {
            $respuesta = ['success' => true, 'message' => 'Categoría agregada correctamente.'];
        } else {
            $errorInfo = $stmt->errorInfo();
            $respuesta['message'] = 'Error al guardar: ' . $errorInfo[2];
        }
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

        // PDO: Verificar que el nombre no exista en otra categoría
        $stmt = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_categoria != ?");
        $stmt->execute([$nombre, $id]);
        
        if ($stmt->rowCount() > 0) {
            $respuesta['message'] = 'Ya existe otra categoría con ese nombre.';
            break;
        }

        // PDO: Actualizar
        $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
        if ($stmt->execute([$nombre, $descripcion, $id])) {
            $respuesta = ['success' => true, 'message' => 'Categoría actualizada correctamente.'];
        } else {
            $errorInfo = $stmt->errorInfo();
            $respuesta['message'] = 'Error al actualizar: ' . $errorInfo[2];
        }
        break;

    case 'eliminar':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            $respuesta['message'] = 'ID de categoría inválido.';
            break;
        }

        // PDO: Verificar si hay productos asociados usando fetchColumn()
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM productos WHERE id_categoria = ?");
        $stmt->execute([$id]);
        $total = $stmt->fetchColumn();

        if ($total > 0) {
            $respuesta['message'] = "No se puede eliminar la categoría porque tiene $total producto(s) asociado(s).";
            break;
        }

        // PDO: Eliminar
        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        if ($stmt->execute([$id])) {
            $respuesta = ['success' => true, 'message' => 'Categoría eliminada correctamente.'];
        } else {
            $errorInfo = $stmt->errorInfo();
            $respuesta['message'] = 'Error al eliminar: ' . $errorInfo[2];
        }
        break;

    default:
        $respuesta['message'] = 'Operación no reconocida.';
}

echo json_encode($respuesta);
exit;
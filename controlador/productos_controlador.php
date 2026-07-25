<?php

require_once '../usuario_panel/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_panel/productos.php');
    exit;
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'crear':
        crearProducto($conexion);
        break;

    case 'modificar':
        modificarProducto($conexion);
        break;

    default:
        die('Acción no válida.');
}


/* =====================================================
   CREAR PRODUCTO
===================================================== */

function crearProducto(PDO $conexion): void
{
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCategoria = (int) ($_POST['categoria'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $precio = (float) ($_POST['precio'] ?? 0);
    $tienePromocion = isset($_POST['promocion']) ? 1 : 0;

    validarProducto(
        $nombre,
        $idCategoria,
        $stock,
        $precio
    );

    $imagenUrl = procesarImagen();

    $sql = "
        INSERT INTO productos (
            nombre,
            descripcion,
            id_categoria_fk,
            precio,
            stock,
            imagen_url,
            tiene_promocion
        )
        VALUES (
            :nombre,
            :descripcion,
            :categoria,
            :precio,
            :stock,
            :imagen_url,
            :promocion
        )
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':categoria' => $idCategoria,
        ':precio' => $precio,
        ':stock' => $stock,
        ':imagen_url' => $imagenUrl,
        ':promocion' => $tienePromocion
    ]);

    header(
        'Location: ../admin_panel/productos.php?creado=1'
    );
    exit;
}


/* =====================================================
   MODIFICAR PRODUCTO
===================================================== */

function modificarProducto(PDO $conexion): void
{
    $idProducto = (int) ($_POST['id_producto'] ?? 0);

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCategoria = (int) ($_POST['categoria'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $precio = (float) ($_POST['precio'] ?? 0);
    $tienePromocion = isset($_POST['promocion']) ? 1 : 0;

    if ($idProducto <= 0) {
        die('Producto no válido.');
    }

    validarProducto(
        $nombre,
        $idCategoria,
        $stock,
        $precio
    );

    $sqlActual = "
        SELECT imagen_url
        FROM productos
        WHERE id_producto = :id
    ";

    $stmtActual = $conexion->prepare($sqlActual);

    $stmtActual->execute([
        ':id' => $idProducto
    ]);

    $productoActual =
        $stmtActual->fetch(PDO::FETCH_ASSOC);

    if (!$productoActual) {
        die('El producto no existe.');
    }

    $imagenUrl = $productoActual['imagen_url'];

    /*
     * Si seleccionaron una imagen nueva,
     * procesarImagen() devuelve su nuevo nombre.
     */
    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK
    ) {
        $imagenAnterior = $imagenUrl;

        $imagenUrl = procesarImagen();

        eliminarImagenAnterior($imagenAnterior);
    }

    $sql = "
        UPDATE productos
        SET
            nombre = :nombre,
            descripcion = :descripcion,
            id_categoria_fk = :categoria,
            precio = :precio,
            stock = :stock,
            imagen_url = :imagen_url,
            tiene_promocion = :promocion
        WHERE id_producto = :id
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':categoria' => $idCategoria,
        ':precio' => $precio,
        ':stock' => $stock,
        ':imagen_url' => $imagenUrl,
        ':promocion' => $tienePromocion,
        ':id' => $idProducto
    ]);

    header(
        'Location: ../admin_panel/productos.php?modificado=1'
    );
    exit;
}


/* =====================================================
   VALIDACIONES
===================================================== */

function validarProducto(
    string $nombre,
    int $idCategoria,
    int $stock,
    float $precio
): void {
    if ($nombre === '') {
        die('El nombre es obligatorio.');
    }

    if ($idCategoria <= 0) {
        die('Selecciona una categoría válida.');
    }

    if ($stock < 0) {
        die('El stock no puede ser negativo.');
    }

    if ($precio < 0) {
        die('El precio no puede ser negativo.');
    }
}


/* =====================================================
   SUBIR IMAGEN
===================================================== */

function procesarImagen(): string
{
    if (
        !isset($_FILES['imagen']) ||
        $_FILES['imagen']['error'] !== UPLOAD_ERR_OK
    ) {
        die('Debes seleccionar una imagen válida.');
    }

    $archivoTemporal = $_FILES['imagen']['tmp_name'];
    $nombreOriginal = $_FILES['imagen']['name'];

    $extension = strtolower(
        pathinfo($nombreOriginal, PATHINFO_EXTENSION)
    );

    $extensionesPermitidas = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    if (!in_array(
        $extension,
        $extensionesPermitidas,
        true
    )) {
        die('El formato de imagen no está permitido.');
    }

    $nombreImagen =
        uniqid('producto_', true) . '.' . $extension;

    $carpetaDestino = '../img/productos/';

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $rutaDestino =
        $carpetaDestino . $nombreImagen;

    if (!move_uploaded_file(
        $archivoTemporal,
        $rutaDestino
    )) {
        die('No se pudo guardar la imagen.');
    }

    return $nombreImagen;
}


/* =====================================================
   ELIMINAR IMAGEN ANTERIOR
===================================================== */

function eliminarImagenAnterior(?string $imagen): void
{
    if (empty($imagen)) {
        return;
    }

    $ruta = '../img/productos/' . $imagen;

    if (is_file($ruta)) {
        unlink($ruta);
    }
}
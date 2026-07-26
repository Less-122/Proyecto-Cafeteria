<?php

require_once '../config/conexion.php';


/* =====================================================
   SOLO PERMITIR PETICIONES POST
===================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_panel/productos.php');
    exit;
}


/* =====================================================
   DETERMINAR ACCIÓN
===================================================== */

$accion = $_POST['accion'] ?? '';

switch ($accion) {

    case 'crear':
        crearProducto($conexion);
        break;

    case 'modificar':
        modificarProducto($conexion);
        break;

    case 'eliminar':
        eliminarProducto($conexion);
        break;

    default:
        die('Acción no válida.');
}


/* =====================================================
   CREAR PRODUCTO
===================================================== */

function crearProducto(mysqli $conexion): void
{
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    $idCategoria = (int) ($_POST['categoria'] ?? 0);

    $stock = (int) ($_POST['stock'] ?? 0);

    $precio = (float) ($_POST['precio'] ?? 0);

    $tienePromocion =
        isset($_POST['promocion']) ? 1 : 0;


    /* Validar información */
    validarProducto(
        $nombre,
        $idCategoria,
        $stock,
        $precio
    );


    /* Procesar imagen */
    $imagenUrl = procesarImagen($idCategoria);


    /* Insertar producto */
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
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexion->prepare($sql);


    if (!$stmt) {
        die(
            'Error al preparar la consulta: '
            . $conexion->error
        );
    }


    $stmt->bind_param(
        "ssidisi",
        $nombre,
        $descripcion,
        $idCategoria,
        $precio,
        $stock,
        $imagenUrl,
        $tienePromocion
    );


    if (!$stmt->execute()) {

        /*
         * Si falla el INSERT, eliminar la imagen
         * que acabamos de subir.
         */
        eliminarImagenAnterior($imagenUrl);

        die(
            'Error al guardar el producto: '
            . $stmt->error
        );
    }


    $stmt->close();


    header(
        'Location: ../admin_panel/productos.php?creado=1'
    );

    exit;
}


/* =====================================================
   MODIFICAR PRODUCTO
===================================================== */

function modificarProducto(mysqli $conexion): void
{
    $idProducto =
        (int) ($_POST['id_producto'] ?? 0);

    $nombre =
        trim($_POST['nombre'] ?? '');

    $descripcion =
        trim($_POST['descripcion'] ?? '');

    $idCategoria =
        (int) ($_POST['categoria'] ?? 0);

    $stock =
        (int) ($_POST['stock'] ?? 0);

    $precio =
        (float) ($_POST['precio'] ?? 0);

    $tienePromocion =
        isset($_POST['promocion']) ? 1 : 0;


    if ($idProducto <= 0) {
        die('Producto no válido.');
    }


    validarProducto(
        $nombre,
        $idCategoria,
        $stock,
        $precio
    );


    /* =================================================
       BUSCAR PRODUCTO ACTUAL
    ================================================= */

    $sqlActual = "
        SELECT imagen_url
        FROM productos
        WHERE id_producto = ?
    ";

    $stmtActual =
        $conexion->prepare($sqlActual);


    if (!$stmtActual) {
        die(
            'Error al buscar el producto: '
            . $conexion->error
        );
    }


    $stmtActual->bind_param(
        "i",
        $idProducto
    );


    $stmtActual->execute();


    $resultadoActual =
        $stmtActual->get_result();


    $productoActual =
        $resultadoActual->fetch_assoc();


    $stmtActual->close();


    if (!$productoActual) {
        die('El producto no existe.');
    }


    /*
     * Por defecto conservamos la imagen
     * que ya tenía el producto.
     */
    $imagenUrl =
        $productoActual['imagen_url'];

    $imagenAnterior = $imagenUrl;

    $hayNuevaImagen = false;


    /* =================================================
       NUEVA IMAGEN
    ================================================= */

    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK
    ) {

        $imagenUrl = procesarImagen($idCategoria);

        $hayNuevaImagen = true;
    }


    /* =================================================
       ACTUALIZAR PRODUCTO
    ================================================= */

    $sql = "
        UPDATE productos
        SET
            nombre = ?,
            descripcion = ?,
            id_categoria_fk = ?,
            precio = ?,
            stock = ?,
            imagen_url = ?,
            tiene_promocion = ?
        WHERE id_producto = ?
    ";


    $stmt = $conexion->prepare($sql);


    if (!$stmt) {

        if ($hayNuevaImagen) {
            eliminarImagenAnterior($imagenUrl);
        }

        die(
            'Error al preparar la actualización: '
            . $conexion->error
        );
    }


    $stmt->bind_param(
        "ssidisii",
        $nombre,
        $descripcion,
        $idCategoria,
        $precio,
        $stock,
        $imagenUrl,
        $tienePromocion,
        $idProducto
    );


    if (!$stmt->execute()) {

        /*
         * Si falla la actualización,
         * borramos solamente la nueva imagen.
         */
        if ($hayNuevaImagen) {
            eliminarImagenAnterior($imagenUrl);
        }

        die(
            'Error al modificar el producto: '
            . $stmt->error
        );
    }


    $stmt->close();


    /*
     * Solo después de actualizar correctamente
     * eliminamos la imagen anterior.
     */
    if (
        $hayNuevaImagen &&
        $imagenAnterior !== $imagenUrl
    ) {

        eliminarImagenAnterior(
            $imagenAnterior
        );
    }


    header(
        'Location: ../admin_panel/productos.php?modificado=1'
    );

    exit;
}


/* =====================================================
   ELIMINAR PRODUCTO
===================================================== */

function eliminarProducto(mysqli $conexion): void
{
    $idProducto =
        (int) ($_POST['id_producto'] ?? 0);


    if ($idProducto <= 0) {
        die('Producto no válido.');
    }


    /* =================================================
       BUSCAR PRODUCTO
    ================================================= */

    $sqlBuscar = "
        SELECT imagen_url
        FROM productos
        WHERE id_producto = ?
    ";


    $stmtBuscar =
        $conexion->prepare($sqlBuscar);


    if (!$stmtBuscar) {
        die(
            'Error al buscar el producto: '
            . $conexion->error
        );
    }


    $stmtBuscar->bind_param(
        "i",
        $idProducto
    );


    $stmtBuscar->execute();


    $resultado =
        $stmtBuscar->get_result();


    $producto =
        $resultado->fetch_assoc();


    $stmtBuscar->close();


    if (!$producto) {
        die('El producto no existe.');
    }


    /* =================================================
       ELIMINAR PRODUCTO
    ================================================= */

    $sqlEliminar = "
        DELETE FROM productos
        WHERE id_producto = ?
    ";


    $stmtEliminar =
        $conexion->prepare($sqlEliminar);


    if (!$stmtEliminar) {
        die(
            'Error al preparar la eliminación: '
            . $conexion->error
        );
    }


    $stmtEliminar->bind_param(
        "i",
        $idProducto
    );


    try {

        $stmtEliminar->execute();

    } catch (mysqli_sql_exception $e) {

        die(
            'No se pudo eliminar el producto. '
            . 'Es posible que esté relacionado '
            . 'con algún pedido.'
        );
    }


    $stmtEliminar->close();


    /* =================================================
       ELIMINAR IMAGEN
    ================================================= */

    if (!empty($producto['imagen_url'])) {

        eliminarImagenAnterior(
            $producto['imagen_url']
        );
    }


    header(
        'Location: ../admin_panel/productos.php?eliminado=1'
    );

    exit;
}


/* =====================================================
   VALIDAR PRODUCTO
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
        die(
            'Selecciona una categoría válida.'
        );
    }


    if ($stock < 0) {
        die(
            'El stock no puede ser negativo.'
        );
    }


    if ($precio < 0) {
        die(
            'El precio no puede ser negativo.'
        );
    }
}


/* =====================================================
   PROCESAR IMAGEN
===================================================== */

function procesarImagen(int $idCategoria): string
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
        pathinfo(
            $nombreOriginal,
            PATHINFO_EXTENSION
        )
    );

    $extensionesPermitidas = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    if (
        !in_array(
            $extension,
            $extensionesPermitidas,
            true
        )
    ) {
        die('El formato de imagen no está permitido.');
    }


    /* ================================================
       DETERMINAR CARPETA SEGÚN CATEGORÍA
    ================================================= */

    switch ($idCategoria) {

        case 1:
            $carpetaCategoria = 'Bebidas-calientes';
            break;

        case 2:
            $carpetaCategoria = 'Bebidas-frias';
            break;

        case 3:
            $carpetaCategoria = 'Postres';
            break;

        default:
            die('Categoría no válida.');
    }


    /* Crear nombre único */
    $nombreImagen =
        uniqid('producto_', true)
        . '.'
        . $extension;


    /* Carpeta física */
    $carpetaDestino =
        '../img/productos/'
        . $carpetaCategoria
        . '/';


    /* Crear carpeta si no existe */
    if (!is_dir($carpetaDestino)) {

        mkdir(
            $carpetaDestino,
            0777,
            true
        );
    }


    $rutaDestino =
        $carpetaDestino
        . $nombreImagen;


    /* Guardar archivo */
    if (
        !move_uploaded_file(
            $archivoTemporal,
            $rutaDestino
        )
    ) {
        die('No se pudo guardar la imagen.');
    }


    /*
     * Esto es lo que guardamos en MySQL.
     *
     * Ejemplo:
     * Bebidas-calientes/producto_123.jpeg
     */
    return $carpetaCategoria . '/' . $nombreImagen;
}


/* =====================================================
   ELIMINAR IMAGEN
===================================================== */

function eliminarImagenAnterior(
    ?string $imagen
): void {

    if (empty($imagen)) {
        return;
    }
    $ruta =
        '../img/productos/'
        . $imagen;
    if (is_file($ruta)) {
        unlink($ruta);
    }
}
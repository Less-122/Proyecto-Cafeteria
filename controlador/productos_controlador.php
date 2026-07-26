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

function crearProducto(PDO $conexion): void
{
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    $idCategoria = (int) ($_POST['categoria'] ?? 0);

    $stock = (int) ($_POST['stock'] ?? 0);

    $precio = (float) ($_POST['precio'] ?? 0);

    $tienePromocion =
        isset($_POST['promocion']) ? 1 : 0;


    /* Validar datos */
    validarProducto(
        $nombre,
        $idCategoria,
        $stock,
        $precio
    );


    /* Guardar imagen en su carpeta correspondiente */
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
        VALUES (
            :nombre,
            :descripcion,
            :categoria,
            :precio,
            :stock,
            :imagen,
            :promocion
        )
    ";


    try {

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':categoria' => $idCategoria,
            ':precio' => $precio,
            ':stock' => $stock,
            ':imagen' => $imagenUrl,
            ':promocion' => $tienePromocion
        ]);

    } catch (PDOException $e) {

        /* Si falla el INSERT, borrar la imagen subida */
        eliminarImagenAnterior($imagenUrl);

        die(
            'Error al guardar el producto: '
            . $e->getMessage()
        );
    }


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
        WHERE id_producto = :id
    ";

    $stmtActual =
        $conexion->prepare($sqlActual);

    $stmtActual->execute([
        ':id' => $idProducto
    ]);

    $productoActual =
        $stmtActual->fetch(PDO::FETCH_ASSOC);


    if (!$productoActual) {
        die('El producto no existe.');
    }


    /* Conservar imagen actual */
    $imagenUrl =
        $productoActual['imagen_url'];

    $imagenAnterior =
        $productoActual['imagen_url'];

    $hayNuevaImagen = false;


    /* =================================================
       SI SE SUBIÓ UNA NUEVA IMAGEN
    ================================================= */

    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK
    ) {

        $imagenUrl =
            procesarImagen($idCategoria);

        $hayNuevaImagen = true;
    }


    /* =================================================
       ACTUALIZAR PRODUCTO
    ================================================= */

    $sql = "
        UPDATE productos
        SET
            nombre = :nombre,
            descripcion = :descripcion,
            id_categoria_fk = :categoria,
            precio = :precio,
            stock = :stock,
            imagen_url = :imagen,
            tiene_promocion = :promocion
        WHERE id_producto = :id
    ";


    try {

        $stmt =
            $conexion->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':categoria' => $idCategoria,
            ':precio' => $precio,
            ':stock' => $stock,
            ':imagen' => $imagenUrl,
            ':promocion' => $tienePromocion,
            ':id' => $idProducto
        ]);

    } catch (PDOException $e) {

        /*
         * Si se había subido una imagen nueva
         * pero falló el UPDATE, eliminarla.
         */
        if ($hayNuevaImagen) {
            eliminarImagenAnterior($imagenUrl);
        }

        die(
            'Error al modificar el producto: '
            . $e->getMessage()
        );
    }


    /*
     * Si se actualizó correctamente y había
     * una imagen nueva, borrar la anterior.
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

function eliminarProducto(PDO $conexion): void
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
        WHERE id_producto = :id
    ";


    $stmtBuscar =
        $conexion->prepare($sqlBuscar);

    $stmtBuscar->execute([
        ':id' => $idProducto
    ]);

    $producto =
        $stmtBuscar->fetch(PDO::FETCH_ASSOC);


    if (!$producto) {
        die('El producto no existe.');
    }


    /* =================================================
       ELIMINAR DE LA BASE DE DATOS
    ================================================= */

    $sqlEliminar = "
        DELETE FROM productos
        WHERE id_producto = :id
    ";


    try {

        $stmtEliminar =
            $conexion->prepare($sqlEliminar);

        $stmtEliminar->execute([
            ':id' => $idProducto
        ]);

    } catch (PDOException $e) {

        die(
            'No se pudo eliminar el producto. '
            . 'Es posible que esté relacionado '
            . 'con algún pedido.'
        );
    }


    /* =================================================
       ELIMINAR IMAGEN DEL PRODUCTO
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
        $_FILES['imagen']['error']
            !== UPLOAD_ERR_OK
    ) {

        die(
            'Debes seleccionar una imagen válida.'
        );
    }


    $archivoTemporal =
        $_FILES['imagen']['tmp_name'];

    $nombreOriginal =
        $_FILES['imagen']['name'];


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

        die(
            'El formato de imagen no está permitido.'
        );
    }


    /* =================================================
       CARPETA SEGÚN CATEGORÍA
    ================================================= */

    switch ($idCategoria) {

        case 1:
            $carpetaCategoria =
                'Bebidas-calientes';
            break;

        case 2:
            $carpetaCategoria =
                'Bebidas-frias';
            break;

        case 3:
            $carpetaCategoria =
                'Postres';
            break;

        default:
            die('Categoría no válida.');
    }


    /* =================================================
       NOMBRE ÚNICO
    ================================================= */

    $nombreImagen =
        uniqid('producto_')
        . '.'
        . $extension;


    /* =================================================
       RUTA DONDE SE GUARDARÁ
    ================================================= */

    $carpetaDestino =
        '../img/productos/'
        . $carpetaCategoria
        . '/';


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


    /* =================================================
       GUARDAR IMAGEN
    ================================================= */

    if (
        !move_uploaded_file(
            $archivoTemporal,
            $rutaDestino
        )
    ) {

        die(
            'No se pudo guardar la imagen.'
        );
    }


    /*
     * Esto se guarda en imagen_url.
     *
     * Ejemplo:
     * Bebidas-calientes/producto_123.jpeg
     */
    return
        $carpetaCategoria
        . '/'
        . $nombreImagen;
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
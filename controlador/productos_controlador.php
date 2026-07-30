<?php require_once '../config/conexion.php';
/* =====================================================
   SOLO PERMITIR PETICIONES POST
===================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_panel/productos.php');
    exit; }
/* =====================================================
   DETERMINAR ACCIÓN
===================================================== */

$accion = $_POST['accion'] ?? '';

try {
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
            throw new Exception('La acción seleccionada no es válida.');
    }
} catch (Throwable $error) {
    exit(
        'Error al procesar el producto: ' .
        htmlspecialchars(
            $error->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}

/* =====================================================
   CREAR PRODUCTO
===================================================== */

function crearProducto(PDO $conexion): void
{
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCategoria = (int) ($_POST['categoria'] ?? 0);
    $precio = (float) ($_POST['precio'] ?? 0);

    $tienePromocion =
        isset($_POST['promocion']) ? 1 : 0;

    $etiquetaPromo =
        trim($_POST['etiqueta_promo'] ?? '');

    $precioDescuento =
        obtenerPrecioDescuento(
            $_POST['precio_descuento'] ?? ''
        );

    function validarProducto(
    PDO $conexion,
    string $nombre,
    int $idCategoria,
    float $precio,
    int $tienePromocion,
    string $etiquetaPromo,
    ?float $precioDescuento
): void {
    if ($nombre === '') {
        throw new Exception(
            'El nombre del producto es obligatorio.'
        );
    }

    if ($idCategoria <= 0) {
        throw new Exception(
            'Selecciona una categoría válida.'
        );
    }

    /*
     * Verificar que la categoría exista realmente
     * en la tabla categorias.
     */
    $sqlCategoria = "
        SELECT COUNT(*)
        FROM categorias
        WHERE id_categoria = :id_categoria
    ";

    $stmtCategoria =
        $conexion->prepare($sqlCategoria);

    $stmtCategoria->execute([
        ':id_categoria' => $idCategoria
    ]);

    $categoriaExiste =
        (int) $stmtCategoria->fetchColumn();

    if ($categoriaExiste === 0) {
        throw new Exception(
            'La categoría seleccionada no existe en la base de datos.'
        );
    }

    if ($precio < 0) {
        throw new Exception(
            'El precio no puede ser negativo.'
        );
    }

    if ($tienePromocion === 1) {
        if ($etiquetaPromo === '') {
            throw new Exception(
                'Escribe una etiqueta para la promoción.'
            );
        }

        if ($precioDescuento === null) {
            throw new Exception(
                'Escribe el precio con descuento.'
            );
        }

        if ($precioDescuento < 0) {
            throw new Exception(
                'El precio con descuento no puede ser negativo.'
            );
        }

        if ($precioDescuento >= $precio) {
            throw new Exception(
                'El precio con descuento debe ser menor al precio normal.'
            );
        }
    }
}

    if (
        !isset($_FILES['imagen']) ||
        $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        throw new Exception(
            'Debes seleccionar una imagen para el producto.'
        );
    }

    $imagenUrl = guardarImagenProducto(
        $conexion,
        $_FILES['imagen'],
        $nombre,
        $idCategoria
    );
    try {
        $sql = "
            INSERT INTO productos (
                nombre,
                descripcion,
                id_categoria,
                precio,
                imagen_url,
                tiene_promocion,
                etiqueta_promo,
                precio_descuento
            )
            VALUES (
                :nombre,
                :descripcion,
                :id_categoria,
                :precio,
                :imagen_url,
                :tiene_promocion,
                :etiqueta_promo,
                :precio_descuento
            )
        ";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' =>
                $descripcion !== '' ? $descripcion : null,
            ':id_categoria' => $idCategoria,
            ':precio' => $precio,
            ':imagen_url' => $imagenUrl,
            ':tiene_promocion' => $tienePromocion,
            ':etiqueta_promo' =>
                $tienePromocion === 1
                    ? $etiquetaPromo
                    : null,
            ':precio_descuento' =>
                $tienePromocion === 1
                    ? $precioDescuento
                    : null
        ]);
    } catch (PDOException $error) {
        /*
         * Si la imagen se guardó, pero el INSERT falló,
         * se elimina para no dejar archivos sobrantes.
         */
        eliminarImagen($imagenUrl);

        throw new Exception(
            'No se pudo guardar el producto: ' .
            $error->getMessage()
        );}

    header(
        'Location: ../admin_panel/productos.php?creado=1'
    );
    exit;}

/* =====================================================
   MODIFICAR PRODUCTO
===================================================== */

function modificarProducto(PDO $conexion): void
{
    $idProducto =
        (int) ($_POST['id_producto'] ?? 0);

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCategoria = (int) ($_POST['categoria'] ?? 0);
    $precio = (float) ($_POST['precio'] ?? 0);

    $tienePromocion =
        isset($_POST['promocion']) ? 1 : 0;

    $etiquetaPromo =
        trim($_POST['etiqueta_promo'] ?? '');

    $precioDescuento =
        obtenerPrecioDescuento(
            $_POST['precio_descuento'] ?? ''
        );

    if ($idProducto <= 0) {
        throw new Exception('El producto seleccionado no es válido.');}
    validarProducto(
        $conexion,
        $nombre,
        $idCategoria,
        $precio,
        $tienePromocion,
        $etiquetaPromo,
        $precioDescuento
    );

    /* Buscar la información actual del producto */

    $sqlActual = "
        SELECT
            id_producto,
            imagen_url
        FROM productos
        WHERE id_producto = :id_producto
    ";

    $stmtActual = $conexion->prepare($sqlActual);

    $stmtActual->execute([
        ':id_producto' => $idProducto
    ]);

    $productoActual =
        $stmtActual->fetch(PDO::FETCH_ASSOC);
    if (!$productoActual) {
        throw new Exception(
            'El producto que intentas modificar no existe.'
        );
    }
    $imagenAnterior =
    $productoActual['imagen_url'];

    $imagenUrl = $imagenAnterior;
    $hayNuevaImagen = false;

    /*
     * La imagen no es obligatoria al modificar.
     * Si no se selecciona una nueva, se conserva la anterior.
     */

    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $imagenUrl = guardarImagenProducto(
            $conexion,
            $_FILES['imagen'],
            $nombre,
            $idCategoria
        );

        $hayNuevaImagen = true;
    }

    try {
        $sql = "
            UPDATE productos
            SET
                nombre = :nombre,
                descripcion = :descripcion,
                id_categoria = :id_categoria,
                precio = :precio,
                imagen_url = :imagen_url,
                tiene_promocion = :tiene_promocion,
                etiqueta_promo = :etiqueta_promo,
                precio_descuento = :precio_descuento
            WHERE id_producto = :id_producto
        ";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' =>
                $descripcion !== '' ? $descripcion : null,
            ':id_categoria' => $idCategoria,
            ':precio' => $precio,
            ':imagen_url' => $imagenUrl,
            ':tiene_promocion' => $tienePromocion,
            ':etiqueta_promo' =>
                $tienePromocion === 1
                    ? $etiquetaPromo
                    : null,
            ':precio_descuento' =>
                $tienePromocion === 1
                    ? $precioDescuento
                    : null,
            ':id_producto' => $idProducto
        ]);
    } catch (PDOException $error) {
        /*
         * Si se subió una imagen nueva, pero falló
         * el UPDATE, se elimina la imagen nueva.
         */
        if ($hayNuevaImagen) {
            eliminarImagen($imagenUrl);
        }

        throw new Exception(
            'No se pudo modificar el producto: ' .
            $error->getMessage()
        );
    }

    /*
     * Si la modificación fue correcta y se subió
     * una nueva imagen, se borra la imagen anterior.
     */

    if (
        $hayNuevaImagen &&
        !empty($imagenAnterior) &&
        $imagenAnterior !== $imagenUrl
    ) {
        eliminarImagen($imagenAnterior);
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
        throw new Exception(
            'El producto seleccionado no es válido.'
        );
    }

    /* Buscar el producto antes de eliminarlo */

    $sqlBuscar = "
        SELECT
            id_producto,
            imagen_url
        FROM productos
        WHERE id_producto = :id_producto
    ";

    $stmtBuscar = $conexion->prepare($sqlBuscar);

    $stmtBuscar->execute([
        ':id_producto' => $idProducto
    ]);

    $producto =
        $stmtBuscar->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        throw new Exception(
            'El producto que intentas eliminar no existe.'
        );
    }

    try {
        $sqlEliminar = "
            DELETE FROM productos
            WHERE id_producto = :id_producto
        ";

        $stmtEliminar =
            $conexion->prepare($sqlEliminar);

        $stmtEliminar->execute([
            ':id_producto' => $idProducto
        ]);
    } catch (PDOException $error) {
        throw new Exception(
            'No se pudo eliminar el producto. ' .
            'Es posible que esté relacionado con un pedido.'
        );
    }

    /*
     * La imagen se elimina únicamente después
     * de borrar correctamente el producto.
     */

    if (!empty($producto['imagen_url'])) {
        eliminarImagen(
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


    /*
     * Tu sistema utiliza:
     *
     * 1 = Bebidas calientes
     * 2 = Bebidas frías
     * 3 = Postres
     */

    if (!in_array($idCategoria, [1, 2, 3], true)) {
        throw new Exception(
            'Selecciona una categoría válida.'
        );
    }

    if ($precio < 0) {
        throw new Exception(
            'El precio no puede ser negativo.'
        );
    }

    if ($tienePromocion === 1) {
        if ($etiquetaPromo === '') {
            throw new Exception(
                'Escribe una etiqueta para la promoción.'
            );
        }

        if ($precioDescuento === null) {
            throw new Exception(
                'Escribe el precio con descuento.'
            );
        }

        if ($precioDescuento < 0) {
            throw new Exception(
                'El precio con descuento no puede ser negativo.'
            );
        }

        if ($precioDescuento >= $precio) {
            throw new Exception(
                'El precio con descuento debe ser menor al precio normal.'
            );
        }
    }

/* =====================================================
   CONVERTIR PRECIO DE DESCUENTO
===================================================== */

function obtenerPrecioDescuento(
    mixed $valor
): ?float {
    if (
        $valor === '' ||
        $valor === null
    ) {
        return null;
    }

    return (float) $valor;
}

/* =====================================================
   GUARDAR IMAGEN DEL PRODUCTO
===================================================== */

function guardarImagenProducto(
    PDO $conexion,
    array $archivo,
    string $nombreProducto,
    int $idCategoria
): string {
    $sqlCategoria = "
    SELECT nombre
    FROM categorias
    WHERE id_categoria = :id_categoria
";

$stmtCategoria = $conexion->prepare($sqlCategoria);

$stmtCategoria->execute([
    ':id_categoria' => $idCategoria
]);

$nombreCategoria = $stmtCategoria->fetchColumn();

if (!$nombreCategoria) {
    throw new Exception(
        'La categoría seleccionada no existe.'
    );
}

$nombreNormalizado =
    limpiarNombreArchivo($nombreCategoria);

switch ($nombreNormalizado) {
    case 'bebidas-calientes':
        $carpetaCategoria = 'Bebidas-calientes';
        break;

    case 'bebidas-frias':
        $carpetaCategoria = 'Bebidas-frias';
        break;

    case 'postres':
        $carpetaCategoria = 'Postres';
        break;

    default:
        throw new Exception(
            'La categoría no tiene una carpeta configurada.'
        );
}

    if (
        !isset($archivo['error']) ||
        $archivo['error'] !== UPLOAD_ERR_OK
    ) {
        throw new Exception(
            'No se recibió correctamente la imagen.'
        );
    }

    /*
     * Tamaño máximo permitido: 5 MB.
     */

    $tamanoMaximo =
        5 * 1024 * 1024;

    if ($archivo['size'] > $tamanoMaximo) {
        throw new Exception(
            'La imagen no debe superar los 5 MB.'
        );
    }

    /*
     * Comprobar el tipo real del archivo.
     */

    $finfo =
        new finfo(FILEINFO_MIME_TYPE);

    $tipoMime =
        $finfo->file($archivo['tmp_name']);

    $extensionesPermitidas = [
        'image/jpeg' => 'jpeg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($extensionesPermitidas[$tipoMime])) {
        throw new Exception(
            'La imagen debe ser JPEG, PNG o WEBP.'
        );
    }

    $extension =
        $extensionesPermitidas[$tipoMime];

    /*
     * Ejemplo:
     *
     * Cappuccino Clásico
     *       ↓
     * cappuccino-clasico
     */

    $nombreLimpio =
        limpiarNombreArchivo($nombreProducto);

    if ($nombreLimpio === '') {
        throw new Exception(
            'No fue posible crear el nombre de la imagen.'
        );
    }

    /*
     * Ruta física:
     *
     * proyecto/img/productos/Bebidas-calientes/
     */

    $directorioDestino =
        dirname(__DIR__) .
        '/img/productos/' .
        $carpetaCategoria .
        '/';

    if (!is_dir($directorioDestino)) {
        $carpetaCreada = mkdir(
            $directorioDestino,
            0775,
            true
        );

        if (!$carpetaCreada) {
            throw new Exception(
                'No fue posible crear la carpeta de imágenes.'
            );
        }
    }

    $nombreArchivo =
        $nombreLimpio . '.' . $extension;

    $rutaCompleta =
        $directorioDestino . $nombreArchivo;

    /*
     * Si el nombre ya existe:
     *
     * cappuccino-clasico.jpeg
     * cappuccino-clasico-2.jpeg
     * cappuccino-clasico-3.jpeg
     */

    $contador = 2;

    while (file_exists($rutaCompleta)) {
        $nombreArchivo =
            $nombreLimpio .
            '-' .
            $contador .
            '.' .
            $extension;

        $rutaCompleta =
            $directorioDestino .
            $nombreArchivo;

        $contador++;
    }

    if (
        !move_uploaded_file(
            $archivo['tmp_name'],
            $rutaCompleta
        )
    ) {
        throw new Exception(
            'No fue posible guardar la imagen.'
        );
    }

    /*
     * Este valor se almacena en imagen_url:
     *
     * Bebidas-calientes/cappuccino-clasico.jpeg
     */

    return
        $carpetaCategoria .
        '/' .
        $nombreArchivo;
}

/* =====================================================
   LIMPIAR NOMBRE DEL ARCHIVO
===================================================== */

function limpiarNombreArchivo(
    string $nombre
): string {
    $nombreConvertido = iconv(
        'UTF-8',
        'ASCII//TRANSLIT//IGNORE',
        $nombre
    );

    if ($nombreConvertido !== false) {
        $nombre = $nombreConvertido;
    }

    $nombre = strtolower($nombre);

    $nombre = preg_replace(
        '/[^a-z0-9]+/',
        '-',
        $nombre
    );

    return trim($nombre, '-');
}

/* =====================================================
   ELIMINAR IMAGEN
===================================================== */

function eliminarImagen(
    ?string $imagen
): void {
    if (empty($imagen)) {
        return;
    }

    $rutaImagen =
        dirname(__DIR__) .
        '/img/productos/' .
        $imagen;

    if (is_file($rutaImagen)) {
        unlink($rutaImagen);
    }
}
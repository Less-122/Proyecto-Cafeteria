<?php

require_once '../../../conexion.php';

/* Solo permitir acceso mediante POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../productos.php');
    exit;
}


/* =========================
   RECIBIR DATOS
========================= */

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

$id_categoria = (int) ($_POST['categoria'] ?? 0);

$stock = (int) ($_POST['stock'] ?? 0);
$precio = (float) ($_POST['precio'] ?? 0);

$tiene_promocion = isset($_POST['promocion']) ? 1 : 0;


/* =========================
   VALIDACIONES
========================= */

if ($nombre === '') {
    die('El nombre es obligatorio.');
}

if ($id_categoria <= 0) {
    die('Selecciona una categoría válida.');
}

if ($stock < 0) {
    die('El stock no puede ser negativo.');
}

if ($precio < 0) {
    die('El precio no puede ser negativo.');
}


/* =========================
   PROCESAR IMAGEN
========================= */

$imagen_url = null;

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] === UPLOAD_ERR_OK
) {

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

    if (!in_array($extension, $extensionesPermitidas, true)) {
        die('El formato de imagen no está permitido.');
    }

    /* Crear nombre único */
    $imagen_url = uniqid('producto_', true) . '.' . $extension;

    /* Carpeta donde se guardarán */
    $carpetaDestino = '../../../img/productos/';

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $rutaDestino = $carpetaDestino . $imagen_url;

    if (!move_uploaded_file($archivoTemporal, $rutaDestino)) {
        die('No se pudo guardar la imagen.');
    }
}


/* =========================
   GUARDAR EN BASE DE DATOS
========================= */

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
    ':categoria' => $id_categoria,
    ':precio' => $precio,
    ':stock' => $stock,
    ':imagen_url' => $imagen_url,
    ':promocion' => $tiene_promocion
]);


/* =========================
   REGRESAR A PRODUCTOS
========================= */

header('Location: ../../productos.php?creado=1');
exit;
?>
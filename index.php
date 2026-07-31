<?php 
session_start();

require_once 'config/conexion.php';

/* Obtener categorías disponibles */
$sqlCategorias = "
    SELECT
        id_categoria,
        nombre,
        descripcion
    FROM categorias
    ORDER BY id_categoria ASC
";
$categoriasResultado = $conexion->query($sqlCategorias);
$categorias = $categoriasResultado ? $categoriasResultado->fetchAll(PDO::FETCH_ASSOC) : [];

/* Obtener productos para agruparlos por categoría y promociones */
$sqlProductos = "
    SELECT
        p.id_producto,
        p.nombre,
        p.descripcion,
        p.id_categoria,
        p.precio,
        p.imagen_url,
        p.tiene_promocion,
        p.etiqueta_promo,
        p.precio_descuento
    FROM productos AS p
    ORDER BY p.id_categoria ASC, p.id_producto ASC
";
$productosResultado = $conexion->query($sqlProductos);

/* Separar productos por categoría */
$productosPorCategoria = [];
$productosPromocion = [];

if ($productosResultado) {
    while ($producto = $productosResultado->fetch(PDO::FETCH_ASSOC)) {
        $idCategoria = (int) $producto['id_categoria'];
        $productosPorCategoria[$idCategoria][] = $producto;

        if (!empty($producto['tiene_promocion']) && !empty($producto['precio_descuento'])) {
            $productosPromocion[] = $producto;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aroma a Cafe</title>
    <link rel="stylesheet" href="css/header-menu.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="icon" type="image/jpeg" href="img/Logo/isotipo.jpg">
</head>


<body>
    <?php include("includes/header-menu.php"); ?>
    <section id="inicio" class="hero"> <!-- Bienvenida y descripcion-->
        <div class="container hero-content">
            <div class="hero-txt">
                <h1>Bienvenid@<br><span>Un rincón con aroma a hogar y sabor a café.</span></h1>
                <p>En Aroma a Café, creemos que una taza de café es mucho más que una bebida para empezar el día; es la excusa perfecta para pausar el tiempo, compartir una buena plática o disfrutar de un momento a solas.

Nos apasiona recibirte con el olor a grano recién molido y pan calientito saliendo del horno. Cada una de nuestras tazas está preparada con paciencia, dedicación y el cariño de quienes aman lo que hacen, buscando recordarte ese sabor casero que reconforta el alma.

</p>
             
            </div>
            <div class="hero-img">
                <img id="PromoImagen" src="img/productos/Postres/producto_6a658017028190.67328164.jpeg" alt="Delicias de café y helado" alt="Promociones">
            </div>
        </div>
    </section>
<!-- APARTADO DE PROMOCIONES-->
    <section id="promociones" class="categoria-seccion container">
        <h2>Promociones</h2>
        <p class="subtitulo">Ofertas por tiempo limitado</p>

        <div class="carousel-wrapper">
            <button class="carousel-btn" id="btn-prev">❮</button>
            <div class="carrusel-container" id="promos-carousel">
                <?php if (!empty($productosPromocion)): ?>
                    <?php foreach ($productosPromocion as $producto): ?>
                        <?php $precioFinalPromocion = !empty($producto['precio_descuento']) ? (float) $producto['precio_descuento'] : (float) $producto['precio']; ?>
                        <div class="box">
                            <div class="tag-oferta"><?= htmlspecialchars(!empty($producto['etiqueta_promo']) ? $producto['etiqueta_promo'] : 'Promoción', ENT_QUOTES, 'UTF-8') ?></div>
                            <?php if (!empty($producto['imagen_url'])): ?>
                                <img src="img/productos/<?= htmlspecialchars($producto['imagen_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                            <div class="product-txt">
                                <h3><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="producto-footer">
                                    <span class="precio-anterior">$<?= number_format((float) $producto['precio'], 2) ?></span>
                                    <span class="precio-promo">$<?= number_format($precioFinalPromocion, 2) ?></span>
                                    <button
                                        class="btn-agCarrito"
                                        data-id="<?= (int) $producto['id_producto'] ?>"
                                        data-nombre="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-precio="<?= $precioFinalPromocion ?>"
                                        data-imagen="<?= htmlspecialchars($producto['imagen_url'], ENT_QUOTES, 'UTF-8') ?>"
                                    >Agregar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos en promoción disponibles.</p>
                <?php endif; ?>
            </div>
            <button class="carousel-btn" id="btn-next">❯</button>
        </div>
    </section>

    <!-- SECCIONES DINÁMICAS POR CATEGORÍA -->
    <?php foreach ($categorias as $categoria): ?>
        <?php
            $idCategoria = (int) $categoria['id_categoria'];
            $productosCategoria = $productosPorCategoria[$idCategoria] ?? [];
        ?>

        <section id="categoria-<?= $idCategoria ?>" class="categoria-seccion container">
            <h2><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></h2>

            <?php if (!empty($categoria['descripcion'])): ?>
                <p class="subtitulo">
                    <?= htmlspecialchars($categoria['descripcion'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <div class="box-container limit-grid">
                <?php if (!empty($productosCategoria)): ?>
                    <?php $contador = 0;
                    foreach ($productosCategoria as $producto):
                        $contador++;
                        $claseProducto = $contador <= 4 ? 'product-item' : 'product-item-extra';
                    ?>
                    <div class="box <?= $claseProducto ?>">
                        <?php if (!empty($producto['imagen_url'])): ?>
                            <img
                                src="img/productos/<?= htmlspecialchars($producto['imagen_url'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                            >
                        <?php endif; ?>

                        <div class="product-txt">
                            <h3><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>

                            <div class="producto-footer">
                                <?php if (!empty($producto['tiene_promocion']) && !empty($producto['precio_descuento'])): ?>
                                    <span class="precio-anterior">
                                        $<?= number_format((float) $producto['precio'], 2) ?>
                                    </span>
                                    <span class="precio-promo">
                                        $<?= number_format((float) $producto['precio_descuento'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="precio">
                                        $<?= number_format((float) $producto['precio'], 2) ?>
                                    </span>
                                <?php endif; ?>

                                <?php $precioRealProducto = !empty($producto['tiene_promocion']) && !empty($producto['precio_descuento']) ? (float) $producto['precio_descuento'] : (float) $producto['precio']; ?>
                                <button
                                    class="btn-agCarrito"
                                    data-id="<?= (int) $producto['id_producto'] ?>"
                                    data-nombre="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-precio="<?= $precioRealProducto ?>"
                                    data-imagen="<?= htmlspecialchars($producto['imagen_url'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos disponibles en esta categoría.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($productosCategoria) && count($productosCategoria) > 4): ?>
                <div class="btn-container-center">
                    <button
                        id="btn-mas-<?= $idCategoria ?>"
                        class="btn-secundario btn-ver-mas"
                        data-target="categoria-<?= $idCategoria ?>"
                    >
                        Ver más productos
                    </button>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>

    <?php include("includes/footer.php"); ?>
    <script>
    // Le preguntamos a PHP si existe la variable de sesión.
    window.usuarioLogueado = <?php echo isset($_SESSION['id_usuario']) ? 'true' : 'false'; ?>;
    </script>
    <script src="js/carrito.js?v=2"></script>
    <script src="js/menu.js"></script> </body>
    <script src="js/header-menu.js"></script>
    
</html>    
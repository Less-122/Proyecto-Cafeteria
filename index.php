<?php 
session_start();

require_once 'config/conexion.php';

/* Obtener productos junto con su categoría */
$sql = "
    SELECT
        p.id_producto,
        p.nombre,
        p.descripcion,
        p.id_categoria,
        p.precio,
        p.imagen_url,
        p.tiene_promocion,
        p.etiqueta_promo,
        p.precio_descuento,
        c.nombre AS categoria
    FROM productos AS p
    INNER JOIN categorias AS c
        ON p.id_categoria = c.id_categoria
    ORDER BY p.id_producto ASC
";

$resultado = $conexion->query($sql);

/* Separar productos por categoría */
$productosPorCategoria = [];

if ($resultado) {

    while ($producto = $resultado->fetch(PDO::FETCH_ASSOC)) {

        $idCategoria = (int) $producto['id_categoria'];

        $productosPorCategoria[$idCategoria][] = $producto;
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
                <div class="box">
                    <div class="tag-oferta">Ahorra un 20%</div>
                    <img src="img/productos/Promociones/Desayuno-Amanecer.jpeg" alt="Promoción 1">
                    <div class="product-txt">

                        <h3>Desayuno al amanecer</h3>
                        <p>Primer sorbo + Delicia de zanahoria</p>
                    <div class="producto-footer">
                        <span class="precio-anterior">$124.00</span>
                        <span class="precio-promo">$99.20</span>
                        <button class="btn-agCarrito">Agregar</button>
                    </div>
                        
                    </div>
                </div>
                <div class="box">
                    <div class="tag-oferta">2x1</div>
                    <img src="img/productos/Promociones/Doble-felicidad.jpeg" alt="Promoción 2">
                    <div class="product-txt">
                        <h3>Doble felicidad</h3>
                        <p>Dos frappes de caramelo para disfrutar enpareja.</p>
                        <div class="producto-footer">
                            <span class="precio-anterior">$176.00</span>
                            <span class="precio-promo">$88.00</span>
                            <button class="btn-agCarrito">Agregar</button>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="tag-oferta">-15%</div>
                    <img src="img/productos/Promociones/Combo-dulce.jpeg" alt="Promoción 3">
                    <div class="product-txt">
                        <h3>Combo Dulce</h3>
                        <p>Momento dulce + Cielo de chocolate</p>
                        <div class="producto-footer">
                            <span class="precio-anterior">$156.00</span>
                            <span class="precio-promo">$132.60</span>
                            <button class="btn-agCarrito">Agregar</button>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="tag-oferta">Nuevo</div>
                    <img src="img/productos/Postres/producto_6a658017028190.67328164.jpeg" alt="Promoción 4">
                    <div class="product-txt">
                        <h3>Especial Sueño de chocolate</h3>
                        <p>Cremoso helado de chocolate elaborado con cacao de alta calidad, de textura <br>
                            suave y un sabor intenso que conquista desde la primera cucharada.</p>
                        <div class="producto-footer">
                            <span class="precio">$62.00</span>
                            <button class="btn-agCarrito">Agregar</button>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-btn" id="btn-next">❯</button>
        </div>
    </section>

    <!-- APARTADO DE bebidas calientes-->

    <section id="calientes" class="categoria-seccion container">
        <h2>Bebidas Calientes</h2>
        <p class="subtitulo">Tazas elaboradas con técnica experta</p>
        <div class="box-container limit-grid">
            <?php if (!empty($productosPorCategoria[1])): ?>
            <?php $contador = 0;
            foreach ($productosPorCategoria[1] as $producto):
                $contador++;
                $claseProducto =
                $contador <= 4
                ? 'product-item'
                : 'product-item-extra';
            ?>
            <div class="box <?= $claseProducto ?>">
                <?php if (!empty($producto['imagen_url'])): ?>
                    <img
                    src="img/productos/<?= htmlspecialchars(
                        $producto['imagen_url'],
                        ENT_QUOTES,
                        'UTF-8'
                ) ?>"
                alt="<?= htmlspecialchars(
                    $producto['nombre'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>" >
                <?php endif; ?>
                <div class="product-txt">
                    <h3>
                        <?= htmlspecialchars(
                            $producto['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $producto['descripcion'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <div class="producto-footer">

                            <?php if (
                                !empty($producto['tiene_promocion'])
                                &&
                                !empty($producto['precio_descuento'])
                            ): ?>

                                <span class="precio-anterior">
                                    $<?= number_format(
                                        (float) $producto['precio'],
                                        2
                                    ) ?>
                                </span>

                                <span class="precio-promo">
                                    $<?= number_format(
                                        (float) $producto['precio_descuento'],
                                        2
                                    ) ?>
                                </span>

                            <?php else: ?>

                                <span class="precio">
                                    $<?= number_format(
                                        (float) $producto['precio'],
                                        2
                                    ) ?>
                                </span>

                            <?php endif; ?>


                            <button
                                class="btn-agCarrito"
                                data-id="<?= (int) $producto['id_producto'] ?>"
                                data-nombre="<?= htmlspecialchars(
                                    $producto['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                data-precio="<?= (float) $producto['precio'] ?>"
                                data-imagen="<?= htmlspecialchars(
                                    $producto['imagen_url'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Agregar
                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>
            
            <p>No hay bebidas calientes disponibles.</p>

        <?php endif; ?>

    </div>


    <?php if (
        !empty($productosPorCategoria[1])
        && count($productosPorCategoria[1]) > 4
    ): ?>

        <div class="btn-container-center">

            <button
                id="btn-masBcalientes"
                class="btn-secundario"
            >
                Ver más productos
            </button>

        </div>

    <?php endif; ?>

</section>
 <!-- CATEGORIA DE BEBIDAS FRIAS-->
    <section id="frias" class="categoria-seccion container">
        <h2>Bebidas Frias</h2>
        <p class="subtitulo">Refrescantes y deliciosas</p>
        <div class="box-container limit-grid">
            <?php if (!empty($productosPorCategoria[2])): ?>
            <?php $contador = 0;
            foreach ($productosPorCategoria[2] as $producto):
                $contador++;
                $claseProducto =
                $contador <= 4
                ? 'product-item'
                : 'product-item-extra';
            ?>
            <div class="box <?= $claseProducto ?>">
                <?php if (!empty($producto['imagen_url'])): ?>
                    <img
                    src="img/productos/<?= htmlspecialchars(
                        $producto['imagen_url'],
                        ENT_QUOTES,
                        'UTF-8'
                ) ?>"
                alt="<?= htmlspecialchars(
                    $producto['nombre'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>" >
                <?php endif; ?>
                <div class="product-txt">
                    <h3>
                        <?= htmlspecialchars(
                            $producto['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $producto['descripcion'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <div class="producto-footer">

                            <?php if (
                                !empty($producto['tiene_promocion'])
                                &&
                                !empty($producto['precio_descuento'])
                            ): ?>

                                <span class="precio-anterior">
                                    $<?= number_format(
                                        (float) $producto['precio'],
                                        2
                                    ) ?>
                                </span>

                                <span class="precio-promo">
                                    $<?= number_format(
                                        (float) $producto['precio_descuento'],
                                        2
                                    ) ?>
                                </span>

                            <?php else: ?>

                                <span class="precio">
                                    $<?= number_format(
                                        (float) $producto['precio'],
                                        2
                                    ) ?>
                                </span>

                            <?php endif; ?>


                            <button
                                class="btn-agCarrito"
                                data-id="<?= (int) $producto['id_producto'] ?>"
                                data-nombre="<?= htmlspecialchars(
                                    $producto['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                data-precio="<?= (float) $producto['precio'] ?>"
                                data-imagen="<?= htmlspecialchars(
                                    $producto['imagen_url'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Agregar
                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>
            
            <p>No hay bebidas frias disponibles.</p>

        <?php endif; ?>

    </div>


    <?php if (
        !empty($productosPorCategoria[2])
        && count($productosPorCategoria[2]) > 4
    ): ?>

        <div class="btn-container-center">

            <button
                id="btn-masBFrias"
                class="btn-secundario"
            >
                Ver más productos
            </button>

        </div>

    <?php endif; ?>

</section>

<!-- CATEGORIA DE POSTRES -->
     <section id="postres" class="categoria-seccion container">
        <h2>Postres</h2>
        <p class="subtitulo">Deliciosos postres para endulzar tu día</p>
        <div class="box-container limit-grid">
            <?php if (!empty($productosPorCategoria[3])): ?>
            <?php $contador = 0;
            foreach ($productosPorCategoria[3] as $producto):
                $contador++;
                $claseProducto =
                $contador <= 4
                ? 'product-item'
                : 'product-item-extra';
            ?>
            <div class="box <?= $claseProducto ?>">
                <?php if (!empty($producto['imagen_url'])): ?>
                    <img
                    src="img/productos/<?= htmlspecialchars(
                        $producto['imagen_url'],
                        ENT_QUOTES,
                        'UTF-8'
                ) ?>"
                alt="<?= htmlspecialchars(
                    $producto['nombre'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>" >
                <?php endif; ?>
                <div class="product-txt">
                    <h3>
                        <?= htmlspecialchars(
                            $producto['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $producto['descripcion'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <div class="producto-footer">

                            <?php if (
                                !empty($producto['tiene_promocion'])
                                &&
                                !empty($producto['precio_descuento'])
                            ): ?>

                                <span class="precio-anterior">
                                    $<?= number_format(
                                        (float) $producto['precio'],
                                        2
                                    ) ?>
                                </span>

                                <span class="precio-promo">
                                    $<?= number_format(
                                        (float) $producto['precio_descuento'],
                                        2
                                    ) ?>
                                </span>

                            <?php else: ?>

                                <span class="precio">
                                    $<?= number_format(
                                        (float) $producto['precio'],
                                        2
                                    ) ?>
                                </span>

                            <?php endif; ?>


                            <button
                                class="btn-agCarrito"
                                data-id="<?= (int) $producto['id_producto'] ?>"
                                data-nombre="<?= htmlspecialchars(
                                    $producto['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                data-precio="<?= (float) $producto['precio'] ?>"
                                data-imagen="<?= htmlspecialchars(
                                    $producto['imagen_url'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Agregar
                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>
            
            <p>No hay postres disponibles.</p>

        <?php endif; ?>

    </div>


    <?php if (
        !empty($productosPorCategoria[3])
        && count($productosPorCategoria[3]) > 4
    ): ?>

        <div class="btn-container-center">

            <button
                id="btn-masPostres"
                class="btn-secundario"
            >
                Ver más productos
            </button>

        </div>

    <?php endif; ?>

</section>
    <?php include("includes/footer.php"); ?>
    <script>
    // Le preguntamos a PHP si existe la variable de sesión.
    window.usuarioLogueado = <?php echo isset($_SESSION['id_usuario']) ? 'true' : 'false'; ?>;
    </script>
    <script src="js/carrito.js?v=2"></script>
    <script src="js/menu.js"></script> </body>
    <script src="js/header-menu.js"></script>
    
</html>    
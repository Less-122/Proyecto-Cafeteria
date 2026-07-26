<?php 
session_start();

require_once 'config/conexion.php';

/* Obtener productos junto con su categoría */
$sql = "
    SELECT
        p.id_producto,
        p.nombre,
        p.descripcion,
        p.id_categoria_fk,
        p.precio,
        p.stock,
        p.imagen_url,
        p.tiene_promocion,
        p.etiqueta_promo,
        p.precio_descuento,
        c.nombre AS categoria
    FROM productos AS p
    INNER JOIN categorias AS c
        ON p.id_categoria_fk = c.id_categoria
    ORDER BY p.id_producto ASC
";

$resultado = $conexion->query($sql);

/* Separar productos por categoría */
$productosPorCategoria = [];

if ($resultado) {

    while ($producto = $resultado->fetch_assoc()) {

        $idCategoria = (int) $producto['id_categoria_fk'];

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
                <img id="PromoImagen" src="img/productos/Postres/sueño-chocolate.jpeg" alt="Delicias de café y helado" alt="Promociones">
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
                    <img src="img/productos/Postres/sueño-chocolate.jpeg" alt="Promoción 4">
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
        <h2>Bebidas Frías</h2>
        <p class="subtitulo">¡Especialmente para esta temporada de calor!</p>
        <div class="box-container limit-grid">

    <div class="box product-item">
        <img src="img/productos/Bebidas-frias/Bosque-Purpura.jpeg" alt="Bosque-Purpura">
        <div class="product-txt">
            <h3>Bosque purpura</h3>
            <p> Mezcla de frutos naturales con una textura ligera y un sabor fresco lleno de color.</p>
            <div class="producto-footer">
                <span class="precio">$55.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item">
        <img src="img/productos/Bebidas-frias/Brisa-fria.jpeg" alt="Brisa-fria">
        <div class="product-txt">
            <h3>Brisa Fría</h3>
            <p>Cold Brew. Café preparado lentamente en frío durante varias horas para lograr un sabor suave, refrescante y de baja acidez.</p>
            <div class="producto-footer">
                <span class="precio">$68.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item">
        <img src="img/productos/Bebidas-frias/Nube-helada.jpeg" alt="Nube Helada">
        <div class="product-txt">
            <h3>Nube Helada</h3>
            <p>Frappé de vainilla. Bebida cremosa con hielo triturado y un delicado sabor a vainilla que refresca cada momento. </p>
            <div class="producto-footer">
                <span class="precio">$75.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>


    <div class="box product-item">
        <img src="img/productos/Bebidas-frias/Moka-ice.jpeg" alt="Moka Ice">
        <div class="product-txt">
            <h3>Moka Ice</h3>
            <p>Frappé moka. Café, chocolate y hielo mezclados en una bebida cremosa ideal para los amantes del moka.</p>
            <div class="producto-footer">
                <span class="precio">$65.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Bebidas-frias/Dulce-felicidad.jpeg" alt="Dulce-felicidad">
        <div class="product-txt">
            <h3>Dulce Felicidad</h3>
            <p>Frappé de caramelo. Una combinación de café, leche y caramelo con una textura suave y un dulzor perfectamente equilibrado.</p>
            <div class="producto-footer">
                <span class="precio">$88.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Bebidas-frias/Tropica-lFresh.jpeg" alt="Tropical Fresh">
        <div class="product-txt">
            <h3>Tropical Fresh</h3>
            <p>Té helado de durazno. Té frío con un delicado toque de durazno que ofrece una bebida ligera y muy refrescante.</p>
            <div class="producto-footer">
                <span class="precio">$68.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Bebidas-frias/Espuma-Gelada.jpeg" alt="Espuma Helada">
        <div class="product-txt">
            <h3>Espuma Helada</h3>
            <p>Iced Latte. Espresso servido sobre hielo con leche fría, logrando una bebida cremosa y refrescante.</p>
            <div class="producto-footer">
                <span class="precio">$96.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Bebidas-frias/Cafe-nevado.jpeg" alt="Café Nevado">
        <div class="product-txt">
            <h3>Café Nevado</h3>
            <p>Café frío con helado. Café helado acompañado de una bola de helado que aporta suavidad y un toque especial en cada sorbo.</p>
            <div class="producto-footer">
                <span class="precio">$99.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

</div>
        <div class="btn-container-center">
            <button id="btn-masBFrias" class="btn-secundario">Ver más productos</button>
        </div>


    </section>

<!-- CATEGORIA DE POSTRES -->
    <section id="postres" class="categoria-seccion container">
        <h2>Postres</h2>
        <p class="subtitulo">¡Date un gusto de la vida!</p>
          <div class="box-container limit-grid">

    <div class="box product-item">
        <img src="img/productos/Postres/Dulce-tentacion.jpeg" alt="Dulce Tentación">
        <div class="product-txt">
            <h3>Dulce Tentación</h3>
            <p>Brownie de chocolate. Brownie recién horneado con un intenso sabor a chocolate y una textura suave por dentro.</p>
            <div class="producto-footer">
                <span class="precio">$78.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item">
        <img src="img/productos/Postres/Momento-Dulce.jpeg" alt="Momento Dulce">
        <div class="product-txt">
            <h3>Momento Dulce</h3>
            <p>Cheesecake de frutos rojos. Suave pastel de queso acompañado de una deliciosa salsa de frutos rojos que equilibra cada bocado.</p>
            <div class="producto-footer">
                <span class="precio">$69.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item">
        <img src="img/productos/Postres/Dulce-Zanahoria.jpeg" alt="Delicia de Zanahoria">
        <div class="product-txt">
            <h3>Delicia de Zanahoria</h3>
            <p>Pastel de zanahoria. Esponjoso pastel elaborado con zanahoria y especias, cubierto con un cremoso betún de queso.</p>
            <div class="producto-footer">
                <span class="precio">$65.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item">
        <img src="img/productos/Postres/Nuez-dorada.jpeg" alt="Nuez Dorada">
        <div class="product-txt">
            <h3>Nuez Dorada</h3>
            <p>Muffin de nuez. Muffin suave con trozos de nuez que aportan un ligero toque crujiente y un sabor casero.</p>
            <div class="producto-footer">
                <span class="precio">$77.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Postres/Deditos-dorados.png" alt="Deditos Dorados">
        <div class="product-txt">
            <h3>Deditos Dorados</h3>
            <p>Churros tradicionales. Delicados churros recién preparados, espolvoreados con azúcar y canela, perfectos para acompañar una taza de café o chocolate caliente.</p>
            <div class="producto-footer">
                <span class="precio">$48.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Postres/sueño-chocolate.jpeg" alt="Sueño de Chocolate">
        <div class="product-txt">
            <h3>Sueño de Chocolate</h3>
            <p>Helado de chocolate. Cremoso helado de chocolate elaborado con cacao de alta calidad, de textura suave y un sabor intenso que conquista desde la primera cucharada.</p>
            <div class="producto-footer">
                <span class="precio">$62.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Postres/Dulce recuerdo.jpeg" alt=" Dulce Recuerdo">
        <div class="product-txt">
            <h3>Dulce Recuerdo</h3>
            <p>Arroz con leche. Un postre tradicional preparado con arroz, leche y un toque de canela, cuya receta casera evoca los sabores y recuerdos de hogar.</p>
            <div class="producto-footer">
                <span class="precio">$55.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>

    <div class="box product-item-extra">
        <img src="img/productos/Postres/cielo-chocolate.jpeg" alt="Cielo de Chocolate">
        <div class="product-txt">
            <h3>Cielo de Chocolate</h3>
            <p>Pastel de chocolate. Capas de bizcocho de chocolate cubiertas con una cremosa ganache que conquista desde el primer bocado.</p>
            <div class="producto-footer">
                <span class="precio">$87.00</span>
                <button class="btn-agCarrito">Agregar</button>
            </div>
        </div>
    </div>
</div>

        
        <div class="btn-container-center">
            <button id="btn-masPostres" class="btn-secundario">Ver más productos</button>
        </div>
    </section>
    <?php include("includes/footer.php"); ?>
    <script>
    // Le preguntamos a PHP si existe la variable de sesión.
    window.usuarioLogueado = <?php echo isset($_SESSION['id_usuario']) ? 'true' : 'false'; ?>;
    </script>
    <script src="js/carrito.js?v=2"></script>
    <script src="js/menu.js"></script> </body>
</html>    
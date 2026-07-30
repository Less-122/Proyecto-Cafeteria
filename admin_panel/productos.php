<?php

include 'seguridad_admin.php';
require_once '../config/conexion.php';

$sql = "
    SELECT
        p.*,
        c.nombre AS categoria
    FROM productos AS p
    INNER JOIN categorias AS c
        ON p.id_categoria = c.id_categoria
    ORDER BY p.id_producto ASC
";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Obtener categorías para los formularios */
$sqlCategorias = "
    SELECT
        id_categoria,
        nombre
    FROM categorias
    ORDER BY nombre ASC
";

$stmtCategorias = $conexion->prepare($sqlCategorias);
$stmtCategorias->execute();

$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <!-- Rutas absolutas para los assets -->
    <link rel="stylesheet" href="/Proyecto-Cafeteria/css/admin.css">
    <link rel="icon" type="image/jpeg" href="/Proyecto-Cafeteria/img/Logo/isotipoAzul.jpeg">
</head>

<body>
    <!-- Inserción directa con PHP, eliminando los placeholders de JS -->
    <?php include 'admin_header.php'; ?>
    <?php include 'admin_menu.php'; ?>

    <main class="main_container">

        <h1 class="titulo">Gestión de Productos</h1>

        <section>

            <div class="filtros_container">

                <div class="search_box">
                    <ion-icon name="search-outline" class="icono_filtro"></ion-icon>
                    <input type="text" id="searchInput" placeholder="Buscar productos">
                </div>

                <div class="select_box">
                    <ion-icon name="pricetag-outline" class="icono_filtro"></ion-icon>
                    <select name="seleccion" id="selector" class="custom_select">
                        <option value="" selected>Todas las categorías</option>
                        <option value="BEBIDAS CALIENTES">Bebidas calientes</option>
                        <option value="BEBIDAS FRIAS">Bebidas frías</option>
                        <option value="POSTRES">Postres</option>
                    </select>
                </div>

            </div>

            <div>
                <button type="button" data-modal="modalAdd" class="btn-add">Añadir</button>
                <button type="button" class="btn-edit" id="btnModificar">Modificar</button>
                <button id="btnEliminarProducto" type="button" class="btn-delete">Borrar</button>
            </div>

        </section>

        <!-- TABLA DE PRODUCTOS -->
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Promoción</th>
                    <th>Etiqueta de promoción</th>
                    <th>Precio con descuento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="11">No hay productos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td>
                                <input 
                                    type="checkbox" 
                                    class="producto-check" 
                                    name="seleccion_producto" 
                                    value="<?= (int) $p['id_producto'] ?>"
                                    data-nombre="<?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-categoria="<?= (int) $p['id_categoria'] ?>"
                                    data-precio="<?= htmlspecialchars($p['precio'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-promocion="<?= (int) ($p['tiene_promocion'] ?? 0) ?>"
                                >
                            </td>
                            <td><?= (int) $p['id_producto'] ?></td>
                            <td><?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($p['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>$<?= number_format((float) $p['precio'], 2) ?></td>
                            <td>
                                <?php if (!empty($p['imagen_url'])): ?>
                                    <!-- Ruta absoluta corregida para la imagen -->
                                    <img src="/Proyecto-Cafeteria/img/productos/<?= htmlspecialchars($p['imagen_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>" width="70">
                                <?php else: ?>
                                    Sin imagen
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="checkbox" <?= !empty($p['tiene_promocion']) ? 'checked' : '' ?> disabled>
                            </td>
                            <td><?= htmlspecialchars($p['etiqueta_promo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php if ($p['precio_descuento'] !== null && $p['precio_descuento'] !== ''): ?>
                                    $<?= number_format((float) $p['precio_descuento'], 2) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </main>

    <!-- MODAL AÑADIR PRODUCTO -->
    <div id="modalAdd" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="modalAdd">&times;</span>
            <h2>Añadir nuevo producto</h2>
            <!-- Ruta absoluta en el form -->
            <form id="formAdd" action="/Proyecto-Cafeteria/controlador/productos_controlador.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"></textarea>

                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    <option value="" selected disabled> selecciona una categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>">
                            <?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <label for="precio">Precio:</label> 
                <input type="number" id="precio" name="precio" min="0" step="0.01" required>

                <label for="imagen">Imagen del producto:</label>
                <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>

                <label for="promocion">Promoción:</label>
                <input type="checkbox" id="promocion" name="promocion">

                <button type="submit">Guardar</button>
            </form>
        </div>
    </div>

    <!-- MODAL MODIFICAR PRODUCTO -->
    <div id="modalEdit" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="modalEdit">&times;</span>
            <h2>Modificar producto</h2>
            <!-- Ruta absoluta en el form -->
            <form id="formEdit" action="/Proyecto-Cafeteria/controlador/productos_controlador.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="modificar">
                <input type="hidden" id="editIdProducto" name="id_producto">

                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" name="nombre" required>

                <label for="editDescripcion">Descripción:</label>
                <textarea id="editDescripcion" name="descripcion"></textarea>

                <label for="editCategoria">Categoría:</label>
                <select id="editCategoria" name="categoria" required>
                    <option value="" selected disabled>Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>">
                            <?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <label for="editPrecio">Precio:</label>
                <input type="number" id="editPrecio" name="precio" min="0" step="0.01" required>

                <label for="editImagen">Nueva imagen:</label>
                <input type="file" id="editImagen" name="imagen" accept=".jpg,.jpeg,.png,.webp">

                <label for="editPromocion">Promoción:</label>
                <input type="checkbox" id="editPromocion" name="promocion">

                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>

    <!-- MODAL ELIMINAR PRODUCTO -->
    <div id="modalDeleteProducto" class="modal">
        <div class="modal-content text-center">
            <span class="close" data-modal="modalDeleteProducto">&times;</span>
            <h2>¿Eliminar producto?</h2>
            <br>
            <p>
                Esta acción no se puede deshacer.
                <br><br>
                ¿Estás seguro de que deseas eliminar el producto seleccionado?
            </p>
            <!-- Ruta absoluta en el form -->
            <form id="formEliminarProducto" action="/Proyecto-Cafeteria/controlador/productos_controlador.php" method="POST">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" id="deleteProdId" name="id_producto">
                <div class="modal-buttons">
                    <button type="button" class="btn-cancelar" onclick="document.getElementById('modalDeleteProducto').style.display = 'none'">Cancelar</button>
                    <button type="submit" id="btnConfirmarEliminarProducto" class="btn-danger">Confirmar eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MENSAJE DE CONFIRMACIÓN -->
    <div id="confirmation-overlay" class="overlay" style="display: none;">
        <div class="modal-confirm">
            <h3>LISTO</h3>
            <p>Los cambios se han guardado correctamente.</p>
        </div>
    </div>

    <!-- SCRIPTS corregidos con rutas absolutas -->
    <script src="/Proyecto-Cafeteria/js/admin.js"></script>
    <script src="/Proyecto-Cafeteria/js/productos.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</body>

</html>
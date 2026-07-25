<?php 
include 'seguridad_admin.php';
require_once '../usuario_panel/conexion.php';

$sql = "
    SELECT
        p.*,
        c.nombre AS categoria
    FROM productos p
    INNER JOIN categorias c
        ON p.id_categoria_fk = c.id_categoria
";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Productos</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="icon" type="image/jpeg" href="../img/Logo/isotipoAzul.jpeg">
</head>
<body>
    <div id="header-placeholder" class="header-placeholder"></div>

    <div id="menu-placeholder" class="menu-placeholder"></div>

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
                    <option value="" disabled selected>Todas las categorías</option>
                    <option value="Bebidas calientes">Bebidas calientes</option>
                    <option value="Bebidas frias">Bebidas frías</option>
                    <option value="Postres">Postres</option>
                </select>
            </div>
        </div>
        
<div>
    <button data-modal="modalAdd" class="btn-add">Añadir</button>
    <button data-modal="modalEdit" class="btn-edit">Modificar</button>
    <button data-modal="modalDeleteProducto" class="btn-delete">Borrar</button>
</div>
    </section>

    <table>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>img_url</th>
                <th>Promoción</th>
            </tr>
            <?php foreach($productos as $p): ?>
            <tr>
                <td><input type="checkbox" name="seleccion" value="<?=$p['id_producto'] ?>"></td>
                <td><?php echo $p['id_producto']; ?></td>
                <td><?php echo $p['nombre']; ?></td>
                <td><?php echo $p['descripcion']; ?></td>
                <td><?php echo $p['id_categoria_fk']; ?></td>
                <td><?php echo $p['stock']; ?></td>
                <td><?php echo $p['precio']; ?></td>
                <td><?php echo $p['imagen_url']; ?></td>
                <td><input type="checkbox" name="seleccion" value="prom"></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>

    <!-- Modal añadir -->
<div id="modalAdd" class="modal">
  <div class="modal-content">
    <span class="close" data-modal="modalAdd">&times;</span>
    <h2>Añadir nuevo producto</h2>
    <form id="formAdd" action="crud/productos/crear.php" method="POST" enctype="multipart/form-data">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" required>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion"></textarea>

      <label for="categoria">Categoría:</label>
      <select id="categoria" name="categoria" required class="modal-content">
                    <option value="1">bebidas calientes</option>
                    <option value="2">bebidas frías</option>
                    <option value="3">postres</option>
      </select>

      <label for="stock">Stock:</label>
      <input type="number" id="stock" name="stock" min="0" step="1" required>

      <label for="precio">Precio:</label>
      <input type="number" id="precio" name="precio" step="1" min="0" required>

      <label for="imagen">Imagen del producto</label>
      <input type="file" id="imagen" accept="image/*" name="imagen" required>

      <input type="checkbox" id="promocion" name="promocion">

      <button type="submit">Guardar</button>
    </form>
  </div>
</div>

<div id="modalEdit" class="modal">
        <div class="modal-content">
            <span class="close" data-modal="modalEdit">&times;</span>
            <h2>Modificar producto</h2>
            <form id="formEdit" action="modificar_producto.php" method="POST" enctype="multipart/form-data">
                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" name="nombre" required>

                <label for="editDescripcion">Descripción:</label>
                <textarea id="editDescripcion" name="descripcion"></textarea>

                <label for="editCategoria">Categoría:</label>
                <select id="editCategoria" name="categoria" required>
                    <option value="Bebida caliente">Bebida caliente</option>
                    <option value="Bebida fría">Bebida fría</option>
                    <option value="Postre">Postre</option>
                </select>

                <label for="editPrecio">Precio:</label>
                <input type="number" id="editPrecio" name="precio" step="1">

                <label for="editImagen">Imagen del producto</label>
                <input type="file" id="editImagen" accept="image/*" name="imagen">

                <label for="editPromocion">Promoción</label>
                <input type="checkbox" id="editPromocion" name="promocion">

                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>

<!-- Modal para eliminar producto -->
<div id="modalDeleteProducto" class="modal">
    <div class="modal-content text-center">
        <span class="close" data-modal="modalDeleteProducto">&times;</span>
        <h2>¿Eliminar Producto?</h2>
        <br>
        <p>Esta acción no se puede deshacer.<br><br>¿Estás seguro de que deseas eliminar el producto seleccionado?<br></p>
        <input type="hidden" id="deleteProdId">
        
        <div class="modal-buttons">
            <button type="button" class="btn-cancelar" onclick="document.getElementById('modalDeleteProducto').style.display='none'">Cancelar</button>
            <button type="button" id="btn-confirmar-eliminar" class="btn-danger">Confirmar Eliminar</button>
        </div>
    </div>
</div>
<div id="confirmation-overlay" class="overlay" style="display:none;">
    <div class="modal-confirm">
        <h3>LISTO</h3>
        <p><br>Los cambios se han guardado correctamente.</br></p>
      
    </div>
</div>

    <script src="../js/admin.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</body>
</html>
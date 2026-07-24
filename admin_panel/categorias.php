<!DOCTYPE html>
<html lang="es">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

    <div id="header-placeholder" class="header-placeholder"></div>

    <div id="menu-placeholder" class="menu-placeholder"></div>

    <main class="main_container">
        <h1 class="titulo">Gestión de Categorías</h1>
    <section>
        
        <div class="filtros_container">
            <div class="search_box">
                <ion-icon name="search-outline" class="icono_filtro"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar categoría">
            </div>
        </div>

        <div>
            <button data-modal="modalAddCategoria" class="btn-add">Añadir</button>
            <button class="btn-edit" data-modal="modalEditCategoria">Modificar</button>
           <button class="btn-delete" data-modal="modalDeleteCategoria">Borrar</button>
        
        </div>
    </section>

    <table>
        <tr>
            <th></th>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Cantidad de productos</th>
        </tr>
        <tr>
            <td><input type="checkbox" name="seleccion" value="1"></td>
            <td>001</td>
            <td>Bebidas calientes</td>
            <td>Lorem, ipsum dolor sit amet consectetur adipisicing elit.</td>
            <td>8</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="seleccion" value="1"></td>
            <td>002</td>
            <td>Bebidas frías</td>
            <td>Lorem, ipsum dolor sit amet consectetur adipisicing elit.</td>
            <td>9</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="seleccion" value="1"></td>
            <td>003</td>
            <td>Postres</td>
            <td>Lorem, ipsum dolor sit amet consectetur adipisicing elit.</td>
            <td>9</td>
        </tr>
    </table>

    <!-- Modal para añadir categoría -->
    <div id="modalAddCategoria" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="modalAddCategoria">&times;</span>
        <h2>Añadir Nueva Categoría</h2>
        <form id="formAddCategoria" action="guardar_categoria.php" method="POST">
        <label for="catNombre">Nombre:</label>
        <input type="text" id="catNombre" name="nombre" required>
        <label for="catDescripcion">Descripción:</label>
        <textarea id="catDescripcion" name="descripcion"></textarea>
        <button type="submit">Guardar</button>
        </form>
    </div>
    </div>

<div id="modalDeleteCategoria" class="modal">
    <div class="modal-content text-center">
        <span class="close" data-modal="modalDeleteCategoria">&times;</span>
        <h2>¿Eliminar Categoría?</h2>
        <br>
        <p>Esta acción no se puede deshacer. 
            <br><br>¿Estás seguro de que deseas eliminar la categoría seleccionada?<br>
        </p>
    </br>
        <input type="hidden" id="deleteCatId">
        
        <div class="modal-buttons">
            <button type="button" class="btn-cancelar" onclick="document.getElementById('modalDeleteCategoria').style.display='none'">Cancelar</button>
            <button type="button" id="btn-confirmar-eliminar" class="btn-danger">Confirmar Eliminar</button>
        </div>
    </div>
</div>

<div>
    <div id="modalEditCategoria" class="modal">
    <div class="modal-content">
        <span class="close" data-modal="modalEditCategoria">&times;</span>
        <h2>Modificar Categoría</h2>
        <form id="formEditCategoria">
            <input type="hidden" id="editCatId" name="id">
            
            <label for="editCatNombre">Nombre:</label>
            <input type="text" id="editCatNombre" name="nombre" required>
            
            <label for="editCatDescripcion">Descripción:</label>
            <textarea id="editCatDescripcion" name="descripcion"></textarea>
            
            <button type="submit" class="btn-guardar">Actualizar Cambios</button>
        </form>
    </div>
</div>
<div id="confirmation-overlay" class="overlay" style="display:none;">
    <div class="modal-confirm">
        <h3>LISTO</h3>
        <p><br>Los cambios se han guardado correctamente.</br></p>
      
    </div>
</div>



    </main>
    <script src="../js/admin.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</body>
</html>


